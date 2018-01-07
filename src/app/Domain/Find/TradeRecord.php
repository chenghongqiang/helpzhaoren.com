<?php
/**
 * Created by PhpStorm.
 * User: kewin.cheng
 * Date: 18/1/6
 * Time: PM11:02
 */

namespace App\Domain\Find;

use App\Model\Find\TradeRecord as ModelTradeRecord;

/**
 * Class TradeRecord
 * @package App\Domain\Find
 */
class TradeRecord  {

    public function insert($data){
        $model = new ModelTradeRecord();
        return $model->insert($data);
    }

    public function get($id){
        $model = new ModelTradeRecord();
        return $model->get($id);
    }

    public function upate($id, $data){
        $model = new ModelTradeRecord();
        return $model->update($id, $data);
    }

    public function getTradeRecord($recordId, $openId){
        $model = new ModelTradeRecord();
        return $model->getTradeRecord($recordId, $openId);
    }
}
