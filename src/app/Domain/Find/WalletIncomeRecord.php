<?php
/**
 * User: kewin.cheng
 * Date: 2018/1/9
 * Time: 11:17
 */

namespace App\Domain\Find;

use App\Model\Find\WalletIncomeRecord as ModelWalletIncomeRecord;

class WalletIncomeRecord {

    public function insert($data){

        $model = new ModelWalletIncomeRecord();
        return $model->insert($data);
    }

    public function get($id){
        $model = new ModelWalletIncomeRecord();
        return $model->get($id);
    }

    public function upate($id, $data){
        $model = new ModelWalletIncomeRecord();
        return $model->update($id, $data);
    }
}