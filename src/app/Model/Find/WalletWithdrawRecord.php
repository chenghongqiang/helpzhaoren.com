<?php
/**
 * Created by PhpStorm.
 * User: kewin.cheng
 * Date: 18/1/7
 * Time: PM5:17
 */
namespace App\Model\Find;

use PhalApi\Model\NotORMModel as NotORM;

class WallWithdrawRecord extends NotORM{

    protected function getTableName($id) {
        return 'wallet_withdraw_record';
    }

    /**
     * 根据openId获取提现记录
     * @param $openId
     * @return mixed
     */
    public function getRecordByOpenid($openId){
        return $this->getORM()
            ->select('*')
            ->where('openid', $openId)
            ->order('create_time desc')
            ->fetchAll();
    }

    public function getRecordByState($state){
        return $this->getORM()
            ->select('*')
            ->where('state', $state)
            ->order('create_time asc')
            ->fetchAll();
    }



}