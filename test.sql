# Host: localhost  (Version: 5.5.53)
# Date: 2024-04-14 00:59:17
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
# Data for table "dp_admin_access"
#

/*!40000 ALTER TABLE `dp_admin_access` DISABLE KEYS */;
/*!40000 ALTER TABLE `dp_admin_access` ENABLE KEYS */;

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
# Data for table "dp_admin_action"
#

/*!40000 ALTER TABLE `dp_admin_action` DISABLE KEYS */;
INSERT INTO `dp_admin_action` VALUES (1,'user','user_add','添加用户','添加用户','','[user|get_nickname] 添加了用户：[record|get_nickname]',1,1480156399,1480163853),(2,'user','user_edit','编辑用户','编辑用户','','[user|get_nickname] 编辑了用户：[details]',1,1480164578,1480297748),(3,'user','user_delete','删除用户','删除用户','','[user|get_nickname] 删除了用户：[details]',1,1480168582,1480168616),(4,'user','user_enable','启用用户','启用用户','','[user|get_nickname] 启用了用户：[details]',1,1480169185,1480169185),(5,'user','user_disable','禁用用户','禁用用户','','[user|get_nickname] 禁用了用户：[details]',1,1480169214,1480170581),(6,'user','user_access','用户授权','用户授权','','[user|get_nickname] 对用户：[record|get_nickname] 进行了授权操作。详情：[details]',1,1480221441,1480221563),(7,'user','role_add','添加角色','添加角色','','[user|get_nickname] 添加了角色：[details]',1,1480251473,1480251473),(8,'user','role_edit','编辑角色','编辑角色','','[user|get_nickname] 编辑了角色：[details]',1,1480252369,1480252369),(9,'user','role_delete','删除角色','删除角色','','[user|get_nickname] 删除了角色：[details]',1,1480252580,1480252580),(10,'user','role_enable','启用角色','启用角色','','[user|get_nickname] 启用了角色：[details]',1,1480252620,1480252620),(11,'user','role_disable','禁用角色','禁用角色','','[user|get_nickname] 禁用了角色：[details]',1,1480252651,1480252651),(12,'user','attachment_enable','启用附件','启用附件','','[user|get_nickname] 启用了附件：附件ID([details])',1,1480253226,1480253332),(13,'user','attachment_disable','禁用附件','禁用附件','','[user|get_nickname] 禁用了附件：附件ID([details])',1,1480253267,1480253340),(14,'user','attachment_delete','删除附件','删除附件','','[user|get_nickname] 删除了附件：附件ID([details])',1,1480253323,1480253323),(15,'admin','config_add','添加配置','添加配置','','[user|get_nickname] 添加了配置，[details]',1,1480296196,1480296196),(16,'admin','config_edit','编辑配置','编辑配置','','[user|get_nickname] 编辑了配置：[details]',1,1480296960,1480296960),(17,'admin','config_enable','启用配置','启用配置','','[user|get_nickname] 启用了配置：[details]',1,1480298479,1480298479),(18,'admin','config_disable','禁用配置','禁用配置','','[user|get_nickname] 禁用了配置：[details]',1,1480298506,1480298506),(19,'admin','config_delete','删除配置','删除配置','','[user|get_nickname] 删除了配置：[details]',1,1480298532,1480298532),(20,'admin','database_export','备份数据库','备份数据库','','[user|get_nickname] 备份了数据库：[details]',1,1480298946,1480298946),(21,'admin','database_import','还原数据库','还原数据库','','[user|get_nickname] 还原了数据库：[details]',1,1480301990,1480302022),(22,'admin','database_optimize','优化数据表','优化数据表','','[user|get_nickname] 优化了数据表：[details]',1,1480302616,1480302616),(23,'admin','database_repair','修复数据表','修复数据表','','[user|get_nickname] 修复了数据表：[details]',1,1480302798,1480302798),(24,'admin','database_backup_delete','删除数据库备份','删除数据库备份','','[user|get_nickname] 删除了数据库备份：[details]',1,1480302870,1480302870),(25,'admin','hook_add','添加钩子','添加钩子','','[user|get_nickname] 添加了钩子：[details]',1,1480303198,1480303198),(26,'admin','hook_edit','编辑钩子','编辑钩子','','[user|get_nickname] 编辑了钩子：[details]',1,1480303229,1480303229),(27,'admin','hook_delete','删除钩子','删除钩子','','[user|get_nickname] 删除了钩子：[details]',1,1480303264,1480303264),(28,'admin','hook_enable','启用钩子','启用钩子','','[user|get_nickname] 启用了钩子：[details]',1,1480303294,1480303294),(29,'admin','hook_disable','禁用钩子','禁用钩子','','[user|get_nickname] 禁用了钩子：[details]',1,1480303409,1480303409),(30,'admin','menu_add','添加节点','添加节点','','[user|get_nickname] 添加了节点：[details]',1,1480305468,1480305468),(31,'admin','menu_edit','编辑节点','编辑节点','','[user|get_nickname] 编辑了节点：[details]',1,1480305513,1480305513),(32,'admin','menu_delete','删除节点','删除节点','','[user|get_nickname] 删除了节点：[details]',1,1480305562,1480305562),(33,'admin','menu_enable','启用节点','启用节点','','[user|get_nickname] 启用了节点：[details]',1,1480305630,1480305630),(34,'admin','menu_disable','禁用节点','禁用节点','','[user|get_nickname] 禁用了节点：[details]',1,1480305659,1480305659),(35,'admin','module_install','安装模块','安装模块','','[user|get_nickname] 安装了模块：[details]',1,1480307558,1480307558),(36,'admin','module_uninstall','卸载模块','卸载模块','','[user|get_nickname] 卸载了模块：[details]',1,1480307588,1480307588),(37,'admin','module_enable','启用模块','启用模块','','[user|get_nickname] 启用了模块：[details]',1,1480307618,1480307618),(38,'admin','module_disable','禁用模块','禁用模块','','[user|get_nickname] 禁用了模块：[details]',1,1480307653,1480307653),(39,'admin','module_export','导出模块','导出模块','','[user|get_nickname] 导出了模块：[details]',1,1480307682,1480307682),(40,'admin','packet_install','安装数据包','安装数据包','','[user|get_nickname] 安装了数据包：[details]',1,1480308342,1480308342),(41,'admin','packet_uninstall','卸载数据包','卸载数据包','','[user|get_nickname] 卸载了数据包：[details]',1,1480308372,1480308372),(42,'admin','system_config_update','更新系统设置','更新系统设置','','[user|get_nickname] 更新了系统设置：[details]',1,1480309555,1480309642);
/*!40000 ALTER TABLE `dp_admin_action` ENABLE KEYS */;

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
# Data for table "dp_admin_attachment"
#

/*!40000 ALTER TABLE `dp_admin_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `dp_admin_attachment` ENABLE KEYS */;

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
# Data for table "dp_admin_config"
#

