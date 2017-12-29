<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/28
 * Time: 16:23
 */

namespace App\Domain\Find;

use App\Model\Find\IntroRecord as ModelIntroRecord;

class IntroRecord {

    public function insert($data){
        $model = new ModelIntroRecord;
        return $model->insert($data);
    }

    public function get($id){
        $model = new ModelIntroRecord();
        return $model->get($id);
    }

    public function update($id, $newData) {
        $model = new ModelIntroRecord();
        return $model->update($id, $newData);
    }
}