<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/28
 * Time: 16:23
 */

namespace App\Domain\Find;

use App\Model\Find\IntroSuccessRecord as ModelIntroSuccessRecord;

class IntroSuccessRecord {

    public function insert($data){
        $model = new ModelIntroSuccessRecord;
        return $model->insert($data);
    }

    public function get($id){
        $model = new ModelIntroSuccessRecord();
        return $model->get($id);
    }
}