/*!40000 ALTER TABLE `dp_admin_config` DISABLE KEYS */;
INSERT INTO `dp_admin_config` VALUES (1,'web_site_status','站点开关','base','switch','1','','站点关闭后将不能访问，后台可正常登录','','','','','',2,'','','','',1475240395,1477403914,1,1),(2,'web_site_title','站点标题','base','text','海豚PHP','','调用方式：<code>config(\'web_site_title\')</code>','','','','','',2,'','','','',1475240646,1477710341,2,1),(3,'web_site_slogan','站点标语','base','text','海豚PHP，极简、极速、极致','','站点口号，调用方式：<code>config(\'web_site_slogan\')</code>','','','','','',2,'','','','',1475240994,1477710357,3,1),(4,'web_site_logo','站点LOGO','base','image','','','','','','','','',2,'','','','',1475241067,1475241067,4,1),(5,'web_site_description','站点描述','base','textarea','','','网站描述，有利于搜索引擎抓取相关信息','','','','','',2,'','','','',1475241186,1475241186,6,1),(6,'web_site_keywords','站点关键词','base','text','海豚PHP、PHP开发框架、后台框架','','网站搜索引擎关键字','','','','','',2,'','','','',1475241328,1475241328,7,1),(7,'web_site_copyright','版权信息','base','text','Copyright © 2015-2017 DolphinPHP All rights reserved.','','调用方式：<code>config(\'web_site_copyright\')</code>','','','','','',2,'','','','',1475241416,1477710383,8,1),(8,'web_site_icp','备案信息','base','text','','','调用方式：<code>config(\'web_site_icp\')</code>','','','','','',2,'','','','',1475241441,1477710441,9,1),(9,'web_site_statistics','站点统计','base','textarea','','','网站统计代码，支持百度、Google、cnzz等，调用方式：<code>config(\'web_site_statistics\')</code>','','','','','',2,'','','','',1475241498,1477710455,10,1),(10,'config_group','配置分组','system','array','base:基本\r\nsystem:系统\r\nupload:上传\r\ndevelop:开发\r\ndatabase:数据库','','','','','','','',2,'','','','',1475241716,1477649446,100,1),(11,'form_item_type','配置类型','system','array','text:单行文本\r\ntextarea:多行文本\r\nstatic:静态文本\r\npassword:密码\r\ncheckbox:复选框\r\nradio:单选按钮\r\ndate:日期\r\ndatetime:日期+时间\r\nhidden:隐藏\r\nswitch:开关\r\narray:数组\r\nselect:下拉框\r\nlinkage:普通联动下拉框\r\nlinkages:快速联动下拉框\r\nimage:单张图片\r\nimages:多张图片\r\nfile:单个文件\r\nfiles:多个文件\r\nueditor:UEditor 编辑器\r\nwangeditor:wangEditor 编辑器\r\neditormd:markdown 编辑器\r\nckeditor:ckeditor 编辑器\r\nicon:字体图标\r\ntags:标签\r\nnumber:数字\r\nbmap:百度地图\r\ncolorpicker:取色器\r\njcrop:图片裁剪\r\nmasked:格式文本\r\nrange:范围\r\ntime:时间','','','','','','','',2,'','','','',1475241835,1495853193,100,1),(12,'upload_file_size','文件上传大小限制','upload','text','0','','0为不限制大小，单位：kb','','','','','',2,'','','','',1475241897,1477663520,100,1),(13,'upload_file_ext','允许上传的文件后缀','upload','tags','doc,docx,xls,xlsx,ppt,pptx,pdf,wps,txt,rar,zip,gz,bz2,7z','','多个后缀用逗号隔开，不填写则不限制类型','','','','','',2,'','','','',1475241975,1477649489,100,1),(14,'upload_image_size','图片上传大小限制','upload','text','0','','0为不限制大小，单位：kb','','','','','',2,'','','','',1475242015,1477663529,100,1),(15,'upload_image_ext','允许上传的图片后缀','upload','tags','gif,jpg,jpeg,bmp,png','','多个后缀用逗号隔开，不填写则不限制类型','','','','','',2,'','','','',1475242056,1477649506,100,1),(16,'list_rows','分页数量','system','number','20','','每页的记录数','','','','','',2,'','','','',1475242066,1476074507,101,1),(17,'system_color','后台配色方案','system','radio','default','default:Default\r\namethyst:Amethyst\r\ncity:City\r\nflat:Flat\r\nmodern:Modern\r\nsmooth:Smooth','','','','','','',2,'','','','',1475250066,1477316689,102,1),(18,'develop_mode','开发模式','develop','radio','1','0:关闭\r\n1:开启','','','','','','',2,'','','','',1476864205,1476864231,100,1),(19,'app_trace','显示页面Trace','develop','radio','0','0:否\r\n1:是','','','','','','',2,'','','','',1476866355,1476866355,100,1),(21,'data_backup_path','数据库备份根路径','database','text','../data/','','路径必须以 / 结尾','','','','','',2,'','','','',1477017745,1477018467,100,1),(22,'data_backup_part_size','数据库备份卷大小','database','text','20971520','','该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M','','','','','',2,'','','','',1477017886,1477017886,100,1),(23,'data_backup_compress','数据库备份文件是否启用压缩','database','radio','1','0:否\r\n1:是','压缩备份文件需要PHP环境支持 <code>gzopen</code>, <code>gzwrite</code>函数','','','','','',2,'','','','',1477017978,1477018172,100,1),(24,'data_backup_compress_level','数据库备份文件压缩级别','database','radio','9','1:最低\r\n4:一般\r\n9:最高','数据库备份文件的压缩级别，该配置在开启压缩时生效','','','','','',2,'','','','',1477018083,1477018083,100,1),(25,'top_menu_max','顶部导航模块数量','system','text','10','','设置顶部导航默认显示的模块数量','','','','','',2,'','','','',1477579289,1477579289,103,1),(26,'web_site_logo_text','站点LOGO文字','base','image','','','','','','','','',2,'','','','',1477620643,1477620643,5,1),(27,'upload_image_thumb','缩略图尺寸','upload','text','','','不填写则不生成缩略图，如需生成 <code>300x300</code> 的缩略图，则填写 <code>300,300</code> ，请注意，逗号必须是英文逗号','','','','','',2,'','','','',1477644150,1477649513,100,1),(28,'upload_image_thumb_type','缩略图裁剪类型','upload','radio','1','1:等比例缩放\r\n2:缩放后填充\r\n3:居中裁剪\r\n4:左上角裁剪\r\n5:右下角裁剪\r\n6:固定尺寸缩放','该项配置只有在启用生成缩略图时才生效','','','','','',2,'','','','',1477646271,1477649521,100,1),(29,'upload_thumb_water','添加水印','upload','switch','0','','','','','','','',2,'','','','',1477649648,1477649648,100,1),(30,'upload_thumb_water_pic','水印图片','upload','image','','','只有开启水印功能才生效','','','','','',2,'','','','',1477656390,1477656390,100,1),(31,'upload_thumb_water_position','水印位置','upload','radio','9','1:左上角\r\n2:上居中\r\n3:右上角\r\n4:左居中\r\n5:居中\r\n6:右居中\r\n7:左下角\r\n8:下居中\r\n9:右下角','只有开启水印功能才生效','','','','','',2,'','','','',1477656528,1477656528,100,1),(32,'upload_thumb_water_alpha','水印透明度','upload','text','50','','请输入0~100之间的数字，数字越小，透明度越高','','','','','',2,'','','','',1477656714,1477661309,100,1),(33,'wipe_cache_type','清除缓存类型','system','checkbox','TEMP_PATH','TEMP_PATH:应用缓存\r\nLOG_PATH:应用日志\r\nCACHE_PATH:项目模板缓存','清除缓存时，要删除的缓存类型','','','','','',2,'','','','',1477727305,1477727305,100,1),(34,'captcha_signin','后台验证码开关','system','switch','0','','后台登录时是否需要验证码','','','','','',2,'','','','',1478771958,1478771958,99,1),(35,'home_default_module','前台默认模块','system','select','index','','前台默认访问的模块，该模块必须有Index控制器和index方法','','','','','',0,'','','','',1486714723,1486715620,104,1),(36,'minify_status','开启minify','system','switch','0','','开启minify会压缩合并js、css文件，可以减少资源请求次数，如果不支持minify，可关闭','','','','','',0,'','','','',1487035843,1487035843,99,1),(37,'upload_driver','上传驱动','upload','radio','local','local:本地','图片或文件上传驱动','','','','','',0,'','','','',1501488567,1501490821,100,1),(38,'system_log','系统日志','system','switch','1','','是否开启系统日志功能','','','','','',0,'','','','',1512635391,1512635391,99,1),(39,'asset_version','资源版本号','develop','text','20180327','','可通过修改版号强制用户更新静态文件','','','','','',0,'','','','',1522143239,1522143239,100,1);
/*!40000 ALTER TABLE `dp_admin_config` ENABLE KEYS */;

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
# Data for table "dp_admin_hook"
#

