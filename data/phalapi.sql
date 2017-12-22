
DROP TABLE IF EXISTS `tbl_curd`;
CREATE TABLE `tbl_curd` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL,
  `content` text,
  `state` tinyint(4) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `tbl_curd` VALUES ('1', 'PhalApi', '欢迎使用PhalApi 2.x 版本!', '0', '2017-07-08 12:09:43');
INSERT INTO `tbl_curd` VALUES ('2', '版本更新', '主要改用composer和命名空间，并遵循psr-4规范。', '1', '2017-07-08 12:10:58');

CREATE DATABASE `phalapi`;

#用户表
CREATE TABLE `phal_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `openid` VARCHAR(50) NOT NULL COMMENT '用户openid',

  `wallet` int(10) DEFAULT 0 COMMENT '用户钱包，初始为0',
  `credit` int(10) DEFAULT 0 COMMENT '信誉积分，初始为0',
  `create_time` datetime DEFAULT NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#用户钱包体现记录表
CREATE TABLE `phal_user_wallet_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `openid` VARCHAR(50) NOT NULL COMMENT '用户openid',

  `money` int(10) DEFAULT 0 COMMENT '用户钱包，初始为0',
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
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#引荐记录表 被引荐人只会有一个
CREATE TABLE `phal_intro_record` (
  `id` int(10) NOT NULL COMMENT '找人记录表id',
  `openid` VARCHAR(50) NOT NULL COMMENT '用户openid',
  `wx_introducers_code` VARCHAR(50) NOT NULL COMMENT '发起人微信号',
  `intro_state` tinyint(4) DEFAULT NULL COMMENT '引荐者所属人  1.引荐人 2.被引荐人',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




