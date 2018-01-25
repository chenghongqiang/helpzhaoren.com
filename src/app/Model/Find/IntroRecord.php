<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/28
 * Time: 16:20
 */
namespace App\Model\Find;

use PhalApi\Model\NotORMModel as NotORM;

class IntroRecord extends NotORM{

    protected function getTableName($id) {
        return 'intro_record';
    }

    public function getIntroRecord($recordId, $openId){

        if($recordId === 0) {
            $condition = array(
                'openId' => $openId
            );
        }else{
            $condition = array(
                'recordId' => $recordId,
                'openId' => $openId
            );
        }

        return $this->getORM()
            ->select('id, recordId, wx_introducer_code')
            ->where($condition)
            ->fetchRow();
    }


}