/*!40000 ALTER TABLE `dp_admin_hook` DISABLE KEYS */;
INSERT INTO `dp_admin_hook` VALUES (1,'admin_index','','后台首页',1,1468174214,1477757518,1),(2,'plugin_index_tab_list','','插件扩展tab钩子',1,1468174214,1468174214,1),(3,'module_index_tab_list','','模块扩展tab钩子',1,1468174214,1468174214,1),(4,'page_tips','','每个页面的提示',1,1468174214,1468174214,1),(5,'signin_footer','','登录页面底部钩子',1,1479269315,1479269315,1),(6,'signin_captcha','','登录页面验证码钩子',1,1479269315,1479269315,1),(7,'signin','','登录控制器钩子',1,1479386875,1479386875,1),(8,'upload_attachment','','附件上传钩子',1,1501493808,1501493808,1),(9,'page_plugin_js','','页面插件js钩子',1,1503633591,1503633591,1),(10,'page_plugin_css','','页面插件css钩子',1,1503633591,1503633591,1),(11,'signin_sso','','单点登录钩子',1,1503633591,1503633591,1),(12,'signout_sso','','单点退出钩子',1,1503633591,1503633591,1),(13,'user_add','','添加用户钩子',1,1503633591,1503633591,1),(14,'user_edit','','编辑用户钩子',1,1503633591,1503633591,1),(15,'user_delete','','删除用户钩子',1,1503633591,1503633591,1),(16,'user_enable','','启用用户钩子',1,1503633591,1503633591,1),(17,'user_disable','','禁用用户钩子',1,1503633591,1503633591,1);
/*!40000 ALTER TABLE `dp_admin_hook` ENABLE KEYS */;

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
# Data for table "dp_admin_hook_plugin"
#

/*!40000 ALTER TABLE `dp_admin_hook_plugin` DISABLE KEYS */;
INSERT INTO `dp_admin_hook_plugin` VALUES (1,'admin_index','SystemInfo',1477757503,1477757503,1,1),(2,'admin_index','DevTeam',1477755780,1477755780,2,1);
/*!40000 ALTER TABLE `dp_admin_hook_plugin` ENABLE KEYS */;

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
# Data for table "dp_admin_icon"
#

/*!40000 ALTER TABLE `dp_admin_icon` DISABLE KEYS */;
/*!40000 ALTER TABLE `dp_admin_icon` ENABLE KEYS */;

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
# Data for table "dp_admin_icon_list"
#

/*!40000 ALTER TABLE `dp_admin_icon_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `dp_admin_icon_list` ENABLE KEYS */;

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
# Data for table "dp_admin_log"
#

/*!40000 ALTER TABLE `dp_admin_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `dp_admin_log` ENABLE KEYS */;

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
# Data for table "dp_admin_menu"
#

