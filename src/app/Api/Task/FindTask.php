<?php
/**
 * User: kewin.cheng
 * Date: 2018/1/15
 * Time: 13:56
 */

namespace App\Api\Task;

use App\Common\Utils\Code;
use App\Common\Utils\Time;
use App\Domain\Common;
use App\Domain\Find\USER as DomainUSER;
use App\Domain\Find\RECORD as DomainRECORD;
use App\Domain\Find\FormRecord as DomainFormRECORD;
use App\Domain\Find\WalletWithdrawRecord as DomainWalletWithdrawRecord;
use App\WxCore\lib\WxPayApi;
use App\WxCore\lib\WxPayConfig;
use App\WxCore\lib\WxPayTransfers;
use App\WxCore\WxPayJsApi;
use PhalApi\Api;
use PhalApi\Exception;

/**
 * 找人计划任务
 * Class FindTask
 * @package App\Api\Task
 */
class FindTask extends Api{

    public function getRules(){

        return array(
            'sendModuleMsg' => array(
                'recordId' => array('name' => 'recordId', 'type' => 'int', 'require' => true , 'desc' => '找人记录id'),
            ),
            'collectFormId' => array(
                'openId' => array('name' => 'openId', 'type' => 'string', 'require' => true, 'desc' => 'openId'),
                'formId' => array('name' => 'formId', 'type' => 'string', 'require' => true , 'desc' => 'formId'),
            ),
            'collectReturnMoney' => array(
                'recordId' => array('name' => 'recordId', 'type' => 'int', 'require' => true , 'desc' => '找人记录id'),
            ),
            'returnMoney' => array(
                'recordId' => array('name' => 'recordId', 'type' => 'int', 'require' => true , 'desc' => '找人记录id'),
            ),
            'addReturnMoneyCrontab' => array(
                'recordId' => array('name' => 'recordId', 'type' => 'int', 'require' => true , 'desc' => '找人记录id'),
            ),
            'transfers' => array(
                'openId' => array('name' => 'openId', 'type' => 'string', 'desc' => 'openId'),
                'money' => array('name' => 'money', 'type' => 'int', 'min' => '1', 'desc' => '订单金额'),
                'secret' => array('name' => 'secret', 'type' => 'string', 'min' => '1', 'require' => true, 'desc' => '密钥'),
            )
        );
    }
    /**
     * 执行收集formId任务计划
     * @desc 执行任务计划
     */
    public function executeTask() {
        $ret = \PhalApi\DI()->taskRunnerLocal->go('App.Task_FindTask.collectFormId');
        return $ret;
    }

    /**
     * 执行发送模板消息任务计划
     * @desc 执行任务计划
     */
    public function executeSendModuleMsgTask() {
        $ret = \PhalApi\DI()->taskRunnerLocal->go('App.Task_FindTask.sendModuleMsg');
        return $ret;
    }

    /**
     * 执行更新记录状态和返还金额任务计划
     * @desc 执行任务计划
     */
    public function executeRecordTask() {
        $ret = \PhalApi\DI()->taskRunnerLocal->go('App.Task_FindTask.addReturnMoneyCrontab');
        return $ret;
    }

