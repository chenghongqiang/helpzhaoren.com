<?php
/**
 * Created by PhpStorm.
 * User: kewin.cheng
 * Date: 18/1/6
 * Time: PM11:02
 */

namespace App\Domain\Find;

use App\Model\Find\OrderRecord as ModelOrderRecord;

/**
 * Class OrderRecord
 * @package App\Domain\Find
 */
class OrderRecord  {

    public function insert($data){
        $model = new ModelOrderRecord();
        return $model->insert($data);
    }

    public function get($id){
        $model = new ModelOrderRecord();
        return $model->get($id);
    }

    public function upate($id, $data){
        $model = new ModelOrderRecord();
        return $model->update($id, $data);
    }

    public function getRecordByRecordId($recordId, $openId){
        $model = new ModelOrderRecord();
        return $model->getRecordByRecordId($recordId, $openId);
    }

    public function getRecordByTradeNo($outTradeNo, $openId){
        $model = new ModelOrderRecord();
        return $model->getRecordByTradeNo($outTradeNo, $openId);
    }

    public function checkOrderState($outTradeNo, $openId){
        $model = new ModelOrderRecord();
        $record = $model->getRecordByTradeNo($outTradeNo, $openId);

        if(!empty($record) && $record['state']== ModelOrderRecord::SUCCESS){
            return true;
        }else{
            return false;
        }

    }

}