/*!40000 ALTER TABLE `dp_admin_menu` DISABLE KEYS */;
INSERT INTO `dp_admin_menu` VALUES (1,0,'admin','首页','fa fa-fw fa-home','module_admin','admin/index/index','_self',0,1467617722,1477710540,1,1,1,''),(2,1,'admin','快捷操作','fa fa-fw fa-folder-open-o','module_admin','','_self',0,1467618170,1477710695,1,1,1,''),(3,2,'admin','清空缓存','fa fa-fw fa-trash-o','module_admin','admin/index/wipecache','_self',0,1467618273,1489049773,3,1,1,''),(4,0,'admin','系统','fa fa-fw fa-gear','module_admin','admin/system/index','_self',0,1467618361,1477710540,2,1,1,''),(5,4,'admin','系统功能','si si-wrench','module_admin','','_self',0,1467618441,1477710695,1,1,1,''),(6,5,'admin','系统设置','fa fa-fw fa-wrench','module_admin','admin/system/index','_self',0,1467618490,1477710695,1,1,1,''),(7,5,'admin','配置管理','fa fa-fw fa-gears','module_admin','admin/config/index','_self',0,1467618618,1477710695,2,1,1,''),(8,7,'admin','新增','','module_admin','admin/config/add','_self',0,1467618648,1477710695,1,1,1,''),(9,7,'admin','编辑','','module_admin','admin/config/edit','_self',0,1467619566,1477710695,2,1,1,''),(10,7,'admin','删除','','module_admin','admin/config/delete','_self',0,1467619583,1477710695,3,1,1,''),(11,7,'admin','启用','','module_admin','admin/config/enable','_self',0,1467619609,1477710695,4,1,1,''),(12,7,'admin','禁用','','module_admin','admin/config/disable','_self',0,1467619637,1477710695,5,1,1,''),(13,5,'admin','节点管理','fa fa-fw fa-bars','module_admin','admin/menu/index','_self',0,1467619882,1477710695,3,1,1,''),(14,13,'admin','新增','','module_admin','admin/menu/add','_self',0,1467619902,1477710695,1,1,1,''),(15,13,'admin','编辑','','module_admin','admin/menu/edit','_self',0,1467620331,1477710695,2,1,1,''),(16,13,'admin','删除','','module_admin','admin/menu/delete','_self',0,1467620363,1477710695,3,1,1,''),(17,13,'admin','启用','','module_admin','admin/menu/enable','_self',0,1467620386,1477710695,4,1,1,''),(18,13,'admin','禁用','','module_admin','admin/menu/disable','_self',0,1467620404,1477710695,5,1,1,''),(19,68,'user','权限管理','fa fa-fw fa-key','module_admin','','_self',0,1467688065,1477710702,1,1,1,''),(20,19,'user','用户管理','fa fa-fw fa-user','module_admin','user/index/index','_self',0,1467688137,1477710702,1,1,1,''),(21,20,'user','新增','','module_admin','user/index/add','_self',0,1467688177,1477710702,1,1,1,''),(22,20,'user','编辑','','module_admin','user/index/edit','_self',0,1467688202,1477710702,2,1,1,''),(23,20,'user','删除','','module_admin','user/index/delete','_self',0,1467688219,1477710702,3,1,1,''),(24,20,'user','启用','','module_admin','user/index/enable','_self',0,1467688238,1477710702,4,1,1,''),(25,20,'user','禁用','','module_admin','user/index/disable','_self',0,1467688256,1477710702,5,1,1,''),(32,4,'admin','扩展中心','si si-social-dropbox','module_admin','','_self',0,1467688853,1477710695,2,1,1,''),(33,32,'admin','模块管理','fa fa-fw fa-th-large','module_admin','admin/module/index','_self',0,1467689008,1477710695,1,1,1,''),(34,33,'admin','导入','','module_admin','admin/module/import','_self',0,1467689153,1477710695,1,1,1,''),(35,33,'admin','导出','','module_admin','admin/module/export','_self',0,1467689173,1477710695,2,1,1,''),(36,33,'admin','安装','','module_admin','admin/module/install','_self',0,1467689192,1477710695,3,1,1,''),(37,33,'admin','卸载','','module_admin','admin/module/uninstall','_self',0,1467689241,1477710695,4,1,1,''),(38,33,'admin','启用','','module_admin','admin/module/enable','_self',0,1467689294,1477710695,5,1,1,''),(39,33,'admin','禁用','','module_admin','admin/module/disable','_self',0,1467689312,1477710695,6,1,1,''),(40,33,'admin','更新','','module_admin','admin/module/update','_self',0,1467689341,1477710695,7,1,1,''),(41,32,'admin','插件管理','fa fa-fw fa-puzzle-piece','module_admin','admin/plugin/index','_self',0,1467689527,1477710695,2,1,1,''),(42,41,'admin','导入','','module_admin','admin/plugin/import','_self',0,1467689650,1477710695,1,1,1,''),(43,41,'admin','导出','','module_admin','admin/plugin/export','_self',0,1467689665,1477710695,2,1,1,''),(44,41,'admin','安装','','module_admin','admin/plugin/install','_self',0,1467689680,1477710695,3,1,1,''),(45,41,'admin','卸载','','module_admin','admin/plugin/uninstall','_self',0,1467689700,1477710695,4,1,1,''),(46,41,'admin','启用','','module_admin','admin/plugin/enable','_self',0,1467689730,1477710695,5,1,1,''),(47,41,'admin','禁用','','module_admin','admin/plugin/disable','_self',0,1467689747,1477710695,6,1,1,''),(48,41,'admin','设置','','module_admin','admin/plugin/config','_self',0,1467689789,1477710695,7,1,1,''),(49,41,'admin','管理','','module_admin','admin/plugin/manage','_self',0,1467689846,1477710695,8,1,1,''),(50,5,'admin','附件管理','fa fa-fw fa-cloud-upload','module_admin','admin/attachment/index','_self',0,1467690161,1477710695,4,1,1,''),(51,70,'admin','文件上传','','module_admin','admin/attachment/upload','_self',0,1467690240,1489049773,1,1,1,''),(52,50,'admin','下载','','module_admin','admin/attachment/download','_self',0,1467690334,1477710695,2,1,1,''),(53,50,'admin','启用','','module_admin','admin/attachment/enable','_self',0,1467690352,1477710695,3,1,1,''),(54,50,'admin','禁用','','module_admin','admin/attachment/disable','_self',0,1467690369,1477710695,4,1,1,''),(55,50,'admin','删除','','module_admin','admin/attachment/delete','_self',0,1467690396,1477710695,5,1,1,''),(56,41,'admin','删除','','module_admin','admin/plugin/delete','_self',0,1467858065,1477710695,11,1,1,''),(57,41,'admin','编辑','','module_admin','admin/plugin/edit','_self',0,1467858092,1477710695,10,1,1,''),(60,41,'admin','新增','','module_admin','admin/plugin/add','_self',0,1467858421,1477710695,9,1,1,''),(61,41,'admin','执行','','module_admin','admin/plugin/execute','_self',0,1467879016,1477710695,14,1,1,''),(62,13,'admin','保存','','module_admin','admin/menu/save','_self',0,1468073039,1477710695,6,1,1,''),(64,5,'admin','系统日志','fa fa-fw fa-book','module_admin','admin/log/index','_self',0,1476111944,1477710695,6,0,1,''),(65,5,'admin','数据库管理','fa fa-fw fa-database','module_admin','admin/database/index','_self',0,1476111992,1477710695,8,0,1,''),(66,32,'admin','数据包管理','fa fa-fw fa-database','module_admin','admin/packet/index','_self',0,1476112326,1477710695,4,0,1,''),(67,19,'user','角色管理','fa fa-fw fa-users','module_admin','user/role/index','_self',0,1476113025,1477710702,3,0,1,''),(68,0,'user','用户','fa fa-fw fa-user','module_admin','user/index/index','_self',0,1476193348,1477710540,3,0,1,''),(69,32,'admin','钩子管理','fa fa-fw fa-anchor','module_admin','admin/hook/index','_self',0,1476236193,1477710695,3,0,1,''),(70,2,'admin','后台首页','fa fa-fw fa-tachometer','module_admin','admin/index/index','_self',0,1476237472,1489049773,1,0,1,''),(71,67,'user','新增','','module_admin','user/role/add','_self',0,1476256935,1477710702,1,0,1,''),(72,67,'user','编辑','','module_admin','user/role/edit','_self',0,1476256968,1477710702,2,0,1,''),(73,67,'user','删除','','module_admin','user/role/delete','_self',0,1476256993,1477710702,3,0,1,''),(74,67,'user','启用','','module_admin','user/role/enable','_self',0,1476257023,1477710702,4,0,1,''),(75,67,'user','禁用','','module_admin','user/role/disable','_self',0,1476257046,1477710702,5,0,1,''),(76,20,'user','授权','','module_admin','user/index/access','_self',0,1476375187,1477710702,6,0,1,''),(77,69,'admin','新增','','module_admin','admin/hook/add','_self',0,1476668971,1477710695,1,0,1,''),(78,69,'admin','编辑','','module_admin','admin/hook/edit','_self',0,1476669006,1477710695,2,0,1,''),(79,69,'admin','删除','','module_admin','admin/hook/delete','_self',0,1476669375,1477710695,3,0,1,''),(80,69,'admin','启用','','module_admin','admin/hook/enable','_self',0,1476669427,1477710695,4,0,1,''),(81,69,'admin','禁用','','module_admin','admin/hook/disable','_self',0,1476669564,1477710695,5,0,1,''),(183,66,'admin','安装','','module_admin','admin/packet/install','_self',0,1476851362,1477710695,1,0,1,''),(184,66,'admin','卸载','','module_admin','admin/packet/uninstall','_self',0,1476851382,1477710695,2,0,1,''),(185,5,'admin','行为管理','fa fa-fw fa-bug','module_admin','admin/action/index','_self',0,1476882441,1477710695,7,0,1,''),(186,185,'admin','新增','','module_admin','admin/action/add','_self',0,1476884439,1477710695,1,0,1,''),(187,185,'admin','编辑','','module_admin','admin/action/edit','_self',0,1476884464,1477710695,2,0,1,''),(188,185,'admin','启用','','module_admin','admin/action/enable','_self',0,1476884493,1477710695,3,0,1,''),(189,185,'admin','禁用','','module_admin','admin/action/disable','_self',0,1476884534,1477710695,4,0,1,''),(190,185,'admin','删除','','module_admin','admin/action/delete','_self',0,1476884551,1477710695,5,0,1,''),(191,65,'admin','备份数据库','','module_admin','admin/database/export','_self',0,1476972746,1477710695,1,0,1,''),(192,65,'admin','还原数据库','','module_admin','admin/database/import','_self',0,1476972772,1477710695,2,0,1,''),(193,65,'admin','优化表','','module_admin','admin/database/optimize','_self',0,1476972800,1477710695,3,0,1,''),(194,65,'admin','修复表','','module_admin','admin/database/repair','_self',0,1476972825,1477710695,4,0,1,''),(195,65,'admin','删除备份','','module_admin','admin/database/delete','_self',0,1476973457,1477710695,5,0,1,''),(207,69,'admin','快速编辑','','module_admin','admin/hook/quickedit','_self',0,1477713770,1477713770,100,0,1,''),(208,7,'admin','快速编辑','','module_admin','admin/config/quickedit','_self',0,1477713808,1477713808,100,0,1,''),(209,185,'admin','快速编辑','','module_admin','admin/action/quickedit','_self',0,1477713939,1477713939,100,0,1,''),(210,41,'admin','快速编辑','','module_admin','admin/plugin/quickedit','_self',0,1477713981,1477713981,100,0,1,''),(211,64,'admin','日志详情','','module_admin','admin/log/details','_self',0,1480299320,1480299320,100,0,1,''),(212,2,'admin','个人设置','fa fa-fw fa-user','module_admin','admin/index/profile','_self',0,1489049767,1489049773,2,0,1,''),(213,70,'admin','检查版本更新','','module_admin','admin/index/checkupdate','_self',0,1490588610,1490588610,100,0,1,''),(214,68,'user','消息管理','fa fa-fw fa-comments-o','module_admin','','_self',0,1520492129,1520492129,100,0,1,''),(215,214,'user','消息列表','fa fa-fw fa-th-list','module_admin','user/message/index','_self',0,1520492195,1520492195,100,0,1,''),(216,215,'user','新增','','module_admin','user/message/add','_self',0,1520492195,1520492195,100,0,1,''),(217,215,'user','编辑','','module_admin','user/message/edit','_self',0,1520492195,1520492195,100,0,1,''),(218,215,'user','删除','','module_admin','user/message/delete','_self',0,1520492195,1520492195,100,0,1,''),(219,215,'user','启用','','module_admin','user/message/enable','_self',0,1520492195,1520492195,100,0,1,''),(220,215,'user','禁用','','module_admin','user/message/disable','_self',0,1520492195,1520492195,100,0,1,''),(221,215,'user','快速编辑','','module_admin','user/message/quickedit','_self',0,1520492195,1520492195,100,0,1,''),(222,2,'admin','消息中心','fa fa-fw fa-comments-o','module_admin','admin/message/index','_self',0,1520495992,1520496254,100,0,1,''),(223,222,'admin','删除','','module_admin','admin/message/delete','_self',0,1520495992,1520496263,100,0,1,''),(224,222,'admin','启用','','module_admin','admin/message/enable','_self',0,1520495992,1520496270,100,0,1,''),(225,32,'admin','图标管理','fa fa-fw fa-tint','module_admin','admin/icon/index','_self',0,1520908295,1520908295,100,0,1,''),(226,225,'admin','新增','','module_admin','admin/icon/add','_self',0,1520908295,1520908295,100,0,1,''),(227,225,'admin','编辑','','module_admin','admin/icon/edit','_self',0,1520908295,1520908295,100,0,1,''),(228,225,'admin','删除','','module_admin','admin/icon/delete','_self',0,1520908295,1520908295,100,0,1,''),(229,225,'admin','启用','','module_admin','admin/icon/enable','_self',0,1520908295,1520908295,100,0,1,''),(230,225,'admin','禁用','','module_admin','admin/icon/disable','_self',0,1520908295,1520908295,100,0,1,''),(231,225,'admin','快速编辑','','module_admin','admin/icon/quickedit','_self',0,1520908295,1520908295,100,0,1,''),(232,225,'admin','图标列表','','module_admin','admin/icon/items','_self',0,1520923368,1520923368,100,0,1,''),(233,225,'admin','更新图标','','module_admin','admin/icon/reload','_self',0,1520931908,1520931908,100,0,1,''),(234,20,'user','快速编辑','','module_admin','user/index/quickedit','_self',0,1526028258,1526028258,100,0,1,''),(235,67,'user','快速编辑','','module_admin','user/role/quickedit','_self',0,1526028282,1526028282,100,0,1,''),(236,6,'admin','快速编辑','','module_admin','admin/system/quickedit','_self',0,1559054310,1559054310,100,0,1,'');
/*!40000 ALTER TABLE `dp_admin_menu` ENABLE KEYS */;

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
# Data for table "dp_admin_module"
#

