<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/28
 * Time: 16:20
 */
namespace App\Model\Find;

use PhalApi\Model\NotORMModel as NotORM;

class FormRecord extends NotORM{

    protected function getTableName($id) {
        return 'form_record';
    }

    public function getFormRecord($state){
        return $this->getORM()
            ->select('formId')
            ->where('state', $state)
            ->where('formId != ?', 'the formId is a mock one')
            ->where('create_time < ?', date("Y-m-d h:m:s", strtotime('-7 days')))
            ->limit(3)
            ->fetch();
    }


}