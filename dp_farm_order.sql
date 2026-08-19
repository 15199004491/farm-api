# Host: localhost  (Version: 5.5.53)
# Date: 2026-08-13 19:19:21
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "dp_farm_order"
#

DROP TABLE IF EXISTS `dp_farm_order`;
CREATE TABLE `dp_farm_order` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` varchar(11) DEFAULT NULL COMMENT '创建时间',
  `msg` varchar(100) DEFAULT NULL COMMENT '备注信息',
  `money` varchar(11) DEFAULT NULL COMMENT '金额',
  `open_id` varchar(30) DEFAULT NULL,
  `prepay_id` varchar(60) DEFAULT NULL COMMENT '微信支付订单号',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';
