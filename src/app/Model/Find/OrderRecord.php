<?php
/**
 * Created by PhpStorm.
 * User: kewin.cheng
 * Date: 18/1/6
 * Time: PM11:01
 */

namespace App\Model\Find;

use PhalApi\Model\NotORMModel as NotORM;

/**
 * Class OrderRecord
 * @package App\Model\Find
 */
class OrderRecord extends NotORM {

    const PROGRESS = 1;
    const SUCCESS = 2;

    protected function getTableName($id){
        return 'order_record';
    }

    public function getRecordByTradeNo($outTradeNo, $openId){
        return $this->getORM()
            ->select('id, state, out_trade_no')
            ->where(array('out_trade_no' => $outTradeNo, 'openId' => $openId))
            ->fetchRow();
    }

}