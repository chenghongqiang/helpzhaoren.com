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
use App\Domain\Find\OrderRecord as DomainOrderRecord;
use App\Domain\Find\IntroRecord as DomainIntroRecord;
use App\Domain\Find\IntroSuccessRecord as DomainIntroSuccessRecord;
use PhalApi\Exception;

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
                'out_trade_no' => array('name' => 'out_trade_no', 'type' => 'int', 'require' => true , 'desc' => '商户订单号'),
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
                'code' => array('name' => 'code', 'type' => 'string', 'require' => true, 'min' => '6','max' => '6' ,'desc' => '找人code'),
            )
        ));
    }

    /**
     * 发起找人
     * @desc 填入红包金额、找人描述、微信号发起找人
     * @return int id 发起找人记录id
     * @return string code 找人码
     * @exception 500 创建找人记录失败
     */
    public function create(){

        \PhalApi\DI()->logger->info(__CLASS__.__FUNCTION__. " openid:" . $this->openID);
        $code = rand(pow(10,(6-1)), pow(10,6)-1);
        $data = array(
            'openid' => $this->openID,
            'money' => $this->money,
            'intro' => $this->intro,
            'wx_self_code' => $this->wx_self_code,
            'oper_state' => 1,
            'code' => $code, //随机生成六位找人码
            'out_trade_no' => $this->out_trade_no
        );

        //通过商户订单号去检测是否发起找人记录中的商户订单号是有效的
        $domainOrderRecord = new DomainOrderRecord();
        $ret = $domainOrderRecord->checkOrderState($this->out_trade_no, $this->openID);
        if(!$ret){
            throw new Exception("商户订单号不存在或未支付成功", 400);
        }

        $domainRecord = new DomainRECORD();
        $id = $domainRecord->insert($data);
        if($id < 1){
            throw new Exception('找人记录创建失败', 500);
        }

        $ret['id'] = $id;
        $ret['code'] = $code;

        return $ret;

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

            $introUserInfo = $domainUser->getUserByOpenid($introUserRet['openId']);
            if(!empty($introUserInfo)){
                $ret['wx_introducer_avatarUrl'] = $introUserInfo['avatarUrl'];
            }

        }

        //过期失效更新下状态
        if(( $ret['oper_state']!=2 ) && (strtotime("now") - strtotime($ret['create_time']))>=24*3600){
            $flag = $domainRecord->upate($this->id, array('oper_state' => 2));
            if($flag) {
                $ret['oper_state'] = 2;
            }
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
        $openIdArr = array();
        //获取找人记录详情
        $domainRecord = new DomainRECORD();
        $ret = $domainRecord->get($this->id);

        $domainUser = new DomainUSER();
        $creatorInfo = $domainUser->getUserByOpenid($ret['openId']);
        $ret['wx_creator_avatarUrl'] = $creatorInfo['avatarUrl'];

        //获取引荐人被引荐人信息
        $domainIntroSuccessRecord = new DomainIntroSuccessRecord();
        $introRecord = $domainIntroSuccessRecord->getRecordByRecordId($this->id);

        $avatarUrlKey = array('wx_introducer_avatarUrl', 'wx_introducered_avatarUrl');

        if(!empty($introRecord)){
            $ret['wx_introducer_code'] = $introRecord['wx_introducer_code'];
            $ret['wx_introducered_code'] = $introRecord['wx_introducered_code'];
            array_push($openIdArr, $introRecord['introducererOpenId'], $introRecord['introduceredOpenId']);

            //获取三方用户的数据
            $userInfoList = $domainUser->getUserByOpenid($openIdArr);
            foreach ($userInfoList as $k => $value){
                $ret[$avatarUrlKey[$k]] = $value['avatarUrl'];
            }

            if($introRecord['introducererOpenId'] == $introRecord['introduceredOpenId']){
                //如果引荐人和被引荐人是同一个人
                $ret[$avatarUrlKey[1]] = $ret[$avatarUrlKey[0]];
            }
        }

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

        $ret = array();

        $domainRecord = new DomainRECORD();
        $domainIntroSuccessRecord = new DomainIntroSuccessRecord();
        $introSuccessRecords = $domainIntroSuccessRecord->getRecordIdByOpenId($this->openID);
        foreach ($introSuccessRecords as $k => $value) {

            $record = $domainRecord->get($value['recordId']);
            $ret[$k] = $record;

            //获取发起人图像
            $domainUser = new DomainUSER();
            $userInfoList = $domainUser->getUserByOpenid($record['openId']);
            $ret[$k]['wx_creator_avatarUrl'] = $userInfoList['avatarUrl'];
            $ret[$k]['wx_creator_nickName'] = $userInfoList['nickName'];
        }

        return $ret;
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