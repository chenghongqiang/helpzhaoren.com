<?php
/**
 * User: kewin.cheng
 * Date: 2018/1/15
 * Time: 13:56
 */

namespace App\Api\Task;

use App\Common\Utils\Code;
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
            'returnMoney' => array(
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
        $ret = \PhalApi\DI()->taskRunnerLocal->go('App.Task_FindTask.returnMoney');
        return $ret;
    }

    /**
     * 收集formId
     * @ignore
     * @desc 计划任务，在创建记录、引荐人和被引荐人提交数据时收集formId [crontab配置定时任务]
     * @use \PhalApi\DI()->taskLite->add('App.Task_FindTask.collectFormId', array('formId' => 'xxxx'));
     */
    public function collectFormId(){
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
     * 红包返还
     * @desc 发起记录24小时内未被完成，设置发起状态为过期，同时返还红包到发起人账户
     */
    public function returnMoney() {
        $domainRecord = new DomainRECORD();
        $recordInfo = $domainRecord->get($this->recordId);

        //过期失效更新下状态
        if(( $recordInfo['oper_state'] == 1 ) && (strtotime("now") - strtotime($recordInfo['create_time']))>=24*3600){
            $flag = $domainRecord->upate($this->id, array('oper_state' => 2));
            if($flag) {
                $ret['oper_state'] = 2;
            }
        }

        if( ( $recordInfo['oper_state'] == 2 ) && (strtotime("now") - strtotime($recordInfo['create_time']))>=24*3600 ) {
            //返还红包金额到发起人账户内
            $domainUser = new DomainUSER();
            $updateFlag = $domainUser->updateWallet($recordInfo['openId'], $recordInfo['money'], 1);
            if(!$updateFlag){
                \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__, "返还红包金额到发起人账户内失败 openId:". $this->openId . " recordId:" . $this->recordId);
            }
        }


    }

}