/*!40000 ALTER TABLE `dp_admin_module` DISABLE KEYS */;
/*!40000 ALTER TABLE `dp_admin_module` ENABLE KEYS */;

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
# Data for table "dp_admin_plugin"
#

/*!40000 ALTER TABLE `dp_admin_plugin` DISABLE KEYS */;
/*!40000 ALTER TABLE `dp_admin_plugin` ENABLE KEYS */;

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
  `area` varchar(3) DEFAULT '132' COMMENT '信息发布所在的团场',
  `top_start` int(10) DEFAULT NULL COMMENT '置顶开始时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `top_end` int(10) DEFAULT NULL COMMENT '置顶结束时间',
  `area_name` varchar(50) DEFAULT NULL COMMENT '地区名称',
  `attend` varchar(255) DEFAULT '[]' COMMENT '参加的人的id集合',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='招工';

#
# Data for table "dp_farm_employ"
#

/*!40000 ALTER TABLE `dp_farm_employ` DISABLE KEYS */;
INSERT INTO `dp_farm_employ` VALUES (16,'撒大声地111','2024-02-29 00:28','2024-06-30 00:28',23,'公斤','{\"name\":\"康源财富中心\",\"latitude\":43.843046,\"longitude\":87.572684}','{\"name\":\"浙北大酒店\",\"latitude\":43.849413,\"longitude\":87.570018}','12','恍恍惚惚hiu尽快工会法很尬第几集干哈干哈发发发金卡个会计法发卡机','15199004491','132',1713023386,1713015653,1744559386,'农八师132团','[9]'),(17,'130团找人干活','2024-04-02 00:20','2024-05-02 00:21',22,'天','{\"name\":\"康源财富中心\",\"latitude\":43.843046,\"longitude\":87.572684}','{\"name\":\"广汇京都小区\",\"latitude\":43.843819,\"longitude\":87.577218}','32','阿达撒打发撒的发生法师打发撒打发三大发啥打法是打发阿斯蒂芬撒的发多少','15199004491','130',NULL,1711988487,NULL,'农八师130团','[]'),(19,'知道168','2024-03-10 21:48','2024-05-16 21:48',2,'天','{\"name\":\"恒和大厦(北四东路)\",\"latitude\":44.306223,\"longitude\":86.074234}','{\"name\":\"石河子瑞丽国际商务宾馆\",\"latitude\":44.304283,\"longitude\":86.051326}','23','阿斯顿发送到发送到发撒旦法手动阀手动阀手动阀134234154','19399183170','132',1712922236,1713017725,1746973785,'农八师132团','[11,9]');
/*!40000 ALTER TABLE `dp_farm_employ` ENABLE KEYS */;

