<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/24
 * Time: 17:35
 */

namespace App\Domain\Find;

use App\Model\Find\USER as ModelUSER;


class USER {

    public function insert($data){

        $model = new ModelUSER();
        return $model->insert($data);
    }

    public function get($id){
        $model = new ModelUSER();
        return $model->get($id);
    }

    public function getUserByOpenid($openid){

        $model = new ModelUSER();
        return $model->getUserByOpenid($openid);

    }

}