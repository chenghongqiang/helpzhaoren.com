<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/28
 * Time: 16:25
 */
namespace App\Api\Find;

use App\Component\FindApi;
use App\Domain\Find\IntroRecord as DomainIntroRecord;

class IntroRecord extends FindApi{

    public function getRules(){

        return array_merge(parent::getRules(),array(

            'intro' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true , 'desc' => '找人记录id'),
                'wx_introducer_code' => array('name' => 'wx_introducer_code', 'type' => 'string', 'require' => true, 'desc' => '引荐人微信号'),

            )
        ));
    }

    /**
     * 引荐人提交数据
     * @desc 引荐人填入微信号，引荐找人
     * @return int id 引荐成功后引荐人id，方便分享后查找对应引荐人记录
     * @return boolean false 插入失败
     */
    public function intro(){
        $domainIntroRecord = new DomainIntroRecord();

        $data = array(
            'id' =>$this->id,
            'openId' => $this->openID,
            'wx_introducer_code' => $this->wx_introducer_code
        );

        $ret = $domainIntroRecord->insert($data);
        return $ret;
    }

}