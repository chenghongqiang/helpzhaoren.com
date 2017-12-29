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
                'user_flag' => array('name' => 'user_flag', 'type' => 'int', 'desc' => '是否是引荐本人[非必须，当是本人时带上] 1为是本人'),
            )
        ));
    }

    /**
     * 引荐人提交数据
     * @desc 引荐人填入微信号，引荐找人
     * @return boolean true 插入成功
     * @return boolean false 插入失败
     */
    public function intro(){
        $domainIntroRecord = new DomainIntroRecord();

        $updateData = array(
            'id' =>$this->id,
            'openId' => $this->openID,
        );
        $data = array(
            'id' =>$this->id,
            'openId' => $this->openID,
            'wx_introducer_code' => $this->wx_introducer_code
        );
        if(isset($this->user_flag)&&$this->user_flag==1){
            //引荐本人进入引荐页面，需要更新引荐人微信号
            $domainIntroRecord->update($updateData, $data);
        }else{
            $domainIntroRecord->insert($data);
        }

    }

}