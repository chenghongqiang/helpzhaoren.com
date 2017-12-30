<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/25
 * Time: 11:28
 */

namespace App\Api\Find;

use App\Component\FindApi;
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
                'intro' => array('name' => 'intro', 'type' => 'string', 'require' => true, 'min' => '2','max' => '20' ,'desc' => '找人描述'),
                'wx_self_code' => array('name' => 'wx_self_code', 'type' => 'string', 'require' => true, 'desc' => '发起人微信号'),
            ),
            'intro' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true , 'desc' => '找人记录id'),
                'wx_introducers_code' => array('name' => 'wx_introducers_code', 'type' => 'string', 'require' => true, 'desc' => '引荐人微信号'),
            ),
            'getOperRecord' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true , 'desc' => '找人记录id'),
                'intro_user_id' => array('name' => 'id', 'type' => 'int', 'desc' => '引荐人id')
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
            'code' => rand(pow(10,(6-1)), pow(10,6)-1)
        );

        $domainRecord = new DomainRECORD();
        $id = $domainRecord->insert($data);
        return $id;

    }

    /**
     * 找人记录详情
     * @desc 根据找人记录id获取详情
     * @return int id 找人记录id
     * @return int money 红包金额
     * @return string code 找人码 6位字符
     * @return string intro 找人推荐描述
     * @return string wx_introducer_code 引荐人微信号
     * @return string wx_introducered_code 被引荐人微信号
     * @return int oper_state 记录状态 1.进行中 2.过期失效 3.引荐成功
     * @return string create_time 记录创建时间
     * @rerurn string current_time 当前时间，根据记录创建时间和服务器当前时间确认此记录是否过期
     */
    public function getOperRecord(){
        //获取找人记录详情
        $domainRecord = new DomainRECORD();
        $ret = $domainRecord->get($this->id);

        //获取引荐人信息
        $domainIntroRecord = new DomainIntroRecord();
        $introUserRet = $domainIntroRecord->get($this->intro_user_id);

        $ret['wx_introducer_code'] = $introUserRet->wx_introducer_code;

        return $ret;
    }
}