<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/24
 * Time: 14:11
 */

namespace App\Api\Find;

use PhalApi\Api;

/**
 * 找人相关接口
 * Class People
 * @package App\Api\Find
 */
class People extends Api{

    public function getRules(){
        return array(
            'intro' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true,  'min' => 1, 'desc' => 'ID')
            ),
            'getOperRecord' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true,  'min' => 1, 'desc' => 'ID')
            ),
            'wallet' => array(
                'openid' => array('name' => 'openid', 'type' => 'string', 'require' => true)
            )

        );
    }

    /**
     * 发起找人
     * @desc 填入红包金额、找人描述、微信号发起找人
     * @return int id 发起找人记录id
     */
    public function create(){

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

    }

    /**
     * 提现记录
     * @desc 根据openid获取提现明细
     * @return string create_time 提现时间
     * @return int money 本次提现金额
     */
    public function walletRecord(){

    }
}