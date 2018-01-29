<?php
/**
 * User: kewin.cheng
 * Date: 2018/1/15
 * Time: 13:56
 */

namespace App\Api\Task;

use App\Common\Utils\Code;
use App\Common\Utils\Time;
use App\Domain\Find\USER as DomainUSER;
use App\Domain\Find\RECORD as DomainRECORD;
use App\Domain\Find\FormRecord as DomainFormRECORD;
use App\Domain\Find\IntroSuccessRecord as DomainIntroSuccessRecord;
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

        $cronCommand = $time[4].' '.$time[3].' '.$time[2].' '.$time[1]." * curl -d 'recordId=" . $this->recordId  ."' https://". $_SERVER['HTTP_HOST']."?service=App.Task_FindTask.returnMoney >> /tmp/task.log";

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

        \PhalApi\DI()->logger->info(__CLASS__.__FUNCTION__, "返还红包金额到发起人账户任务计划 recordId:" . $this->recordId);
        $domainRecord = new DomainRECORD();
        $recordInfo = $domainRecord->get($this->recordId);

        $state = 1;
        //过期失效更新下状态
        if(( $recordInfo['oper_state'] == 1 ) && (strtotime("now") - strtotime($recordInfo['create_time']))>=24 * (Time::HOUR)){
            $flag = $domainRecord->upate($this->recordId, array('oper_state' => 2));
            if($flag) {
                $state = 2;
            }else{
                \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__, "更新记录状态失败 recordId:" . $this->recordId);
            }
        }

        if( ( $state == 2 ) && (strtotime("now") - strtotime($recordInfo['create_time']))>=24 * (Time::HOUR)) {
            //返还红包金额到发起人账户内
            $domainUser = new DomainUSER();
            $updateFlag = $domainUser->updateWallet($recordInfo['openId'], $recordInfo['money'], 1);
            if(!$updateFlag){
                \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__, "返还红包金额到发起人账户内失败 openId:". $this->openId . " recordId:" . $this->recordId);
            }
        }


    }

}






