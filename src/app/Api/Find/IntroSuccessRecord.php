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
            'openId' => $this->openID,
            'wx_introducer_code' => $this->wx_introducer_code
        );

        //引荐人下次重新提交数据，根据openId覆盖以前提交的数据
        $introRecord = $domainIntroSuccessRecord->get($this->id);
        if(!empty($introRecord)){
            return $domainIntroSuccessRecord->update(
                array('id' => $this->id, 'openId' => $this->openID), $data
            );
        }

        $ret = $domainIntroSuccessRecord->insert($data);
        return $ret;
    }

}