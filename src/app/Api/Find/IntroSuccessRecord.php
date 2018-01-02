<?php
/**
 * Created by PhpStorm.
 * User: kewin.cheng
 * Date: 17/12/30
 * Time: PM5:47
 */

namespace App\Api\Find;

use App\Component\FindApi;
use App\Domain\Find\IntroRecord as DomainIntroRecord;
use App\Domain\Find\IntroSuccessRecord as DomainIntroSuccessRecord;

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

        $domainIntroSuccessRecord = new DomainIntroSuccessRecord();

        $data = array(
            'recordId' => $this->id,
            'introducererOpenId' => $introRecord['openId'],
            'wx_introducer_code' => $introRecord['wx_introducer_code'],
            'introduceredOpenId' => $this->openID,
            'wx_introducered_code' => $this->wx_introducered_code
        );

        $ret = $domainIntroSuccessRecord->insert($data);
        return $ret;
    }

    /**
     * 发送模板消息
     * @desc 被推荐人提交数据城后，给发起人、引荐人、被引荐人发送模板消息
     * @return boolean flag 1.成功 0.失败
     */
    public function sendModuleMsg($params){
        $data = array(
            'touser' => $params['touser'],
            'template_id' => $params['template_id'],
            'page' => $params['page'],
            'form_id' => $params['form_id'],
            'data' => array(
                'keyword1' => array('value' => '', 'color' => ''),
                'keyword2' => array('value' => '', 'color' => ''),
                'keyword3' => array('value' => '', 'color' => ''),
                'keyword4' => array('value' => '', 'color' => ''),
            ),
            'emphasis_keyword' => ''

        );

        $domainIntroSuccessRecord = new DomainIntroSuccessRecord();
        $domainIntroSuccessRecord->sendModuleMsg($data);
    }


}