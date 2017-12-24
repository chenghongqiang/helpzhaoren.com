CREATE DATABASE `phalapi`;

use `phalapi`;

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

#用户钱包提现记录表
CREATE TABLE `phal_user_wallet_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `openid` VARCHAR(50) NOT NULL COMMENT '用户openid',

  `money` int(10) DEFAULT 0 COMMENT '本次提现金额',
  `create_time` datetime DEFAULT NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#用户找人记录表
CREATE TABLE `phal_oper_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `openid` VARCHAR(50) NOT NULL COMMENT '用户openid',
  `wx_self_code` VARCHAR(50) NOT NULL COMMENT '发起人微信号',
  `money` int(10) NOT NULL COMMENT '红包金额',
  `intro` VARCHAR(200) NOT NULL COMMENT '找人描述',
  `code` VARCHAR(6) NOT NULL COMMENT '找人码 限定6位字符',
  `oper_state` tinyint(4) DEFAULT NULL COMMENT '记录状态  -1.删除 1.进行中 2.过期失效 3.引荐成功',
  `create_time` datetime DEFAULT NULL COMMENT '记录创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#引荐记录表 被引荐人只会有一个
CREATE TABLE `phal_intro_record` (
  `id` int(10) NOT NULL COMMENT '找人记录表id',
  `openid` VARCHAR(50) NOT NULL COMMENT '用户openid',
  `wx_introducers_code` VARCHAR(50) NOT NULL COMMENT '发起人微信号',
  `intro_state` tinyint(4) DEFAULT NULL COMMENT '引荐者所属人  1.引荐人 2.被引荐人',
  `create_time` datetime DEFAULT NULL

) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




