<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/25
 * Time: 13:37
 */

namespace App\Model\Find;

use PhalApi\Model\NotORMModel as NotORM;

class WALLET extends NotORM {

    protected function getTableName($id) {
        return 'user_wallet_record';
    }

    public function getWalletRecordByOpenid($openid){
        return $this->getORM()
            ->select('*')
            ->where('openid', $openid)
            ->fetchAll();
    }

}