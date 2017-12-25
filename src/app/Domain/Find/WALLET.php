<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/25
 * Time: 13:35
 */

namespace App\Domain\Find;

use App\Model\Find\WALLET as ModelWALLET;

class WALLET{

    public function insert($data){

        $model = new ModelWALLET();
        return $model->insert($data);
    }

    public function get($id){
        $model = new ModelWALLET();
        return $model->get($id);
    }

    public function getWalletRecord($openid){
        $model = new ModelWALLET();
        return $model->getWalletRecordByOpenid($openid);
    }

}