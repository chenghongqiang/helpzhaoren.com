<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/25
 * Time: 11:28
 */

namespace App\Api\Find;

use App\Component\FindApi;
use App\Domain\Find\USER as DomainUSER;
use App\Domain\Find\RECORD as DomainRECORD;
use App\Domain\Find\IntroRecord as DomainIntroRecord;

/**
 * 找人记录
 * Class Record
 * @package App\Api\Find
 */
class Record extends FindApi{

    public function getRules(){

        return array_merge(parent::getRules(),array(

            'create' => array(
                'money' => array('name' => 'money', 'type' => 'int', 'require' => true, 'desc' => '红包金额'),
                'intro' => array('name' => 'intro', 'type' => 'string', 'require' => true, 'min' => '6','max' => '90' ,'desc' => '找人描述'),
                'wx_self_code' => array('name' => 'wx_self_code', 'type' => 'string', 'require' => true, 'desc' => '发起人微信号'),
            ),
            'getIntroRecord' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true , 'desc' => '找人记录id'),
                'intro_user_id' => array('name' => 'intro_user_id', 'type' => 'int', 'desc' => '引荐人id')
            ),
            'getOperRecord' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true , 'desc' => '找人记录id')
            ),
            'checkCode' => array(
                'intro' => array('name' => 'code', 'type' => 'string', 'require' => true, 'min' => '6','max' => '6' ,'desc' => '找人code'),
            )
        ));
    }

    /**
     * 发起找人
     * @desc 填入红包金额、找人描述、微信号发起找人
     * @return int id 发起找人记录id
     */
    public function create(){

        \PhalApi\DI()->logger->info("openid:" . $this->openID);
        $data = array(
            'openid' => $this->openID,
            'money' => $this->money,
            'intro' => $this->intro,
            'wx_self_code' => $this->wx_self_code,
            'oper_state' => 1,
            'code' => rand(pow(10,(6-1)), pow(10,6)-1) //随机生成六位找人码
        );

        $domainRecord = new DomainRECORD();
        $id = $domainRecord->insert($data);
        return $id;

    }

    /**
     * 引荐找人记录详情【引荐人提交数据页面和提交数据后页面详情】
     * @desc 根据找人记录id和引荐人id获取详情
     * @return int id 找人记录id
     * @return string wx_creator_avatarUrl 发起人微信图像
     * @return int money 红包金额
     * @return string code 找人码 6位字符
     * @return string intro 找人推荐描述
     * @return string wx_introducer_code 引荐人微信号
     * @return string wx_introducer_avatarUrl 引荐人微信图像
     * @return int oper_state 记录状态 1.进行中 2.过期失效 3.引荐成功
     * @return string create_time 记录创建时间
     * @rerurn string current_time 当前时间，根据记录创建时间和服务器当前时间确认此记录是否过期
     */
    public function getIntroRecord(){
        //获取找人记录详情
        $domainRecord = new DomainRECORD();
        $ret = $domainRecord->get($this->id);

        $domainUser = new DomainUSER();
        $creatorInfo = $domainUser->getUserByOpenid($ret['openId']);
        $ret['wx_creator_avatarUrl'] = $creatorInfo['avatarUrl'];

        if(!empty($this->intro_user_id)){
            //获取引荐人信息
            $domainIntroRecord = new DomainIntroRecord();
            $introUserRet = $domainIntroRecord->get($this->intro_user_id);

            $ret['wx_introducer_code'] = $introUserRet['wx_introducer_code'];
        }

        return $ret;
    }

    /**
     * 引荐找人成功记录详情【被推荐人提交数据后引荐成功信息页面详情】
     * @desc 根据找人记录id获取详情
     * @return int id 找人记录id
     * @return string wx_creator_avatarUrl 发起人微信图像
     * @return int money 红包金额
     * @return string code 找人码 6位字符
     * @return string intro 找人推荐描述
     * @return string wx_introducer_code 引荐人微信号
     * @return string wx_introducer_avatarUrl 引荐人微信图像
     * @return string wx_introducered_code 被引荐人微信号
     * @return string wx_introducered_avatarUrl 被引荐人微信图像
     * @return string create_time 记录创建时间
     */
    public function getOperRecord(){
        //获取找人记录详情
        $domainRecord = new DomainRECORD();
        $ret = $domainRecord->get($this->id);

        $domainUser = new DomainUSER();
        $creatorInfo = $domainUser->getUserByOpenid($ret['openId']);
        $ret['wx_creator_avatarUrl'] = $creatorInfo['avatarUrl'];

        //获取引荐人信息
        $domainIntroRecord = new DomainIntroRecord();
        $introUserRet = $domainIntroRecord->get($this->intro_user_id);

        $ret['wx_introducer_code'] = $introUserRet['wx_introducer_code'];

        return $ret;
    }

    /**
     * 获取当前用户找人记录【找人记录页面找人tab】
     * @desc 获取当前用户所有相关找人记录
     * @return string intro 找人描述
     * @return string money 红包金额
     * @return string code 找人码
     * @return int oper_state 1.进行中 2.过期失效 3.引荐成功
     * @return string create_time 创建时间
     */
    public function getFindRecord(){
        $domainRecord = new DomainRECORD();
        $records = $domainRecord->getRecordsByOpenId($this->openID);

        return $records;
    }

    /**
     * 获取当前用户引荐被引荐记录【找人记录页面引荐tab】
     * @desc 获取当前用户所有引荐被引荐找人相关记录
     * @return string intro 找人描述
     * @return string wx_creator_avatarUrl 发起人微信图像
     * @return string wx_creator_nickName 发起人昵称
     * @return string money 红包金额
     * @return int oper_state 1.进行中 2.过期失效 3.引荐成功
     * @return string create_time 创建时间
     */
    public function getFindIntroRecord(){
        $domainRecord = new DomainRECORD();
        $records = $domainRecord->getRecordsByOpenId($this->openID);

        return $records;
    }

    /**
     * 验证找人码
     * @desc 通过找人码进入找人页面
     * @return int id 找人记录id
     * @return string desc 找人描述
     */
    public function checkCode(){
        $domainRecord = new DomainRECORD();
        $record = $domainRecord->getRecordByCode($this->code);

        return $record;
    }
}