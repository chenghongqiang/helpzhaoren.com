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
 * Class TradeRecord
 * @package App\Model\Find
 */
class TradeRecord extends NotORM {

    protected function getTableName($id){
        return 'trade_record';
    }

    public function getTradeRecord($recordId, $openID){
        return $this->getORM()
            ->select('id')
            ->where(array('recordId' => $recordId, 'openid' => $openID))
            ->fetchRow();
    }


}