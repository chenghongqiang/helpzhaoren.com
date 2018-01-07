<?php
/**
 * Created by PhpStorm.
 * User: kewin.cheng
 * Date: 18/1/7
 * Time: PM5:25
 */
namespace App\Domain\Find;

use App\Model\Find\WalletWithdrawRecord as ModelWalletWithdrawRecord;

class WalletWithdrawRecord{

    public function insert($data){

        $model = new ModelWalletWithdrawRecord();
        return $model->insert($data);
    }

    public function get($id){
        $model = new ModelWalletWithdrawRecord();
        return $model->get($id);
    }

    public function upate($id, $data){
        $model = new ModelWalletWithdrawRecord();
        return $model->update($id, $data);
    }

    public function getRecordByOpenid($openId){
        $model = new ModelWalletWithdrawRecord();
        return $model->getRecordByOpenid($openId);
    }

    public function getRecordByState($state){
        $model = new ModelWalletWithdrawRecord();
        return $model->getRecordByState($state);
    }

}