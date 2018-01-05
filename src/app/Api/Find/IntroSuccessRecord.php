<?php
/**
 * Created by PhpStorm.
 * User: kewin.cheng
 * Date: 17/12/30
 * Time: PM5:47
 */

namespace App\Api\Find;

use App\Component\FindApi;
use App\Domain\Find\RECORD as DomainRECORD;
use App\Domain\Find\IntroRecord as DomainIntroRecord;
use App\Domain\Find\IntroSuccessRecord as DomainIntroSuccessRecord;
use PhalApi\Exception;

/**
 * 被引荐人提交数据接口
 * Class IntroSuccessRecord
 * @package App\Api\Find
 */
class IntroSuccessRecord extends FindApi{

    public function getRules(){

        return array_merge(parent::getRules(),array(

            'intro' => array(
                'record_id' => array('name' => 'record_id', 'type' => 'int', 'require' => true , 'desc' => '找人记录id'),
                'intro_user_id' => array('name' => 'intro_user_id', 'type' => 'int', 'require' => true , 'desc' => '引荐人id'),
                'wx_introducered_code' => array('name' => 'wx_introducered_code', 'type' => 'string', 'require' => true, 'desc' => '被引荐人微信号'),
            ),
            'sendModuleMsg' => array(
                'formId' => array('name' => 'formId', 'type' => 'string', 'require' => true , 'desc' => 'formId'),
            ),
            'getWxQrcode' => array(
                'page' => array('name' => 'page', 'type' => 'string', 'require' => true , 'desc' => '已经发布的小程序页面'),
                'width' => array('name' => 'width', 'type' => 'string', 'desc' => '二维码宽度', 'default' => '430'),
            )
        ));
    }

    /**
     * 被引荐人提交数据
     * @desc 被引荐人填入微信号，引荐找人成功
     * @return int intro_success_id 引荐成功记录id
     */
    public function intro(){

        //获取引荐人记录
        $domainIntroRecord = new DomainIntroRecord();
        $introRecord = $domainIntroRecord->get($this->intro_user_id);

        $data = array(
            'recordId' => $this->record_id,
            'introducererOpenId' => $introRecord['openId'],
            'wx_introducer_code' => $introRecord['wx_introducer_code'],
            'introduceredOpenId' => $this->openID,
            'wx_introducered_code' => $this->wx_introducered_code
        );

        try{
            //开启事务，当成功推荐记录表数据写入成功后更新找人记录状态失败时回滚
            \PhalApi\DI()->notorm->beginTransaction('db_master');

            $ret = \PhalApi\DI()->notorm->intro_success_record->insert($data);
            if($ret){
                $domainRecord = new DomainRECORD();
                $flag = $domainRecord->upate($this->record_id, array('oper_state' => 3));
                if($flag){
                    \PhalApi\DI()->notorm->commit('db_master');
                }else{
                    \PhalApi\DI()->notorm->rollback('db_master');
                    throw new Exception('更新记录状态失败', 500);
                }
            }

            return $ret;
        }catch (\Exception $e){
            throw new Exception($e->getMessage(), 500);
        }

    }

    /**
     * 发送模板消息
     * @desc 被推荐人提交数据城后，给发起人、引荐人、被引荐人发送模板消息
     * @return boolean flag 1.成功 0.失败
     */
    public function sendModuleMsg(){

        \PhalApi\DI()->logger->info(__CLASS__.__FUNCTION__  . ' openID:' . $this->openID .' formId:' . $this->formId);

        $data = array(
            'touser' => $this->openID,
            'template_id' => '2TyJ-pzj0k5QaYE3mlaMOB_93KgyIRkP8JQ7Nk6DV5A',
            'page' => 'index',
            'form_id' => $this->formId,
            'data' => array(
                'keyword1' => array('value' => '3'),
                'keyword2' => array('value' => 'kewin'),
                'keyword3' => array('value' => '找人红包'),
            ),
            'emphasis_keyword' => ''

        );
        //发送模板消息给发起人
        $this->sendModuleMsgFunc($data);

        $dataParam = array(
            'touser' => $this->openID,
            'template_id' => '2TyJ-pzj0k5QaYE3mlaMOB_93KgyIRkP8JQ7Nk6DV5A',
            'page' => 'index',
            'form_id' => $this->formId,
            'data' => array(
                'keyword1' => array('value' => '3'),
                'keyword2' => array('value' => 'kewin'),
                'keyword3' => array('value' => '找人红包'),
            ),
            'emphasis_keyword' => ''

        );
        //发送模板消息给引荐人和被引荐人
        $this->sendModuleMsgFunc($dataParam);

    }

    private function sendModuleMsgFunc($data){


        $domainIntroSuccessRecord = new DomainIntroSuccessRecord();
        return $domainIntroSuccessRecord->sendModuleMsg($data);
    }

}