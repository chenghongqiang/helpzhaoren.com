<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/25
 * Time: 12:47
 */

namespace App\Api\Find;

use App\Component\FindApi;
use App\Domain\Find\WALLET as DomainWALLET;
use App\Domain\Find\WalletWithdrawRecord as DomainWalletWithdrawRecord;

/**
 * 用户钱包
 * Class Wallet
 * @package App\Api\Find
 */
class Wallet extends FindApi{

    public function getRules(){

        return array_merge(parent::getRules(),array(

            'insert' => array(
                'money' => array('name' => 'money', 'type' => 'string', 'require' => true, 'desc' => '提现金额')
            ),
            'walletRecord' => array(

            )
        ));
    }

    /**
     * 钱包操作提现写入提现记录
     * @desc 当前用户操作提现，写入提现记录
     * @return int id 插入记录id，false插入失败
     */
    private function insert(){

        $data = array(
            'openid' => $this->openID,
            'money' => $this->money
        );

        $domainWallet = new DomainWALLET();

        $walletRecord = $domainWallet->insert($data);
        return $walletRecord;

    }

    /**
     * 获取提现记录
     * @desc 根据openid获取提现明细，根据时间倒序排列
     * @return string create_time 提现时间
     * @return int money 本次提现金额
     */
    private function getWalletWithdrawRecord(){

        $domainWalletWithdrawRecord = new DomainWalletWithdrawRecord();

        $walletWithdrawRecord = $domainWalletWithdrawRecord->getRecordByOpenid($this->openID);
        return $walletWithdrawRecord;
    }

}