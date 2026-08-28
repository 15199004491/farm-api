# Host: localhost  (Version: 5.5.53)
# Date: 2026-08-28 12:43:42
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "dp_farm_factory"
#

DROP TABLE IF EXISTS `dp_farm_factory`;
CREATE TABLE `dp_farm_factory` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `open_id` varchar(128) DEFAULT NULL COMMENT '发布者',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `info` varchar(1200) DEFAULT NULL COMMENT '品类信息',
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机号',
  `location` varchar(150) DEFAULT NULL COMMENT '加工厂实际位置',
  `name` varchar(50) DEFAULT NULL COMMENT '加工厂名称',
  `license` varchar(150) DEFAULT NULL COMMENT '营业执照',
  `identification` int(1) DEFAULT NULL COMMENT '//认证状态: 空--未认证//0--已提交待认证,1--认证成功，2--认证失败',
  `show_mobile` int(1) DEFAULT '1' COMMENT '0--不展示；1--展示',
  `notice` varchar(200) DEFAULT NULL COMMENT '通知消息',
  `count` int(11) DEFAULT '0' COMMENT '访问量',
  `id_card` varchar(150) DEFAULT NULL COMMENT '身份证图片',
  `account` varchar(50) DEFAULT NULL COMMENT '企业的对公账户',
  `today_count` int(11) DEFAULT '0' COMMENT '今日访客量',
  `visit_time` varchar(11) DEFAULT NULL COMMENT '访客时间',
  `start_time` int(10) DEFAULT NULL COMMENT '展示开始时间',
  `end_time` int(10) DEFAULT NULL COMMENT '展示结束时间',
  `invite_code` int(6) DEFAULT NULL COMMENT '邀请码',
  `category` varchar(1200) DEFAULT NULL COMMENT '品类',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='收购';
