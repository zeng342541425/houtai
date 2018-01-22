/*
Navicat MySQL Data Transfer

Source Server         : linzk
Source Server Version : 50173
Source Host           : 111.231.136.181:3306
Source Database       : weixin

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2018-01-22 14:32:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(10) DEFAULT NULL,
  `pass` varchar(255) DEFAULT NULL,
  `time` varchar(15) DEFAULT NULL,
  `isdel` tinyint(1) DEFAULT '0' COMMENT '0能删除，1不能删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', 'admin', '7fef6171469e80d32c0559f88b377245', '1514627046', '1');

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `time` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL COMMENT '分类id',
  `title` varchar(255) DEFAULT NULL COMMENT '描述',
  `description` varchar(2555) DEFAULT NULL,
  `intro` varchar(2555) DEFAULT NULL COMMENT '简介',
  `content` text,
  `beizhu` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `recommend` tinyint(1) DEFAULT '0' COMMENT '1推荐 0不推荐',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of article
-- ----------------------------
INSERT INTO `article` VALUES ('14', '1514965139', '131', 'aaaaaaaaaaaagggggggggggggggggg', 'aaaaaaaaaaaaagggggggggggggggggggg', 'aaaaaaaaaaaaggggggggggggggggggggg', '<p>ggggggggggggggggggggggggggg&nbsp;<img src=\"http://localhost/tp5admin/public/static/admin/editor/php/upload/20171231/15147116189108.png\"/> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>', 'aaaaaaaaaaaaaaaaagggggggggggggggggg', 'uploads/20171231/3848f3c15d48beeb188c7e924c7953db.png', '1');

-- ----------------------------
-- Table structure for baseinfo
-- ----------------------------
DROP TABLE IF EXISTS `baseinfo`;
CREATE TABLE `baseinfo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `intro` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `time` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of baseinfo
-- ----------------------------
INSERT INTO `baseinfo` VALUES ('1', '5_DFLSGptiWj9Wi8s-GyDopcWXCOGZoVzCBgc6xcdEAa0qIMSKMLXBxovfGcdNEq3_vq5kqHlOhINZ93GWkNBrAWTO4l1tAT18G0gt-K9_bqBeIhTjZ-JCj1gOlLCaVScpFfO1JFzFTbMKVhvRYVTcAGAQNL', 'wer', 'wer', 'wer', 'wer', '1515132139');
INSERT INTO `baseinfo` VALUES ('2', 'kgt8ON7yVITDhtdwci0qeRqYbAALumnQoi9qbZV_p2LL9HHsildNNfW5QB5Db3O2ZfbQnNzwrlm6giIRgRVShQ', 'sdf', 'sdf', 'sdf', 'sdfsdf', '1515063397');

-- ----------------------------
-- Table structure for category
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `list_order` int(10) DEFAULT '10000',
  `description` varchar(255) DEFAULT NULL COMMENT '分类描述',
  `name` varchar(100) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `content` text COMMENT '详情',
  `time` int(11) DEFAULT NULL COMMENT '添加时间',
  `isdel` tinyint(1) DEFAULT '0' COMMENT '能否被删除 1 不能',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=134 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of category
-- ----------------------------
INSERT INTO `category` VALUES ('130', '0', '10000', '新闻顶级分类', '新闻', null, '<p>新闻新闻新闻新闻&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>', '1514715154', '1');
INSERT INTO `category` VALUES ('131', '130', '10000', '企业新闻', '企业新闻', null, '<p>&nbsp; &nbsp; &nbsp; &nbsp; 企业新闻企业新闻企业新闻&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>', '1514715172', '1');
INSERT INTO `category` VALUES ('132', '130', '10000', '111', '行业新闻', null, '<p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;111&nbsp; 1&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</p>', '1514715187', '1');

-- ----------------------------
-- Table structure for imgc
-- ----------------------------
DROP TABLE IF EXISTS `imgc`;
CREATE TABLE `imgc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `isdel` tinyint(1) DEFAULT '0' COMMENT '1 不能删除  0 能删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of imgc
-- ----------------------------
INSERT INTO `imgc` VALUES ('4', '首页轮播图', '333', '1514712758', '1');

-- ----------------------------
-- Table structure for imgl
-- ----------------------------
DROP TABLE IF EXISTS `imgl`;
CREATE TABLE `imgl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `time` varchar(20) DEFAULT NULL,
  `description` varchar(2555) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of imgl
-- ----------------------------
INSERT INTO `imgl` VALUES ('16', '4', 'uploads/20180103/cb2ec2abb55e7bea708e04678ee2e63a.png', '淡淡的', '1514968174', '订单');

-- ----------------------------
-- Table structure for wxuser
-- ----------------------------
DROP TABLE IF EXISTS `wxuser`;
CREATE TABLE `wxuser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `openid` varchar(80) DEFAULT NULL,
  `headimgurl` varchar(200) DEFAULT NULL,
  `nickname` varchar(50) DEFAULT NULL,
  `sex` tinyint(1) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wxuser
-- ----------------------------
INSERT INTO `wxuser` VALUES ('16', 'o5mZbxDZEUp9i0qrrGypJOmaPyC0', 'http://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83eoydrbmKaIwam6jz8lzH2tScafsOqdCDwibW1IMCkM1MHo7HqXat4QFH367GTmR6oUn4s1Zw1QA5nw/0', '　　', '1', '湖南', '衡阳', '中国');
