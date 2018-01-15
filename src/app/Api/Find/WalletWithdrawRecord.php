<?php
/**
 * Created by PhpStorm.
 * User: kewin.cheng
 * Date: 18/1/7
 * Time: PM6:03
 */
namespace App\Api\Find;

use App\Domain\Common;
use App\Domain\Find\WalletWithdrawRecord as DomainWalletWithdrawRecord;
use PhalApi\Api;
use PhalApi\Exception;

/**
 * 钱包提取记录
 * Class WalletWithdrawRecord
 * @package App\Api\Find
 */
class WalletWithdrawRecord extends Api {

    public function getRules(){
        return array(
            'withdraw' => array(
                'thirdSessionKey' => array('name' => 'thirdSessionKey', 'type' => 'string', 'require' => true, 'desc' => '第三方session'),
                'money' => array('name' => 'money', 'type' => 'float', 'require' => true, 'desc' => '提现金额数'),
            ),
            'getWithdrawRecord' => array(
                'thirdSessionKey' => array('name' => 'thirdSessionKey', 'type' => 'string', 'require' => true, 'desc' => '第三方session'),
            ),
            'getRecord' => array(

            ),
            'updateState' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '提现记录id'),
                'secret' => array('name' => 'secret', 'type' => 'string', 'require' => true,'desc' => '密钥'),
            )
        );
    }

    /**
     * 用户提现
     * @desc 提取钱包金额
     * @return int id 记录创建成功
     */
    public function withdraw(){
        $commonDomain = new Common();
        $openId = $commonDomain->getOpenId($this->thirdSessionKey);

        $data = array(
            'openId' => $openId,
            'money' => $this->money
        );
        $domainWalletWithdrawRecord = new DomainWalletWithdrawRecord();
        $ret = $domainWalletWithdrawRecord->insert($data);

        return $ret;
    }

    /**
     * 获取当前用户提现记录
     * @desc 根据openid获取当前用户提现记录
     */
    public function getWithdrawRecord(){
        $commonDomain = new Common();
        $openId = $commonDomain->getOpenId($this->thirdSessionKey);

        $domainWalletWithdrawRecord = new DomainWalletWithdrawRecord();
        $ret = $domainWalletWithdrawRecord->getRecordByOpenid($openId);

        return $ret;
    }

    /**
     * 获取用户提现待打款记录 —— 临时供打款用
     * @desc 根据openid获取提现明细，根据时间倒序排列
     * @return int id 提现记录id
     * @return string create_time 提现时间
     * @return int money 本次提现金额
     * @return string wx_code 用户微信号
     */
    public function getRecord(){

        $domainWalletWithdrawRecord = new DomainWalletWithdrawRecord();

        //获取提现中的记录
        $walletWithdrawRecord = $domainWalletWithdrawRecord->getRecordByState(1);
        return $walletWithdrawRecord;
    }

    /**
     * 更新提现记录状态为提现成功 —— 临时供打款用
     * @desc 根据提现记录id和用户openId
     */
    public function updateState(){

        if($this->secret != 'zhaoren'){
            throw new Exception('secret错误', 500);
        }

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



