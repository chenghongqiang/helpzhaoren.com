<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/25
 * Time: 12:47
 */

namespace App\Api\Find;

use PhalApi\Api;
use App\Domain\Find\WALLET as DomainWALLET;

/**
 * 用户钱包
 * Class Wallet
 * @package App\Api\Find
 */
class Wallet extends Api{

    public function getRules(){
        return array(
            '*' => array(
                'thirdSessionKey' => array('name' => 'thirdSessionKey', 'type' => 'string', 'require' => true, 'desc' => '第三方session'),
            ),
            'insert' => array(
                'openid' => array('name' => 'openid', 'type' => 'string', 'require' => true, 'desc' => '用户openid'),
                'money' => array('name' => 'money', 'type' => 'string', 'require' => true, 'desc' => '提现金额')
            ),
            'walletRecord' => array(
                'openid' => array('name' => 'openid', 'type' => 'string', 'require' => true, 'desc' => '用户openid')
            )
        );
    }

    /**
     * 钱包操作提现写入提现记录
     * @desc 当前用户操作提现，写入提现记录
     * @return int id 插入记录id，false插入失败
     */
    public function insert(){

        $data = array(
            'openid' => $this->openid,
            'money' => $this->money
        );

        $domainWallet = new DomainWALLET();

        $walletRecord = $domainWallet->insert($data);
        return $walletRecord;

    }

    /**
     * 获取提现记录
     * @desc 根据openid获取提现明细
     * @return string create_time 提现时间
     * @return int money 本次提现金额
     */
    public function walletRecord(){

        $domainWallet = new DomainWALLET();

        $walletRecord = $domainWallet->getWalletRecord($this->openid);
        return $walletRecord;
    }

}