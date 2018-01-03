<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/25
 * Time: 11:30
 */
namespace App\Model\Find;

use PhalApi\Model\NotORMModel as NotORM;

/**
 * Class RECORD
 * @package App\Model\Find
 *
    #用户找人记录表
        CREATE TABLE `phal_oper_record` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `openid` VARCHAR(50) NOT NULL COMMENT '用户openid',
        `wx_self_code` VARCHAR(50) NOT NULL COMMENT '发起人微信号',
        `money` int(10) NOT NULL COMMENT '红包金额',
        `intro` VARCHAR(200) NOT NULL COMMENT '找人描述',
        `code` VARCHAR(6) NOT NULL COMMENT '找人码 限定6位字符',
        `oper_state` tinyint(4) DEFAULT NULL COMMENT '记录状态  -1.删除 1.进行中 2.过期失效 3.引荐成功',
        `create_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

    #引荐记录表 被引荐人只会有一个
    CREATE TABLE `phal_intro_record` (
        `id` int(10) NOT NULL COMMENT '找人记录表id',
        `openid` VARCHAR(50) NOT NULL COMMENT '用户openid',
        `wx_introducers_code` VARCHAR(50) NOT NULL COMMENT '发起人微信号',
        `intro_state` tinyint(4) DEFAULT NULL COMMENT '引荐者所属人  1.引荐人 2.被引荐人',
        `create_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'

    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

 */
class RECORD extends NotORM {

    const PROGRESS = 1;
    const TIMEOUT = 2;
    const SUCCESS = 3;

    protected function getTableName($id) {
        return 'oper_record';
    }

    public function getOperRecord($id){
        $rs = \PhalApi\DI()->notorm->oper_record
            ->select('wx_self_code, money, intro, code, oper_state,
                intro_record.wx_introducer_code, intro_record.intro_state')
            ->where('id', $id)
            ->fetchAll();

        return $rs;
    }

    public function getRecordsByOpenId($openId){
        return $this->getORM()
            ->select('id, intro, money', 'code', 'oper_state', 'create_time')
            ->where('openId', $openId)
            ->fetchAll();
    }

    /**
     * 通过找人码查找找人记录
     * @param $code
     */
    public function getRecordByCode($code){
        return $this->getORM()
            ->select('id, intro, money', 'create_time')
            ->where(array('code' => $code, 'oper_state'=> self::PROGRESS))
            ->fetchRow();
    }

}