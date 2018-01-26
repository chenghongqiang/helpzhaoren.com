<?php
/**
 * User: kewin.cheng
 * Date: 2018/1/26
 * Time: 11:07
 */

namespace App\Model\Task;

use PhalApi\Model\NotORMModel as NotORM;

class TaskProgress extends NotORM {

    protected function getTableName($id) {
        return 'task_progress';
    }



}