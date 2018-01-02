<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/28
 * Time: 16:25
 */
namespace App\Api\Find;

use App\Component\FindApi;
use App\Domain\Find\IntroRecord as DomainIntroRecord;

/**
 * 引荐人提交数据接口
 * Class IntroRecord
 * @package App\Api\Find
 */
class IntroRecord extends FindApi{

    public function getRules(){

        return array_merge(parent::getRules(),array(

            'intro' => array(
                'record_id' => array('name' => 'record_id', 'type' => 'int', 'require' => true , 'desc' => '找人记录id'),
                'wx_introducer_code' => array('name' => 'wx_introducer_code', 'type' => 'string', 'require' => true, 'desc' => '引荐人微信号'),

            )
        ));
    }

    /**
     * 引荐人提交数据
     * @desc 引荐人填入微信号，引荐找人
     * @return int intro_user_id 引荐成功后引荐人记录id，方便分享后查找对应引荐人记录
     */
    public function intro(){
        $domainIntroRecord = new DomainIntroRecord();

        $data = array(
            'recordId' =>$this->record_id,
            'openId' => $this->openID,
            'wx_introducer_code' => $this->wx_introducer_code
        );

        //引荐人下次重新提交数据，根据openId覆盖以前提交的数据
        $introRecord = $domainIntroRecord->getIntroRecord($this->record_id, $this->openID);
        if(!empty($introRecord)){
            $ret = $domainIntroRecord->update(
                array('id' => $introRecord['id'], 'openId' => $this->openID), $data
            );
            //更新成功返回仍然返回intro_user_id出去
            return $ret? $introRecord['id']: $ret;
        }

        $ret = $domainIntroRecord->insert($data);
        return $ret;
    }

}