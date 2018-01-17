<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/28
 * Time: 16:23
 */

namespace App\Domain\Find;

use App\Model\Find\FormRecord as ModelFormRecord;

class FormRecord {

    public function insert($data){
        $model = new ModelFormRecord;
        return $model->insert($data);
    }

    public function get($id){
        $model = new ModelFormRecord();
        return $model->get($id);
    }

    public function update($id, $newData) {
        $model = new ModelFormRecord();
        return $model->update($id, $newData);
    }

    public function getFormRecord($state=1){
        $model = new ModelFormRecord();
        return $model->getFormRecord($state);
    }
}