#
# Structure for table "dp_farm_factory"
#

DROP TABLE IF EXISTS `dp_farm_factory`;
CREATE TABLE `dp_farm_factory` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `explain` varchar(500) DEFAULT NULL COMMENT '说明',
  `publisher` varchar(11) DEFAULT NULL COMMENT '发布者',
  `top_start` int(10) DEFAULT NULL COMMENT '置顶开始时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `top_end` int(10) DEFAULT NULL COMMENT '置顶结束时间',
  `info` varchar(1200) DEFAULT NULL COMMENT '品类信息',
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机号',
  `location` varchar(150) DEFAULT NULL COMMENT '加工厂实际位置',
  `name` varchar(50) DEFAULT NULL COMMENT '加工厂名称',
  `license` varchar(100) DEFAULT NULL COMMENT '营业执照',
  `identification` int(1) DEFAULT NULL COMMENT '认证状态://0--待认证,1--待打款,2--已打款，认证中,3--认证成功，4--已有发布信息',
  `show_mobile` int(1) DEFAULT '1' COMMENT '0--不展示；1--展示',
  `state` int(1) DEFAULT '1' COMMENT '0--暂停，1--正常收购',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='收购';

#
# Data for table "dp_farm_factory"
#

/*!40000 ALTER TABLE `dp_farm_factory` DISABLE KEYS */;
INSERT INTO `dp_farm_factory` VALUES (14,'阿阿斯顿法师打发412341234234','19399183170',1712844994,1713019486,1723385794,'[{\"type\":\"西瓜\",\"price\":\"2\",\"unit\":\"公斤\",\"intro\":\"阿斯顿发的发的发的发生大发大沙发是打发\"},{\"type\":\"阿斯顿发\",\"price\":\"32\",\"unit\":\"公斤\",\"intro\":\"阿打发手动阀手动阀手动阀是打发\"},{\"type\":\"325\",\"price\":\"32\",\"unit\":\"公斤\",\"intro\":\"额外人情味儿请问二位二位\"}]','19399183170','{\"name\":\"蜀湘雅阁酒店\",\"latitude\":44.30705,\"longitude\":86.079353}','测试111','factory/6614ed7da3a5e.png',4,0,0),(17,NULL,NULL,NULL,1712672095,NULL,'[{\"type\":\"棉花\",\"price\":\"12\",\"unit\":\"公斤\",\"intro\":\"阿斯顿发送到发送到发\"}]',NULL,NULL,NULL,NULL,NULL,1,1);
/*!40000 ALTER TABLE `dp_farm_factory` ENABLE KEYS */;

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
  `area` varchar(3) DEFAULT '132' COMMENT '信息发布所在的团场',
  `top_start` int(10) DEFAULT NULL COMMENT '置顶开始时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `top_end` int(10) DEFAULT NULL COMMENT '置顶结束时间',
  `acreage` int(11) DEFAULT NULL COMMENT '土地面积',
  `area_name` varchar(50) DEFAULT NULL COMMENT '所在地区的名称',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Data for table "dp_farm_land"
#

/*!40000 ALTER TABLE `dp_farm_land` DISABLE KEYS */;
INSERT INTO `dp_farm_land` VALUES (3,'但是发斯蒂芬萨达','2024-01','2029-03','{\"name\":\"新疆图书馆\",\"latitude\":43.840719,\"longitude\":87.57462}','234','撒打发撒的发生打法是的发送到发手动阀阿斯顿发士大夫啊','15199004491','132',1713023833,1711820110,1715615833,23234,NULL),(4,'在撒打发斯蒂芬','2023-03','2032-03','{\"name\":\"新疆图书馆\",\"latitude\":43.840719,\"longitude\":87.57462}','345','问题伍尔特我让他瓦尔塔问他瓦尔塔瓦尔塔瓦尔塔瓦尔塔伍尔特','15199004491','132',NULL,1711821261,NULL,2352345,NULL),(5,'阿斯顿发手动阀撒的发生','2024-03','2024-05','{\"name\":\"重庆老太婆摊摊面(北四东路店)\",\"latitude\":44.30705,\"longitude\":86.077945}','234','人防撒的发生代发手动阀三大发三大发都发放大啊都是法师打发撒的发生代发撒大萨达发','15199004491','132',1713023915,1713023910,1718812673,2342,'农八师132团'),(6,'啊沙发斯蒂芬撒的发生1234','2024-06','2026-12','{\"name\":\"石河子市人民政府北\",\"latitude\":44.30653,\"longitude\":86.07893}','21','打法是的发送到发手动阀是打法是的发送到发稍等234','19399183170','132',1712845012,1713019526,1715437012,12332,'农八师132团');
/*!40000 ALTER TABLE `dp_farm_land` ENABLE KEYS */;

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
  `area` varchar(3) DEFAULT '132' COMMENT '信息发布所在的团场',
  `top_start` int(10) DEFAULT NULL COMMENT '置顶开始时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `top_end` int(10) DEFAULT NULL COMMENT '置顶结束时间',
  `area_name` varchar(50) DEFAULT NULL COMMENT '所在地区的名称',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Data for table "dp_farm_machine"
#

