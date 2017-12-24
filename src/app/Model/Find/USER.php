<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/24
 * Time: 17:27
 */

namespace App\Model\Find;

use PhalApi\Model\NotORMModel as NotORM;

/**
    #�û���
    CREATE TABLE `phal_user` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `openid` VARCHAR(50) NOT NULL COMMENT '�û�openid',
        `headurl` VARCHAR(128) NOT NULL COMMENT '΢��ͼ��',
        `nickname` VARCHAR(50) NOT NULL COMMENT '΢���ǳ�',
        `wallet` int(10) DEFAULT 0 COMMENT '�û�Ǯ������ʼΪ0',
        `credit` int(10) DEFAULT 0 COMMENT '�������֣���ʼΪ0',
        `create_time` datetime DEFAULT NULL,

        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
 *
 *
 */
class USER extends NotORM {

    protected function getTableName($id) {
        return 'user';
    }

    public function getUserByOpenid(){
        
    }


}