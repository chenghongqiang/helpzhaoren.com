<?php
/**
 * User: kewin.cheng
 * Date: 2018/1/15
 * Time: 13:56
 */

namespace App\Api\Task;

use App\Domain\Find\RECORD as DomainRECORD;
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
            'updateOperRecordState' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true , 'desc' => '找人记录id'),
            )
        );
    }
    /**
     * 更新找人记录状态
     */
    public function updateOperRecordState() {
        throw new Exception("hello", 404);
    }

    public function testUpdate(){
        \PhalApi\DI()->taskLite->add('App.Task_FindTask.UpdateOperRecordState', array('id' => 11));

        \PhalApi\DI()->taskLite->add('App.Task_FindTask.UpdateOperRecordState', array('id' => 22));

        \PhalApi\DI()->taskLite->add('App.Task_FindTask.UpdateOperRecordState', array('id' => 33));

        //$res = \PhalApi\DI()->taskRunnerLocal->go('App.Task_FindTask.UpdateOperRecordState');
        //return $res;
    }

    /**
     * 发送模板消息
     * @desc 被推荐人提交数据城后，给发起人、引荐人、被引荐人发送模板消息
     * @return boolean flag 1.成功 0.失败
     */
    public function sendModuleMsg(){
        $recordId = 11;
        $domainRecord = new DomainRECORD();
        $record = $domainRecord->get($recordId);

        if(!empty($record)){
            $domainIntroSuccessRecord = new DomainIntroSuccessRecord();
            $introSuccescRecord = $domainIntroSuccessRecord->get($recordId);
            if(empty($introSuccescRecord)){
                \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__. " 未找到推荐成功相关记录，id:" . $recordId);
            }

            //服务进度通知
            $data = array(
                'touser' => $record['openId'],
                'template_id' => 'DdQxT0RfqPy4AGOPVVHz7a9vK09W7MVsORTwfVMsHHw',
                'page' => 'pages/index',
                'form_id' => $this->formId,
                'data' => array(
                    'keyword1' => array('value' => '找到啦，赶紧去联系他（她）吧'),
                    'keyword2' => array('value' => $introSuccescRecord['wx_introducer_code']),
                    'keyword3' => array('value' => $introSuccescRecord['wx_introducered_code']),
                ),
                'emphasis_keyword' => ''

            );

            //发送模板消息给发起人
            $this->sendModuleMsgFunc($data);

            //收益到账通知
            $dataParam = array(
                'touser' => $introSuccescRecord['introduceredOpenId'],
                'template_id' => '2TyJ-pzj0k5QaYE3mlaMOC4CR-pofRwFlhJr0AEOvsE',
                'page' => 'pages/index',
                'form_id' => $this->formId,
                'data' => array(
                    'keyword1' => array('value' => $record['money']),
                    'keyword2' => array('value' => $record['money']),
                    'keyword3' => array('value' => $record['intro']),
                ),
                'emphasis_keyword' => ''

            );
            //发送模板消息给引荐人和被引荐人
            $this->sendModuleMsgFunc($dataParam);
        }

    }

    private function sendModuleMsgFunc($data){

        $domainIntroSuccessRecord = new DomainIntroSuccessRecord();
        return $domainIntroSuccessRecord->sendModuleMsg($data);
    }

}