/*!40000 ALTER TABLE `dp_farm_machine` DISABLE KEYS */;
INSERT INTO `dp_farm_machine` VALUES (1,'阿士大夫撒地方','{\"name\":\"中国工商银行(北京南路支行)\",\"latitude\":43.844155,\"longitude\":87.573893}','2314','早点发斯蒂芬撒的发生都发啥打法阿斯顿发送到发送到发说的发生的','15199004491','132',NULL,1711898531,NULL,NULL),(2,'阿士大夫撒地方','{\"name\":\"中国工商银行(北京南路支行)\",\"latitude\":43.844155,\"longitude\":87.573893}','2314','早点发斯蒂芬撒的发生都发啥打法阿斯顿发送到发送到发说的发生的','15199004491','132',1713023994,1711898537,1747151961,NULL),(3,'阿士大夫撒地方','{\"name\":\"中国工商银行(北京南路支行)\",\"latitude\":43.844155,\"longitude\":87.573893}','2314','早点发斯蒂芬撒的发生都发啥打法阿斯顿发送到发送到发说的发生的','15199004491','132',NULL,1711898550,NULL,NULL),(5,'双缝干涉发给电饭锅地方21321','{\"name\":\"石河子市东苑群岛花园(北四东路北)\",\"latitude\":44.307063,\"longitude\":86.081867}','213413','我认为儿童瓦尔塔瓦尔塔瓦尔塔瓦尔塔瓦尔塔瓦尔塔瓦尔塔','19399183170','132',1712845036,1713019666,1715437036,'农八师132团');
/*!40000 ALTER TABLE `dp_farm_machine` ENABLE KEYS */;

#
# Structure for table "dp_farm_purchase"
#

DROP TABLE IF EXISTS `dp_farm_purchase`;
CREATE TABLE `dp_farm_purchase` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `explain` varchar(200) DEFAULT NULL COMMENT '说明',
  `publisher` varchar(11) DEFAULT NULL COMMENT '发布者',
  `area` varchar(3) DEFAULT '132' COMMENT '信息发布所在的团场',
  `top_start` int(10) DEFAULT NULL COMMENT '置顶开始时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `top_end` int(10) DEFAULT NULL COMMENT '置顶结束时间',
  `info` varchar(1200) DEFAULT NULL COMMENT '品类信息',
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机号',
  `location` varchar(150) DEFAULT NULL COMMENT '地址',
  `area_name` varchar(50) DEFAULT NULL COMMENT '所在地区的名称',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='收购';

#
# Data for table "dp_farm_purchase"
#

/*!40000 ALTER TABLE `dp_farm_purchase` DISABLE KEYS */;
INSERT INTO `dp_farm_purchase` VALUES (1,'阿斯顿发的发撒的发生都发啥打法是打发手打发撒代发','15199004491','132',NULL,1712416592,NULL,'[{\"type\":\"棉花\",\"price\":\"12\",\"unit\":\"公斤\",\"intro\":\"阿斯蒂芬撒地方撒地方撒打发说法\"}]','15199004491',NULL,NULL),(2,'这是顶部的通知3544356','15199004491','132',NULL,1713024904,NULL,'[{\"type\":\"这是新增的品类\",\"price\":\"12\",\"unit\":\"公斤\",\"intro\":\"阿斯顿发士大夫\"},{\"type\":\"西瓜\",\"price\":\"21\",\"unit\":\"公斤\",\"intro\":\"阿斯顿发送到发送到发撒旦法手动阀是的\"}]','15199004491','{\"name\":\"中国农业银行(石河子东苑兵团支行)\",\"latitude\":44.307059,\"longitude\":86.082535}','农八师132团'),(3,'大师傅撒旦法撒的发生代发撒打发三大发啥打法顶部的说明','15199004491','132',1712846033,1712416924,1715438033,'[{\"type\":\"西瓜\",\"price\":\"1\",\"unit\":\"公斤\",\"intro\":\"阿阿斯顿发的发的发生的放大啊\"},{\"type\":\"新增的品列\",\"price\":\"12\",\"unit\":\"公斤\",\"intro\":\"阿斯顿发生的发送的\"}]','15199004491',NULL,NULL),(7,'水电费撒的发生地方撒旦法手打发撒都发啥打法萨达发撒代发12312','19399183170','132',NULL,1713019439,NULL,'[{\"type\":\"3333\",\"price\":\"12\",\"unit\":\"公斤\",\"intro\":\"阿斯顿法师打发撒的发生代发撒打发送达方\",\"id\":0},{\"type\":\"666\",\"price\":\"21\",\"unit\":\"斤\",\"intro\":\"阿第三方撒旦法撒旦法\",\"id\":1}]','19399183170','{\"name\":\"石河子市人民政府北\",\"latitude\":44.30653,\"longitude\":86.07893}','农八师132团'),(9,'aSASDSADFSADSDFASDfasdfasdfass丰富的法定所发生的法师打发三大发啥打法是都发啥打法萨达','19399183170','132',NULL,1712764404,NULL,'[{\"type\":\"棉花\",\"price\":\"12\",\"unit\":\"公斤\",\"intro\":\"阿斯顿发手动阀是的法师打发撒打发\"}]','19399183170','{\"name\":\"石河子市人民政府北\",\"latitude\":44.30653,\"longitude\":86.07893}',NULL),(10,'手动阀手动阀手动阀手动阀三大发啥打法是都发啥打法',NULL,'132',1712846014,1712764542,1715438014,'[{\"type\":\"棉花\",\"price\":\"12\",\"unit\":\"公斤\",\"intro\":\"阿打发打发手动阀的说法三大发多发点\"}]','19399183170','{\"name\":\"金兰香牛肉面(开发区店)\",\"latitude\":44.307047,\"longitude\":86.081656}','农八师132团'),(11,'水电费撒的发生地方撒旦法手打发撒都发啥打法萨达发撒代发1232343',NULL,'132',NULL,1713019180,NULL,'[{\"type\":\"3253245\",\"price\":\"21\",\"unit\":\"斤\",\"intro\":\"阿第三方撒旦法撒旦法\"},{\"type\":\"测试777\",\"price\":\"12\",\"unit\":\"斤\",\"intro\":\"阿凡达发生的发生的法师打发三大发啥打法\"}]','19399183170','{\"name\":\"重庆老太婆摊摊面(北四东路店)\",\"latitude\":44.30705,\"longitude\":86.077945}','农八师132团'),(12,'水电费撒的发生地方撒旦法手打发撒都发啥打法萨达发撒代发',NULL,'132',NULL,1713019258,NULL,'[{\"type\":\"测试777\",\"price\":\"12\",\"unit\":\"公斤\",\"intro\":\"阿斯顿法师打发撒的发生代发撒打发送达方\",\"id\":0},{\"type\":\"3253245\",\"price\":\"21\",\"unit\":\"斤\",\"intro\":\"阿第三方撒旦法撒旦法\"}]','19399183170','{\"name\":\"石河子市人民政府北\",\"latitude\":44.30653,\"longitude\":86.07893}','农八师132团');
/*!40000 ALTER TABLE `dp_farm_purchase` ENABLE KEYS */;

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
  `area` varchar(3) DEFAULT '132' COMMENT '信息发布所在的团场',
  `top_start` int(10) DEFAULT NULL COMMENT '置顶开始时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `top_end` int(10) DEFAULT NULL COMMENT '置顶结束时间',
  `count` int(11) DEFAULT NULL COMMENT '数量',
  `unit` varchar(10) DEFAULT NULL COMMENT '单位',
  `area_name` varchar(50) DEFAULT NULL COMMENT '所在地区的名称',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Data for table "dp_farm_sale"
#

