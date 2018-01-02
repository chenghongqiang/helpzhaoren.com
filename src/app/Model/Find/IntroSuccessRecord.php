<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/29
 * Time: 14:42
 */

namespace App\Model\Find;

use PhalApi\Model\NotORMModel as NotORM;

/**
 * 被推荐人提交数据找人成功记录表
 * Class IntroSuccessRecord
 * @package App\Model\Find
 */
class IntroSuccessRecord extends NotORM{

    protected function getTableName() {
        return 'intro_success_record';
    }
}