<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/28
 * Time: 16:20
 */
namespace App\Model\Find;

use PhalApi\Model\NotORMModel as NotORM;

class IntroRecord extends NotORM{

    protected function getTableName() {
        return 'intro_record';
    }



}