/*!40000 ALTER TABLE `dp_farm_sale` DISABLE KEYS */;
INSERT INTO `dp_farm_sale` VALUES (1,'撒旦法发','{\"name\":\"康源财富中心\",\"latitude\":43.843046,\"longitude\":87.572684}','12','阿斯顿发射点发射点发射点发射点发啊手动阀手动阀手动阀手动阀阿斯顿发射点阿斯蒂芬','15199004491','132',1713024053,1711906840,1715616053,3000,'克',NULL),(2,'地址发士大夫','{\"name\":\"中厦大厦\",\"latitude\":43.842398,\"longitude\":87.573166}','333','啊手动阀手动阀撒旦发射点发撒打发士大夫撒地方撒旦发射点发撒打发手动阀手动阀阿斯顿发射点发射点发射点发射点发射点发','15199004491','132',NULL,1711906874,NULL,23434,'亩',NULL),(3,'啊去问问12332114341','{\"name\":\"石河子市42小区(北一东路北)\",\"latitude\":44.29368435033423,\"longitude\":86.06886619049071}','121234','蓄势待发额企鹅额辅导费打撒方法是放大司法送达发撒代发','19399183170','132',NULL,1713019031,NULL,2147483647,'斤','农八师132团'),(4,'啊去问问','{\"name\":\"\",\"latitude\":44.30653,\"longitude\":86.07893}','12','蓄势待发额企鹅额辅导费打撒方法是放大司法送达发撒代发','19399183170','132',1712844939,1712771078,1746972913,2324,'斤','农八师132团');
/*!40000 ALTER TABLE `dp_farm_sale` ENABLE KEYS */;

#
# Structure for table "dp_farm_suggest"
#

DROP TABLE IF EXISTS `dp_farm_suggest`;
CREATE TABLE `dp_farm_suggest` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机号',
  `publisher` varchar(11) DEFAULT NULL COMMENT '发布者',
  `content` varchar(255) DEFAULT NULL COMMENT '发布的内容',
  `update_time` int(11) DEFAULT NULL COMMENT '发布的时间',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Data for table "dp_farm_suggest"
#

/*!40000 ALTER TABLE `dp_farm_suggest` DISABLE KEYS */;
INSERT INTO `dp_farm_suggest` VALUES (3,'19399183170','19399183170','啊实打实大法师打发手动阀三大发啥打法撒打发是打发',1712848314);
/*!40000 ALTER TABLE `dp_farm_suggest` ENABLE KEYS */;

#
# Structure for table "dp_farm_user"
#

DROP TABLE IF EXISTS `dp_farm_user`;
CREATE TABLE `dp_farm_user` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `attend` varchar(255) DEFAULT '[]' COMMENT '参加',
  `area` int(3) DEFAULT NULL COMMENT '所在地区',
  `create_time` varchar(11) DEFAULT NULL COMMENT '首次登录的时间',
  `open_id` varchar(30) DEFAULT NULL,
  `nick_name` varchar(20) DEFAULT NULL,
  `avatar_url` varchar(50) DEFAULT NULL,
  `gender` int(1) DEFAULT NULL,
  `login_mobile` varchar(11) DEFAULT NULL COMMENT '登录时候的手机号',
  `update_time` varchar(11) DEFAULT NULL COMMENT '最新登录时间',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='招工';

#
# Data for table "dp_farm_user"
#

/*!40000 ALTER TABLE `dp_farm_user` DISABLE KEYS */;
INSERT INTO `dp_farm_user` VALUES (9,'[\"16\",\"19\"]',132,'1712946111','o3_wF49Hy37Jtp9zREV0RPHLWrH0','微信用户','https://thirdwx.qlogo.cn/mmopen/vi_32/POgEwh4mIHO4',1,'19399183170','1713027417'),(11,'[\"19\"]',NULL,'1713000874','o3_wF4z43zwoXVyOW-TgnS9cLWYI','微信用户','https://thirdwx.qlogo.cn/mmopen/vi_32/POgEwh4mIHO4',1,'15199004491','1713015052');
/*!40000 ALTER TABLE `dp_farm_user` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

#
# Data for table "dp_mobile_channel"
#

INSERT INTO `dp_mobile_channel` VALUES ('0','0',1,1661574761,'撒打发斯蒂芬',2),('1','2',1,1662912417,'右边\n',3);

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

#
# Data for table "dp_mobile_chat"
#

INSERT INTO `dp_mobile_chat` VALUES ('1','2','1232134234','1',0,1662910538,1,1),('1','2','阿斯顿发士大夫','1',0,1662912410,1,2),('1','2','右边\n','1',0,1662912417,1,3);

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
# Data for table "dp_mobile_message"
#

/*!40000 ALTER TABLE `dp_mobile_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `dp_mobile_message` ENABLE KEYS */;

#
# Structure for table "dp_mobile_order"
#

DROP TABLE IF EXISTS `dp_mobile_order`;
CREATE TABLE `dp_mobile_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Data for table "dp_mobile_order"
#


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
# Data for table "dp_mobile_person"
#

/*!40000 ALTER TABLE `dp_mobile_person` DISABLE KEYS */;
INSERT INTO `dp_mobile_person` VALUES (1,'','','',0,'','','',NULL,'',0,0,'',0,1662892396,1662892396,1,'https://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLhRH6fwgzxM82M39TfmdpNicQ5CCLq1dCCLFx9OHU3DxtjEk3sd4ow6ibmrciaPp9sO9zv0VgkkhylQ/132','天天吃鱼125803','o3_wF4z43zwoXVyOW-TgnS9cLWYI'),(2,'130cm','40kg','中专',2020,'未婚','0-10w','[\"http://wcy15199004491-1254414522.cos.ap-nanjing.myqcloud.com/wxFile/k9j6UBbSBlDV2274bf1eb24904560f7dbbfdc63bdca5.png\",\"http://wcy15199004491-1254414522.cos.ap-nanjing.myqcloud.com/wxFile/uZmYs1ohJWq0bd1097cc4c437504d0e4a0a605628de1.png\"]',NULL,'诊断现在打法是的发送到发是打发是的发生的阿斯顿发生的发送是的发送到发送到发生的ass打法是的发送到发是打打法是的发送到发摄氏度法ass大法师打发点发斯蒂芬发是的发',0,0,'',0,1970,1970,1,'https://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83ersp4FWFlxWibKSePjptrDDLs1rEqsicRkuT1dfNmo7cXdNmD8h7sJZH3X995klgjBdF0aiaZS1tZxeA/132','哈尔滨洪运物流','o3_wF44vVs72T6bc9kzomfhfICz4');
/*!40000 ALTER TABLE `dp_mobile_person` ENABLE KEYS */;

#
# Structure for table "dp_mobile_version"
#

DROP TABLE IF EXISTS `dp_mobile_version`;
CREATE TABLE `dp_mobile_version` (
  `online` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Data for table "dp_mobile_version"
#

INSERT INTO `dp_mobile_version` VALUES (1);

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COMMENT='微信登录';

#
# Data for table "dp_mobile_wxuser"
#

INSERT INTO `dp_mobile_wxuser` VALUES ('o3_wF4z43zwoXVyOW-TgnS9cLWYI','????125803','https://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLh',1,1660892448,1661092049,6);
