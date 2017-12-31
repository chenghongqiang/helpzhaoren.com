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

    public function getIntroRecord($recordId, $openId){
        return $this->getORM()
            ->select('id, recordId, wx_introducer_code')
            ->where(array('recordId' => $recordId, 'openId' => $openId))
            ->fetchRow();
    }


}