<?php
/**
 * User: kewin.cheng
 * Date: 2018/1/26
 * Time: 11:10
 */

namespace App\Domain\Task;

use App\Model\Task\TaskProgress as ModelTaskProgress;

class TaskProgress {

    public function insert($data){
        $model = new ModelTaskProgress;
        return $model->insert($data);
    }

    public function get($id){
        $model = new ModelTaskProgress();
        return $model->get($id);
    }

    public function update($id, $newData) {
        $model = new ModelTaskProgress();
        return $model->update($id, $newData);
    }

}