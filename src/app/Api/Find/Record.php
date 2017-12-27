<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/25
 * Time: 11:28
 */

namespace App\Api\Find;

use App\Component\FindApi;
use App\Domain\Find\RECORD as DomainRECORD;

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

            ),
            'getOperRecord' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true , 'desc' => '找人记录id')
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
     * 引荐人
     * @desc 引荐人填入微信号，引荐找人
     * @return boolean true 插入成功
     * @return boolean false 插入失败
     */
    public function intro(){

    }

    /**
     * 找人记录详情
     * @desc 根据找人记录id获取详情
     * @return int id 找人记录id
     * @return int money 红包金额
     * @return string code 找人码 6位字符
     * @return string intro 找人推荐描述
     * @return string wx_introducers_code 引荐人微信号
     * @return string wx_introducered_code 被引荐人微信号
     * @return int oper_state 记录状态 1.进行中 2.过期失效 3.引荐成功
     * @return string create_time 记录创建时间
     */
    public function getOperRecord(){
        $domainRecord = new DomainRECORD();
        $ret = $domainRecord->get($this->id);
        return $ret;
    }
}