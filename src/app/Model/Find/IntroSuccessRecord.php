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

    protected function getTableName($id) {
        return 'intro_success_record';
    }

    public function getRecordByRecordId($recordId){
        return $this->getORM()
            ->select('*')
            ->where('recordId', $recordId)
            ->fetchRow();
    }

    /**
     * 根据引荐人或被引荐人微信号查找记录
     * @param $openId
     */
    public function getRecordIdByOpenId($openId){
        return $this->getORM()
            ->select('recordId')
            ->or('introducererOpenId', $openId)
            ->or('introduceredOpenId', $openId)
            ->order('create_time desc')
            ->fetchAll();
    }
}