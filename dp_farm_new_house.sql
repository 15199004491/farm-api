# Host: localhost  (Version: 5.5.53)
# Date: 2026-08-15 18:06:51
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "dp_farm_new_house"
#

DROP TABLE IF EXISTS `dp_farm_new_house`;
CREATE TABLE `dp_farm_new_house` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `img_url` varchar(150) DEFAULT NULL COMMENT '新房图片',
  `per_price` varchar(10) DEFAULT NULL COMMENT '价格',
  `shape` varchar(1200) DEFAULT NULL COMMENT '户型',
  `state` varchar(5) DEFAULT NULL COMMENT '销售状态：在售，待售，售罄',
  `name` varchar(50) DEFAULT NULL COMMENT '楼盘名称',
  `location` varchar(150) DEFAULT NULL COMMENT '位置',
  `mobile` varchar(11) DEFAULT NULL COMMENT '联系电话',
  `explain` varchar(200) DEFAULT NULL COMMENT '说明',
  `area` varchar(60) DEFAULT NULL COMMENT '信息发布所在地',
  `area_name` varchar(50) DEFAULT NULL COMMENT '信息发布所在地名',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `publisher` varchar(11) DEFAULT NULL COMMENT '发布者',
  `start` int(10) DEFAULT NULL COMMENT '开始时间',
  `end` int(10) DEFAULT NULL COMMENT '结束时间',
  `target_area` varchar(1200) DEFAULT NULL COMMENT '目标区域',
  `meal` varchar(10) DEFAULT NULL COMMENT '套餐',
  `payMoney` varchar(10) DEFAULT NULL COMMENT '支付金额',
  `count` int(11) DEFAULT NULL COMMENT '浏览量',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';
