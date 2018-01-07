<?php
/**
 * Created by PhpStorm.
 * User: kewin.cheng
 * Date: 18/1/7
 * Time: PM6:03
 */
namespace App\Api\Find;

use App\Domain\Find\WalletWithdrawRecord as DomainWalletWithdrawRecord;
use PhalApi\Api;
use PhalApi\Exception;

class WalletWithdrawRecord extends Api {

    public function getRules(){
        return array(
            'getWithdrawRecord' => array(

            ),
            'updateState' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '提现记录id'),
                'openId' => array('name' => 'openId', 'type' => 'string', 'require' => true,'desc' => '用户openId'),
            )
        );
    }

    /**
     * 获取用户提现待打款记录，临时供打款用
     * @desc 根据openid获取提现明细，根据时间倒序排列
     * @return
     * @return string create_time 提现时间
     * @return int money 本次提现金额
     */
    public function getWithdrawRecord(){

        $domainWalletWithdrawRecord = new DomainWalletWithdrawRecord();

        //获取提现中的记录
        $walletWithdrawRecord = $domainWalletWithdrawRecord->getRecordByState(1);
        return $walletWithdrawRecord;
    }

    /**
     * 更新提现记录状态为提现成功
     * @desc 根据提现记录id和用户openId
     */
    public function updateState(){
        $domainWalletWithdrawRecord = new DomainWalletWithdrawRecord();

        $record = $domainWalletWithdrawRecord->get($this->id);
        if(empty($record)){
            throw new Exception('未查找到当前记录，请确认此信息是否正确', 400);
        }else{
            $flag = $domainWalletWithdrawRecord->upate($record['id'], array('state' => 2));
            if(!$flag){
                throw new Exception('更新失败，请重试', 500);
            }else{
                throw new Exception('更新成功，id为:' . $record['id'], 200);
            }
        }

    }

}



