<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/25
 * Time: 12:28
 */

namespace App\Domain\Find;

use App\Model\Find\RECORD as ModelRECORD;

class RECORD {

    public function insert($data){
        $model = new ModelRECORD();
        return $model->insert($data);
    }

    public function get($id){
        $model = new ModelRECORD();
        return $model->get($id);
    }

    public function getRecordsByOpenId($openId){
        $model = new ModelRECORD();
        $records = $model->getRecordsByOpenId($openId);

        return $records;
    }

    public function getRecordByCode($code){
        $model = new ModelRECORD();
        $records = $model->getRecordByCode($code);

        return $records;
    }

}