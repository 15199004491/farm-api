# Host: localhost  (Version: 5.5.53)
# Date: 2026-08-27 16:23:17
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "dp_admin_access"
#

DROP TABLE IF EXISTS `dp_admin_access`;
CREATE TABLE `dp_admin_access` (
  `module` varchar(16) NOT NULL DEFAULT '' COMMENT '模型名称',
  `group` varchar(16) NOT NULL DEFAULT '' COMMENT '权限分组标识',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `nid` varchar(16) NOT NULL DEFAULT '' COMMENT '授权节点id',
  `tag` varchar(16) NOT NULL DEFAULT '' COMMENT '分组标签'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='统一授权表';

#
# Structure for table "dp_admin_action"
#

DROP TABLE IF EXISTS `dp_admin_action`;
CREATE TABLE `dp_admin_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(16) NOT NULL DEFAULT '' COMMENT '所属模块名',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '行为唯一标识',
  `title` varchar(80) NOT NULL DEFAULT '' COMMENT '行为标题',
  `remark` varchar(128) NOT NULL DEFAULT '' COMMENT '行为描述',
  `rule` text NOT NULL COMMENT '行为规则',
  `log` text NOT NULL COMMENT '日志规则',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COMMENT='系统行为表';

#
# Structure for table "dp_admin_attachment"
#

DROP TABLE IF EXISTS `dp_admin_attachment`;
CREATE TABLE `dp_admin_attachment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '文件名',
  `module` varchar(32) NOT NULL DEFAULT '' COMMENT '模块名，由哪个模块上传的',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径',
  `thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图路径',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件链接',
  `mime` varchar(128) NOT NULL DEFAULT '' COMMENT '文件mime类型',
  `ext` char(8) NOT NULL DEFAULT '' COMMENT '文件类型',
  `size` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT 'sha1 散列值',
  `driver` varchar(16) NOT NULL DEFAULT 'local' COMMENT '上传驱动',
  `download` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `width` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '图片宽度',
  `height` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '图片高度',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表';

#
# Structure for table "dp_admin_config"
#

DROP TABLE IF EXISTS `dp_admin_config`;
CREATE TABLE `dp_admin_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '名称',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '标题',
  `group` varchar(32) NOT NULL DEFAULT '' COMMENT '配置分组',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '类型',
  `value` text NOT NULL COMMENT '配置值',
  `options` text NOT NULL COMMENT '配置项',
  `tips` varchar(256) NOT NULL DEFAULT '' COMMENT '配置提示',
  `ajax_url` varchar(256) NOT NULL DEFAULT '' COMMENT '联动下拉框ajax地址',
  `next_items` varchar(256) NOT NULL DEFAULT '' COMMENT '联动下拉框的下级下拉框名，多个以逗号隔开',
  `param` varchar(32) NOT NULL DEFAULT '' COMMENT '联动下拉框请求参数名',
  `format` varchar(32) NOT NULL DEFAULT '' COMMENT '格式，用于格式文本',
  `table` varchar(32) NOT NULL DEFAULT '' COMMENT '表名，只用于快速联动类型',
  `level` tinyint(2) unsigned NOT NULL DEFAULT '2' COMMENT '联动级别，只用于快速联动类型',
  `key` varchar(32) NOT NULL DEFAULT '' COMMENT '键字段，只用于快速联动类型',
  `option` varchar(32) NOT NULL DEFAULT '' COMMENT '值字段，只用于快速联动类型',
  `pid` varchar(32) NOT NULL DEFAULT '' COMMENT '父级id字段，只用于快速联动类型',
  `ak` varchar(32) NOT NULL DEFAULT '' COMMENT '百度地图appkey',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='系统配置表';

#
# Structure for table "dp_admin_hook"
#

DROP TABLE IF EXISTS `dp_admin_hook`;
CREATE TABLE `dp_admin_hook` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `plugin` varchar(32) NOT NULL DEFAULT '' COMMENT '钩子来自哪个插件',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '钩子描述',
  `system` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否为系统钩子',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='钩子表';

#
# Structure for table "dp_admin_hook_plugin"
#

DROP TABLE IF EXISTS `dp_admin_hook_plugin`;
CREATE TABLE `dp_admin_hook_plugin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hook` varchar(32) NOT NULL DEFAULT '' COMMENT '钩子id',
  `plugin` varchar(32) NOT NULL DEFAULT '' COMMENT '插件标识',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(11) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='钩子-插件对应表';

#
# Structure for table "dp_admin_icon"
#

DROP TABLE IF EXISTS `dp_admin_icon`;
CREATE TABLE `dp_admin_icon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '图标名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '图标css地址',
  `prefix` varchar(32) NOT NULL DEFAULT '' COMMENT '图标前缀',
  `font_family` varchar(32) NOT NULL DEFAULT '' COMMENT '字体名',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图标表';

#
# Structure for table "dp_admin_icon_list"
#

DROP TABLE IF EXISTS `dp_admin_icon_list`;
CREATE TABLE `dp_admin_icon_list` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `icon_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属图标id',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '图标标题',
  `class` varchar(255) NOT NULL DEFAULT '' COMMENT '图标类名',
  `code` varchar(128) NOT NULL DEFAULT '' COMMENT '图标关键词',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='详细图标列表';

#
# Structure for table "dp_admin_log"
#

DROP TABLE IF EXISTS `dp_admin_log`;
CREATE TABLE `dp_admin_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `action_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '行为id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '执行用户id',
  `action_ip` bigint(20) NOT NULL COMMENT '执行行为者ip',
  `model` varchar(50) NOT NULL DEFAULT '' COMMENT '触发行为的表',
  `record_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '触发行为的数据id',
  `remark` longtext NOT NULL COMMENT '日志备注',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  PRIMARY KEY (`id`),
  KEY `action_ip_ix` (`action_ip`),
  KEY `action_id_ix` (`action_id`),
  KEY `user_id_ix` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='行为日志表';

#
# Structure for table "dp_admin_menu"
#

DROP TABLE IF EXISTS `dp_admin_menu`;
CREATE TABLE `dp_admin_menu` (
  `id` int(11) unsigned NOT NULL,
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上级菜单id',
  `module` varchar(16) NOT NULL DEFAULT '' COMMENT '模块名称',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '菜单标题',
  `icon` varchar(64) NOT NULL DEFAULT '' COMMENT '菜单图标',
  `url_type` varchar(16) NOT NULL DEFAULT '' COMMENT '链接类型（link：外链，module：模块）',
  `url_value` varchar(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `url_target` varchar(16) NOT NULL DEFAULT '_self' COMMENT '链接打开方式：_blank,_self',
  `online_hide` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '网站上线后是否隐藏',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `system_menu` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否为系统菜单，系统菜单不可删除',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `params` varchar(255) NOT NULL DEFAULT '' COMMENT '参数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='后台菜单表';

#
# Structure for table "dp_admin_module"
#

DROP TABLE IF EXISTS `dp_admin_module`;
CREATE TABLE `dp_admin_module` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '模块名称（标识）',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '模块标题',
  `icon` varchar(64) NOT NULL DEFAULT '' COMMENT '图标',
  `description` text NOT NULL COMMENT '描述',
  `author` varchar(32) NOT NULL DEFAULT '' COMMENT '作者',
  `author_url` varchar(255) NOT NULL DEFAULT '' COMMENT '作者主页',
  `config` text COMMENT '配置信息',
  `access` text COMMENT '授权配置',
  `version` varchar(16) NOT NULL DEFAULT '' COMMENT '版本号',
  `identifier` varchar(64) NOT NULL DEFAULT '' COMMENT '模块唯一标识符',
  `system_module` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否为系统模块',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='模块表';

#
# Structure for table "dp_admin_plugin"
#

DROP TABLE IF EXISTS `dp_admin_plugin`;
CREATE TABLE `dp_admin_plugin` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '插件名称',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '插件标题',
  `icon` varchar(64) NOT NULL DEFAULT '' COMMENT '图标',
  `description` text NOT NULL COMMENT '插件描述',
  `author` varchar(32) NOT NULL DEFAULT '' COMMENT '作者',
  `author_url` varchar(255) NOT NULL DEFAULT '' COMMENT '作者主页',
  `config` text NOT NULL COMMENT '配置信息',
  `version` varchar(16) NOT NULL DEFAULT '' COMMENT '版本号',
  `identifier` varchar(64) NOT NULL DEFAULT '' COMMENT '插件唯一标识符',
  `admin` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台管理',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='插件表';

#
# Structure for table "dp_farm_advertisement"
#

DROP TABLE IF EXISTS `dp_farm_advertisement`;
CREATE TABLE `dp_farm_advertisement` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `start` varchar(20) DEFAULT NULL COMMENT '开始时间',
  `end` varchar(20) DEFAULT NULL COMMENT '结束时间',
  `publisher` varchar(11) DEFAULT NULL COMMENT '发布者',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `img_url` varchar(150) DEFAULT NULL COMMENT '广告图片',
  `page` varchar(20) DEFAULT NULL COMMENT '广告展示在哪个页面',
  `target_area` varchar(1200) NOT NULL DEFAULT '' COMMENT '广告投放目标区域',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Structure for table "dp_farm_car_person"
#

DROP TABLE IF EXISTS `dp_farm_car_person`;
CREATE TABLE `dp_farm_car_person` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '标题',
  `start_time` varchar(20) DEFAULT NULL COMMENT '出发时间',
  `unit` varchar(12) DEFAULT '天' COMMENT '结算单位',
  `gather` varchar(150) DEFAULT NULL COMMENT '出发地点',
  `price` varchar(6) DEFAULT NULL COMMENT '单价',
  `explain` varchar(200) DEFAULT NULL COMMENT '说明',
  `publisher` varchar(11) DEFAULT NULL COMMENT '发布者',
  `area` varchar(60) DEFAULT '8-132团' COMMENT '信息发布所在的团场',
  `top_start` int(10) DEFAULT NULL COMMENT '置顶开始时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `top_end` int(10) DEFAULT NULL COMMENT '置顶结束时间',
  `area_name` varchar(50) DEFAULT NULL COMMENT '地区名称',
  `mobile` varchar(11) DEFAULT NULL COMMENT '联系电话',
  `vehicle_model` varchar(50) DEFAULT NULL COMMENT '车的型号',
  `license_number` varchar(50) DEFAULT NULL COMMENT '车牌号',
  `position` varchar(150) DEFAULT NULL COMMENT '目的地',
  `count` int(11) DEFAULT NULL COMMENT '浏览量',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Structure for table "dp_farm_employ"
#

DROP TABLE IF EXISTS `dp_farm_employ`;
CREATE TABLE `dp_farm_employ` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '标题',
  `start` varchar(20) DEFAULT NULL COMMENT '开始时间',
  `end` varchar(20) DEFAULT NULL COMMENT '结束时间',
  `count_person` int(5) DEFAULT NULL COMMENT '人数',
  `unit` varchar(12) DEFAULT '天' COMMENT '结算单位',
  `gather` varchar(150) DEFAULT NULL COMMENT '集合地点',
  `labour_position` varchar(150) DEFAULT NULL COMMENT '劳动地点',
  `price` varchar(6) DEFAULT NULL COMMENT '单价',
  `explain` varchar(200) DEFAULT NULL COMMENT '说明',
  `publisher` varchar(11) DEFAULT NULL COMMENT '发布者',
  `area` varchar(60) DEFAULT '8-132团' COMMENT '信息发布所在的团场',
  `top_start` int(10) DEFAULT NULL COMMENT '置顶开始时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `top_end` int(10) DEFAULT NULL COMMENT '置顶结束时间',
  `area_name` varchar(50) DEFAULT NULL COMMENT '地区名称',
  `attend` varchar(255) DEFAULT '[]' COMMENT '参加的人的id集合',
  `notice` varchar(300) DEFAULT NULL COMMENT '发送的通知',
  `mobile` varchar(11) DEFAULT NULL COMMENT '联系电话',
  `count` int(11) DEFAULT NULL COMMENT '浏览量',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='招工';

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
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='收购';

#
# Structure for table "dp_farm_goods"
#

DROP TABLE IF EXISTS `dp_farm_goods`;
CREATE TABLE `dp_farm_goods` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `publisher` varchar(11) DEFAULT NULL COMMENT '发布者',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `img_url` varchar(150) DEFAULT NULL COMMENT '农资图片',
  `target_area` varchar(1200) NOT NULL DEFAULT '' COMMENT '广告投放目标区域',
  `explain` varchar(1000) DEFAULT NULL COMMENT '农资描述',
  `title` varchar(20) DEFAULT NULL COMMENT '标题',
  `start` varchar(20) DEFAULT NULL COMMENT '正在展示的初始时间',
  `end` varchar(20) DEFAULT NULL COMMENT '正在展示的结束时间',
  `meal` varchar(1) DEFAULT NULL COMMENT '选择的套餐',
  `money` varchar(6) DEFAULT NULL COMMENT '支付的金额',
  `desc` varchar(200) DEFAULT NULL COMMENT '简短描述',
  `mobile` varchar(11) DEFAULT NULL COMMENT '联系电话',
  `area_name` varchar(2000) DEFAULT NULL COMMENT '发布地区的名字',
  `count` int(11) DEFAULT NULL COMMENT '浏览量',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Structure for table "dp_farm_land"
#

DROP TABLE IF EXISTS `dp_farm_land`;
CREATE TABLE `dp_farm_land` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '标题',
  `start` varchar(20) DEFAULT NULL COMMENT '开始时间',
  `end` varchar(20) DEFAULT NULL COMMENT '结束时间',
  `location` varchar(150) DEFAULT NULL COMMENT '土地所在地点',
  `price` varchar(6) DEFAULT NULL COMMENT '每亩地单价',
  `explain` varchar(200) DEFAULT NULL COMMENT '说明',
  `publisher` varchar(11) DEFAULT NULL COMMENT '发布者',
  `area` varchar(60) DEFAULT '8-132团' COMMENT '信息发布所在的团场',
  `top_start` int(10) DEFAULT NULL COMMENT '置顶开始时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `top_end` int(10) DEFAULT NULL COMMENT '置顶结束时间',
  `acreage` int(11) DEFAULT NULL COMMENT '土地面积',
  `area_name` varchar(50) DEFAULT NULL COMMENT '所在地区的名称',
  `mobile` varchar(11) DEFAULT NULL COMMENT '联系电话',
  `count` int(11) DEFAULT '0' COMMENT '访问量',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Structure for table "dp_farm_machine"
#

DROP TABLE IF EXISTS `dp_farm_machine`;
CREATE TABLE `dp_farm_machine` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '标题',
  `location` varchar(150) DEFAULT NULL COMMENT '土地所在地点',
  `price` varchar(6) DEFAULT NULL COMMENT '每亩地单价',
  `explain` varchar(200) DEFAULT NULL COMMENT '说明',
  `publisher` varchar(11) DEFAULT NULL COMMENT '发布者',
  `area` varchar(60) DEFAULT '8-132团' COMMENT '信息发布所在的团场',
  `top_start` int(10) DEFAULT NULL COMMENT '置顶开始时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `top_end` int(10) DEFAULT NULL COMMENT '置顶结束时间',
  `area_name` varchar(50) DEFAULT NULL COMMENT '所在地区的名称',
  `machine_img` varchar(150) DEFAULT NULL COMMENT '农机图片',
  `mobile` varchar(11) DEFAULT NULL COMMENT '联系电话',
  `work_years` int(2) DEFAULT '1' COMMENT '驾驶年龄',
  `age` int(2) DEFAULT '22' COMMENT '农机作业人员年龄',
  `count` int(11) DEFAULT NULL COMMENT '浏览量',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Structure for table "dp_farm_machine_sale"
#

DROP TABLE IF EXISTS `dp_farm_machine_sale`;
CREATE TABLE `dp_farm_machine_sale` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '标题',
  `location` varchar(150) DEFAULT NULL COMMENT '土地所在地点',
  `price` varchar(6) DEFAULT '面议' COMMENT '农机价格',
  `explain` varchar(200) DEFAULT NULL COMMENT '说明',
  `publisher` varchar(11) DEFAULT NULL COMMENT '发布者',
  `area` varchar(60) DEFAULT '8-132团' COMMENT '信息发布所在的团场',
  `top_start` int(10) DEFAULT NULL COMMENT '置顶开始时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `top_end` int(10) DEFAULT NULL COMMENT '置顶结束时间',
  `area_name` varchar(50) DEFAULT NULL COMMENT '所在地区的名称',
  `machine_img` varchar(150) DEFAULT NULL COMMENT '农机图片',
  `mobile` varchar(11) DEFAULT NULL COMMENT '联系电话',
  `count` int(11) DEFAULT NULL COMMENT '浏览量',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

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

#
# Structure for table "dp_farm_person_car"
#

DROP TABLE IF EXISTS `dp_farm_person_car`;
CREATE TABLE `dp_farm_person_car` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` varchar(20) DEFAULT NULL COMMENT '出发时间',
  `gather` varchar(150) DEFAULT NULL COMMENT '出发地点',
  `explain` varchar(200) DEFAULT NULL COMMENT '说明',
  `publisher` varchar(11) DEFAULT NULL COMMENT '发布者',
  `area` varchar(60) DEFAULT '8-132团' COMMENT '信息发布所在的团场',
  `top_start` int(10) DEFAULT NULL COMMENT '置顶开始时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `top_end` int(10) DEFAULT NULL COMMENT '置顶结束时间',
  `area_name` varchar(50) DEFAULT NULL COMMENT '地区名称',
  `mobile` varchar(11) DEFAULT NULL COMMENT '联系电话',
  `count_person` int(6) DEFAULT NULL COMMENT '人数',
  `position` varchar(150) DEFAULT NULL COMMENT '目的地',
  `count` int(11) DEFAULT NULL COMMENT '浏览量',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Structure for table "dp_farm_purchase"
#

DROP TABLE IF EXISTS `dp_farm_purchase`;
CREATE TABLE `dp_farm_purchase` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(200) DEFAULT NULL COMMENT '说明',
  `open_id` varchar(128) DEFAULT NULL COMMENT '发布者',
  `top_start` int(10) DEFAULT NULL COMMENT '置顶开始时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `top_end` int(10) DEFAULT NULL COMMENT '置顶结束时间',
  `mobile` varchar(15) DEFAULT NULL COMMENT '手机号',
  `count` int(11) DEFAULT NULL COMMENT '浏览量',
  `title` varchar(20) DEFAULT NULL COMMENT '标题',
  `region` varchar(50) DEFAULT NULL COMMENT '地区',
  `categories` varchar(600) DEFAULT NULL COMMENT '品类信息',
  `today_count` int(11) DEFAULT NULL COMMENT '今日访客量',
  `visit_time` varchar(11) DEFAULT NULL COMMENT '访客时间',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='收购';

#
# Structure for table "dp_farm_rent_house"
#

DROP TABLE IF EXISTS `dp_farm_rent_house`;
CREATE TABLE `dp_farm_rent_house` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `rent_image` varchar(100) DEFAULT NULL COMMENT '二手房图片',
  `price` varchar(10) DEFAULT NULL COMMENT '价格',
  `acreage` varchar(10) DEFAULT NULL COMMENT '面积',
  `floor` varchar(10) DEFAULT NULL COMMENT '楼层',
  `name` varchar(8) DEFAULT NULL COMMENT '小区名称',
  `mobile` varchar(15) DEFAULT NULL COMMENT '联系电话',
  `explain` varchar(200) DEFAULT NULL COMMENT '说明',
  `area` varchar(60) DEFAULT NULL COMMENT '房源所在地',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `open_id` varchar(128) DEFAULT NULL COMMENT '发布者',
  `pay_type` varchar(10) DEFAULT NULL COMMENT '付款方式',
  `count` int(11) DEFAULT NULL COMMENT '浏览量',
  `tag_type` varchar(6) DEFAULT NULL COMMENT '合租还是整租',
  `title` varchar(20) DEFAULT NULL COMMENT '标题',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Structure for table "dp_farm_sale"
#

DROP TABLE IF EXISTS `dp_farm_sale`;
CREATE TABLE `dp_farm_sale` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '标题',
  `location` varchar(150) DEFAULT NULL COMMENT '土地所在地点',
  `price` varchar(6) DEFAULT NULL COMMENT '每亩地单价',
  `explain` varchar(200) DEFAULT NULL COMMENT '说明',
  `publisher` varchar(11) DEFAULT NULL COMMENT '发布者',
  `area` varchar(60) DEFAULT '8-132团' COMMENT '信息发布所在的团场',
  `top_start` int(10) DEFAULT NULL COMMENT '置顶开始时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `top_end` int(10) DEFAULT NULL COMMENT '置顶结束时间',
  `amount` int(11) DEFAULT NULL COMMENT '数量',
  `unit` varchar(10) DEFAULT NULL COMMENT '单位',
  `area_name` varchar(50) DEFAULT NULL COMMENT '所在地区的名称',
  `sale_img` varchar(150) DEFAULT NULL COMMENT '农副产品图片',
  `mobile` varchar(11) DEFAULT NULL COMMENT '联系电话',
  `count` int(11) DEFAULT NULL COMMENT '浏览量',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Structure for table "dp_farm_second_house"
#

DROP TABLE IF EXISTS `dp_farm_second_house`;
CREATE TABLE `dp_farm_second_house` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `second_image` varchar(100) DEFAULT NULL COMMENT '二手房图片',
  `price` varchar(10) DEFAULT NULL COMMENT '价格',
  `shape` varchar(10) DEFAULT NULL COMMENT '户型',
  `acreage` varchar(10) DEFAULT NULL COMMENT '面积',
  `floor` varchar(10) DEFAULT NULL COMMENT '楼层',
  `name` varchar(10) DEFAULT NULL COMMENT '小区名称',
  `mobile` varchar(15) DEFAULT NULL COMMENT '联系电话',
  `explain` varchar(200) DEFAULT NULL COMMENT '说明',
  `area` varchar(10) DEFAULT NULL COMMENT '地区',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `open_id` varchar(128) DEFAULT NULL COMMENT '发布者',
  `count` int(11) DEFAULT NULL COMMENT '浏览量',
  `title` varchar(20) DEFAULT NULL COMMENT '标题',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Structure for table "dp_farm_suggest"
#

DROP TABLE IF EXISTS `dp_farm_suggest`;
CREATE TABLE `dp_farm_suggest` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `open_id` varchar(100) DEFAULT NULL COMMENT '发布者',
  `content` varchar(200) DEFAULT NULL COMMENT '发布的内容',
  `update_time` int(11) DEFAULT NULL COMMENT '发布的时间',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Structure for table "dp_farm_user"
#

DROP TABLE IF EXISTS `dp_farm_user`;
CREATE TABLE `dp_farm_user` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` varchar(11) DEFAULT NULL COMMENT '首次登录的时间',
  `open_id` varchar(30) DEFAULT NULL,
  `avatar_url` varchar(150) DEFAULT 'https://mn10086-1325553910.cos.ap-hongkong.myqcloud.com/wxFile%2FJZogn4Bl7Kow26b644af22d4bcf9279a03bcdcef5a01.png' COMMENT '头像',
  `login_mobile` varchar(11) DEFAULT NULL COMMENT '手机号',
  `update_time` varchar(11) DEFAULT NULL COMMENT '最新登录时间',
  `purchase_ids` varchar(100) DEFAULT NULL COMMENT '个人收购信息',
  `second_house_ids` varchar(100) DEFAULT NULL COMMENT '二手房信息',
  `rent_house_ids` varchar(100) DEFAULT NULL COMMENT '租房信息',
  `nick_name` varchar(10) DEFAULT NULL COMMENT '昵称',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Structure for table "dp_mobile_channel"
#

DROP TABLE IF EXISTS `dp_mobile_channel`;
CREATE TABLE `dp_mobile_channel` (
  `fromId` varchar(20) NOT NULL DEFAULT '',
  `toId` varchar(20) NOT NULL DEFAULT '',
  `isRead` int(1) NOT NULL,
  `time` int(10) NOT NULL DEFAULT '0',
  `message` varchar(200) CHARACTER SET utf8 NOT NULL,
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Structure for table "dp_mobile_chat"
#

DROP TABLE IF EXISTS `dp_mobile_chat`;
CREATE TABLE `dp_mobile_chat` (
  `fromId` varchar(11) NOT NULL,
  `toId` varchar(11) NOT NULL,
  `message` varchar(200) NOT NULL,
  `type` varchar(10) NOT NULL,
  `isRead` tinyint(1) NOT NULL DEFAULT '0',
  `time` int(10) NOT NULL DEFAULT '0',
  `channelId` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Structure for table "dp_mobile_message"
#

DROP TABLE IF EXISTS `dp_mobile_message`;
CREATE TABLE `dp_mobile_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fromId` varchar(11) NOT NULL DEFAULT '0' COMMENT '发送消息的用户id',
  `toId` varchar(11) NOT NULL DEFAULT '0' COMMENT '接收消息的用户id',
  `type` int(1) NOT NULL DEFAULT '0' COMMENT '消息分类',
  `message` varchar(200) NOT NULL DEFAULT '' COMMENT '消息内容',
  `isRead` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `channelId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属对话',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='消息表';

#
# Structure for table "dp_mobile_order"
#

DROP TABLE IF EXISTS `dp_mobile_order`;
CREATE TABLE `dp_mobile_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Structure for table "dp_mobile_person"
#

DROP TABLE IF EXISTS `dp_mobile_person`;
CREATE TABLE `dp_mobile_person` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `height` varchar(5) NOT NULL,
  `weight` varchar(5) NOT NULL,
  `level` varchar(10) NOT NULL,
  `birth` int(10) NOT NULL,
  `emotion` varchar(10) NOT NULL,
  `yearMoney` varchar(10) NOT NULL,
  `photoList` varchar(500) NOT NULL,
  `introducer` int(11) DEFAULT NULL,
  `description` varchar(100) NOT NULL,
  `integral` int(5) NOT NULL,
  `status` int(1) NOT NULL,
  `activity` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `update_time` int(10) NOT NULL,
  `create_time` int(10) NOT NULL,
  `gender` int(1) NOT NULL,
  `avatarUrl` varchar(200) NOT NULL,
  `nickName` varchar(20) NOT NULL,
  `open_id` varchar(30) DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='系统行为表';

#
# Structure for table "dp_mobile_version"
#

DROP TABLE IF EXISTS `dp_mobile_version`;
CREATE TABLE `dp_mobile_version` (
  `online` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Structure for table "dp_mobile_wxuser"
#

DROP TABLE IF EXISTS `dp_mobile_wxuser`;
CREATE TABLE `dp_mobile_wxuser` (
  `open_id` varchar(30) DEFAULT NULL,
  `nickName` varchar(20) DEFAULT NULL,
  `avatarUrl` varchar(50) DEFAULT NULL,
  `gender` int(1) DEFAULT NULL,
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='微信登录';
