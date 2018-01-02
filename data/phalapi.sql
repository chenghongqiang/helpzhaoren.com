CREATE DATABASE `phalapi`;

use `phalapi`;

#用户表 openid唯一建 加索引 DECIMAL(5, 1) -99999.9 到 999999.9
CREATE TABLE `phal_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `openId` VARCHAR(50) NOT NULL COMMENT '用户openId',
  `avatarUrl` VARCHAR(256) NOT NULL COMMENT '微信图像',
  `nickName` VARCHAR(50) NOT NULL COMMENT '微信昵称',
  `wallet` DECIMAL(7, 2) DEFAULT 0 COMMENT '用户钱包，初始为0(以元为单位)',
  `credit` int(10) DEFAULT 0 COMMENT '信誉积分，初始为0',
  `state` tinyint(4) NOT NULL DEFAULT 1 COMMENT '用户状态，预留字段',

  `create_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#用户钱包提现记录表
CREATE TABLE `phal_user_wallet_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `openId` VARCHAR(50) NOT NULL COMMENT '用户openid',

  `money` DECIMAL(7, 2) DEFAULT 0 COMMENT '本次提现金额(以元为单位)',
  `create_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#用户找人记录表
CREATE TABLE `phal_oper_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `openId` VARCHAR(50) NOT NULL COMMENT '用户openid',
  `wx_self_code` VARCHAR(50) NOT NULL COMMENT '发起人微信号',
  `money` int NOT NULL COMMENT '红包金额(整数以元为单位)',
  `intro` VARCHAR(200) NOT NULL COMMENT '找人描述',
  `code` VARCHAR(6) NOT NULL COMMENT '找人码 限定6位字符',
  `oper_state` tinyint(4) DEFAULT NULL COMMENT '记录状态  -1.删除 1.进行中 2.过期失效 3.引荐成功 4.引荐失败(被申诉)',
  `create_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#引荐人记录表
CREATE TABLE `phal_intro_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `recordId` int(10) NOT NULL COMMENT '找人记录表id',
  `openId` VARCHAR(50) NOT NULL COMMENT '用户openid',
  `wx_introducer_code` VARCHAR(50) NOT NULL COMMENT '引荐人微信号',
  `intro_state` tinyint(4) DEFAULT 1 COMMENT '引荐者所属人  1.引荐人',
  `create_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',

  PRIMARY KEY (`id`)

) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#引荐被引荐成功表 引荐人、被引荐人只会有一个
#考虑引荐人很多，后期数量大，加了一个引荐人记录表和引荐成功表
CREATE TABLE `phal_intro_success_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `recordId` int(10) NOT NULL COMMENT '找人记录表id',
  `openId` VARCHAR(50) NOT NULL COMMENT '用户openid',
  `wx_introducer_code` VARCHAR(50) NOT NULL COMMENT '引荐人微信号',
  `intro_state` tinyint(4) DEFAULT NULL COMMENT '引荐者所属人  1.引荐人 2.被引荐人',
  `create_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#交易记录
CREATE TABLE `phal_trade_record` (
  `id` int(10) NOT NULL COMMENT '交易记录表id',
  `openId` VARCHAR(50) NOT NULL COMMENT '用户openid',
  `out_trade_no` VARCHAR(32) NOT NULL COMMENT '商户订单号',
  `transaction_id` VARCHAR(32) NOT NULL COMMENT '微信支付订单号',
  `spbill_create_ip` VARCHAR(16) NOT NULL COMMENT '终端IP',
  `total_fee` int NOT NULL COMMENT '订单总金额，单位为分',
  `trade_type` VARCHAR(16) NOT NULL COMMENT '交易类型 JSAPI、NATIVE、APP',
  `time_end` VARCHAR(14) NOT NULL COMMENT '支付完成时间 yyyyMMddHHmmss'

) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