    /**
     * 添加返还金额的定时任务
     * @desc 添加返还金额的定时任务
     */
    public function addReturnMoneyCrontab() {

        $domainRecord = new DomainRECORD();
        $recordInfo = $domainRecord->get($this->recordId);

        $createTime = date('Y,m,d,H,i,s',(strtotime( $recordInfo['create_time']) + 24 * (Time::HOUR)));
        $time = explode(',', $createTime);

        $cronCommand = $time[4].' '.$time[3].' '.$time[2].' '.$time[1]." * curl -d 'recordId=" . $this->recordId  ."' https://". $_SERVER['HTTP_HOST']."?service=App.Task_FindTask.returnMoney >> /tmp/task.log\n";

        $cronFile = API_ROOT . "/runtime/log/". date('Ym') . "/crontab.log";
        $crontab_arr = array();
        $state_code = -1;

        file_put_contents($cronFile, $cronCommand, FILE_APPEND);

        exec('crontab '.$cronFile, $crontab_arr, $state_code);
        if($state_code == 0){
            \PhalApi\DI()->logger->info(__CLASS__.__FUNCTION__, "exec crontab执行成功 recordId:" . $this->recordId);
        }else{
            \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__, "exec crontab执行失败 recordId:" . $this->recordId , "createTime:" . $createTime);
        }

    }

    /**
     * 收集formId
     * @ignore
     * @desc 计划任务，在创建记录、引荐人和被引荐人提交数据时收集formId [crontab配置定时任务]
     * @use \PhalApi\DI()->taskLite->add('App.Task_FindTask.collectFormId', array('formId' => 'xxxx'));
     */
    public function collectFormId() {
        $domainFormRecord = new DomainFormRECORD();
        $data = array(
            'openId' => $this->openId,
            'formId' => $this->formId
        );

        $ret = $domainFormRecord->insert($data);
        if(!$ret){
            \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__, "收集formId失败 formId:". $this->formId);
        }

    }

    /**
     * 收集returnMoney redis 列表数据
     * @ignore
     * @desc 计划任务 收集returnMoney redis 列表数据
     * @use \PhalApi\DI()->taskLite->add('App.Task_FindTask.returnMoney', array('recordId' => $this->recordId));
     */
    public function collectReturnMoney() {
        \PhalApi\DI()->taskLite->add('App.Task_FindTask.returnMoney', array('recordId' => $this->recordId));
    }

    /**
     * 红包返还
     * @desc 发起记录24小时内未被完成，设置发起状态为过期，同时返还红包到发起人账户
     */
    public function returnMoney() {

        $domainRecord = new DomainRECORD();
        $recordInfo = $domainRecord->get($this->recordId);

        $state = 1;

        \PhalApi\DI()->logger->info(__CLASS__.__FUNCTION__, "返还红包金额到发起人账户任务计划 recordId:" . $recordInfo['id']);
        //过期失效更新下状态
        if(( $recordInfo['oper_state'] == 1 ) && ((time() - strtotime($recordInfo['create_time']))>= (Time::DAY - 600))){
            $flag = $domainRecord->upate($this->recordId, array('oper_state' => 2));
            if($flag) {
                $state = 2;
            }else{
                \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__, "更新记录状态失败 recordId:" . $this->recordId);
            }
        }

        if( ( $state == 2 || $recordInfo['oper_state'] == 2 ) && ((time() - strtotime($recordInfo['create_time'])) >= (Time::DAY - 600))) {
            //返还红包金额到发起人账户内
            $domainUser = new DomainUSER();
            $updateFlag = $domainUser->updateWallet($recordInfo['openId'], $recordInfo['money'], 1);
            if(!$updateFlag){
                \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__, "返还红包金额到发起人账户内失败 openId:". $this->openId . " recordId:" . $this->recordId);
            }
        }

    }

    /**
     * 企业付款给用户
     * @desc 企业付款给用户
     * @param $transaction_id
     * @return string
     * @throws \App\WxCore\lib\WxPayException
     */
    public function transfers()
    {
        if( $this->secret != 'kewin.cheng') {
            throw new Exception("禁止访问!", 403);
        }

        //手动打款，输入用户openid和金额打款
        if( isset($this->openId, $this->money) && !empty($this->openId) && !empty($this->money) ) {
            $this->WxPayTransfer($this->openId, $this->money);
        }else {
            $domainWalletWithdrawRecord = new DomainWalletWithdrawRecord();
            $records = $domainWalletWithdrawRecord->getRecordByState(1);

            foreach ($records as $r => $v) {
                $ret = $this->WxPayTransfer($v['openId'], $v['money']);
                if($ret) {
                    $state = $domainWalletWithdrawRecord->upate($v['id'], array('state' => 2));
                    if(!$state) {
                        \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__, "企业付款给用户成功，但更新记录状态失败 记录id:". $v['id'] . " openId:" . $v['openId']);
                    }
                }else {
                    \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__, "企业付款给用户失败 记录id:". $v['id'] . " openId:" . $v['openId']);
                }
            }
        }

    }

    private function WxPayTransfer($openId, $amount) {
        $data = array(
            'amount' => $amount * 100, //转换为分
            'openid' => $openId,
            'partner_trade_no' => WxPayConfig::MCHID . date("YmdHis") . rand(1000, 9999),
            'create_time' => date("YmdHis")
        );

        //退款操作
        $input = new WxPayTransfers();
        $input->SetPartnerTradeNo($data['partner_trade_no']); //商户订单号
        $input->SetOpenid($data['openid']);
        $input->SetCheckName("NO_CHECK");
        $input->SetAmount($data['amount']);
        $input->SetDesc("钱包提现");

        $refund = WxPayApi::transfers($input);

        \PhalApi\DI()->logger->info('transfers:' . json_encode($refund));
        return true;
    }

}






