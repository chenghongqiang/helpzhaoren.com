<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/24
 * Time: 17:27
 */

namespace App\Model\Find;

use PhalApi\Model\NotORMModel as NotORM;

/**
    #用户表
    CREATE TABLE `phal_user` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `openid` VARCHAR(50) NOT NULL COMMENT '用户openid',
        `headurl` VARCHAR(128) NOT NULL COMMENT '微信图像',
        `nickname` VARCHAR(50) NOT NULL COMMENT '微信昵称',
        `wallet` int(10) DEFAULT 0 COMMENT '用户钱包，初始为0',
        `credit` int(10) DEFAULT 0 COMMENT '信誉积分，初始为0',
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

    public function getUserByOpenid($openid){

        $conn = $this->getORM()
            ->select('openId, avatarUrl, nickName, wallet')
            ->where('openId', $openid);

        if(is_array($openid)){
            return $conn->fetchAll();
        }else{
            return $conn->fetchOne();

        }

    }

    public function updateWallet($openId, $money, $type=1){
        //type=1 钱包入账 type=2钱包出账
        if($type == 1){
            $literal = new \NotORM_Literal("wallet + {$money}");
        }else if($type == 2){
            $literal = new \NotORM_Literal("wallet - {$money}");
        }
        $user = $this->getORM();
        return $user->where('openId', $openId)->update(array('wallet' => $literal));

    }




}