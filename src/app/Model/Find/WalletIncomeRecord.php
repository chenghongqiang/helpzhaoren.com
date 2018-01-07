<?php
/**
 * Created by PhpStorm.
 * User: kewin.cheng
 * Date: 18/1/7
 * Time: PM5:17
 */
namespace App\Model\Find;

use PhalApi\Model\NotORMModel as NotORM;

class WallIncomeRecord extends NotORM{

    protected function getTableName($id) {
        return 'wallet_income_record';
    }


}