/*
Navicat MySQL Data Transfer

Source Server         : 172.25.10.102_waf
Source Server Version : 50639
Source Host           : 172.25.10.102:3306
Source Database       : db_waf

Target Server Type    : MYSQL
Target Server Version : 50639
File Encoding         : 65001

Date: 2018-03-26 16:39:03
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_system_menu
-- ----------------------------
DROP TABLE IF EXISTS `t_system_menu`;
CREATE TABLE `t_system_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '系统ID',
  `menu_level` int(11) NOT NULL DEFAULT '0' COMMENT '菜单层级',
  `menu_pid` int(11) NOT NULL DEFAULT '0' COMMENT '上级菜单ID',
  `menu_title` varchar(50) NOT NULL COMMENT '菜单标题',
  `menu_full_title` varchar(100) NOT NULL COMMENT '菜单全称',
  `menu_icon` text NOT NULL COMMENT '菜单图标地址',
  `menu_url` text NOT NULL COMMENT '菜单链接',
  `menu_order` int(11) NOT NULL DEFAULT '0' COMMENT '菜单排序',
  `state` int(11) NOT NULL COMMENT '数据状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_system_menu
-- ----------------------------
INSERT INTO `t_system_menu` VALUES ('1', '0', '0', '防火墙', '防火墙管理', '/static/skin/default/nav/pus.png', '#', '0', '1');
INSERT INTO `t_system_menu` VALUES ('2', '1', '1', '规则', '规则管理', '#', '#', '0', '1');
INSERT INTO `t_system_menu` VALUES ('4', '0', '0', '系统', '系统管理', '/static/skin/default/nav/sys.png', '#', '0', '1');
INSERT INTO `t_system_menu` VALUES ('5', '1', '4', '站点', '站点管理', '#', '#', '0', '1');
INSERT INTO `t_system_menu` VALUES ('6', '2', '5', '管理菜单', '管理菜单', '#', '/system/menu.html', '0', '1');
INSERT INTO `t_system_menu` VALUES ('10', '2', '2', '应用站点', '应用站点', '#', '/waf/site_list.html', '0', '1');
INSERT INTO `t_system_menu` VALUES ('11', '2', '2', '规则集合', '规则集合', '#', '/waf/rule_list.html', '1', '1');

-- ----------------------------
-- Table structure for t_user_info
-- ----------------------------
DROP TABLE IF EXISTS `t_user_info`;
CREATE TABLE `t_user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '系统ID',
  `user_name` varchar(100) NOT NULL COMMENT '用户名',
  `user_password` varchar(50) NOT NULL COMMENT '登录密码',
  `user_pass_salt` varchar(50) NOT NULL COMMENT '登录密码盐',
  `user_type` int(11) NOT NULL COMMENT '用户类别',
  `state` int(11) NOT NULL COMMENT '账号状态',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '最后更新时间',
  `update_ip` varchar(50) NOT NULL COMMENT '最后更新ip',
  PRIMARY KEY (`id`),
  KEY `user_name_index` (`user_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_user_info
-- ----------------------------
INSERT INTO `t_user_info` VALUES ('1', 'cheergo', 'a66abb5684c45962d887564f08346e8d', '123456', '1', '1', '2018-01-23 23:17:48', '2018-01-23 23:17:52', '127.0.0.1');

-- ----------------------------
-- Table structure for t_waf_rule_logic
-- ----------------------------
DROP TABLE IF EXISTS `t_waf_rule_logic`;
CREATE TABLE `t_waf_rule_logic` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '系统ID',
  `rule_node_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联单条规则ID,0表示是中间逻辑',
  `rule_logic_name` varchar(100) NOT NULL COMMENT '逻辑名称',
  `rule_logic_type` int(11) NOT NULL DEFAULT '1' COMMENT '规则逻辑类型,1:中间逻辑,2:最终逻辑',
  `left_logic_id` int(11) NOT NULL DEFAULT '0' COMMENT '左侧逻辑ID,0表示true',
  `left_logic_type` varchar(50) NOT NULL DEFAULT 'and' COMMENT '左侧逻辑类别,and,or,andnot,ornot',
  `current_logic_key` varchar(150) NOT NULL COMMENT '当前逻辑key',
  `current_logic_type` varchar(50) NOT NULL DEFAULT 'eq' COMMENT '当前逻辑操作类别,eq,lt,gt,lte,gte,neq,regex',
  `current_logic_value` varchar(250) NOT NULL COMMENT '逻辑比较值,除了regex,必须是数字',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `update_ip` varchar(50) NOT NULL COMMENT '更新IP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_waf_rule_logic
-- ----------------------------
INSERT INTO `t_waf_rule_logic` VALUES ('3', '2', '搜索引擎UA', '2', '0', 'and', 's_http_header_useragent', 'regex', 'spider|Googlebot|bingbot', '2018-03-06 18:02:28', '2018-03-20 10:04:08', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('4', '3', '采集UA', '2', '0', 'and', 's_http_header_useragent', 'regex', 'wget|curl|scrapy|nutch|java|phantomjs|python|httpclient|ruby|newspaper|ahrefs|User.Agent|ExtLinksBot|Indy', '2018-03-06 18:04:27', '2018-03-20 10:40:55', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('5', '4', 'IP可疑加分大于100', '2', '0', 'and', 'd_score_ip_black', 'gt', '100', '2018-03-06 18:07:24', '2018-03-06 18:07:24', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('6', '5', '访问新闻页面', '1', '0', 'and', 's_http_header_url', 'regex', '/news/id_', '2018-03-06 18:09:39', '2018-03-06 18:09:39', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('7', '5', '访问概念页面', '1', '6', 'or', 's_http_header_url', 'regex', '/story/details/id_', '2018-03-06 18:10:32', '2018-03-06 18:10:32', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('8', '5', '不带访问来源', '2', '7', 'and', 's_http_header_referer', 'eq', '', '2018-03-06 18:12:12', '2018-03-06 18:12:12', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('9', '6', '请求ajax接口', '1', '0', 'and', 's_http_header_url', 'regex', 'yapi/ajax', '2018-03-06 18:15:45', '2018-03-06 18:15:45', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('10', '6', '不带UUID', '2', '9', 'and', 's_cookie_uuid', 'eq', '', '2018-03-06 18:16:17', '2018-03-06 18:16:17', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('11', '7', '漏洞URL', '2', '0', 'and', 's_http_header_url', 'regex', '/wp-|1.php|/FCKeditor|/admin|/plus/', '2018-03-06 18:18:51', '2018-03-06 18:18:51', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('12', '8', '新闻简版页面', '1', '0', 'and', 's_http_header_url', 'regex', 'insider/simple', '2018-03-06 18:30:19', '2018-03-06 18:30:19', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('13', '8', '每分钟超过25次', '2', '12', 'and', 'd_count_uuid_min', 'gt', '25', '2018-03-06 18:31:14', '2018-03-06 18:31:14', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('14', '9', 'uuid为空', '1', '0', 'and', 's_cookie_uuid', 'eq', '', '2018-03-26 15:25:52', '2018-03-26 15:25:52', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('15', '9', '没有访问来源', '1', '14', 'and', 's_http_header_referer', 'eq', '', '2018-03-26 15:26:45', '2018-03-26 15:26:45', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('16', '9', '内参页面访问', '2', '15', 'and', 's_http_header_url', 'regex', 'insider/', '2018-03-26 15:27:38', '2018-03-26 15:27:38', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('17', '10', '访问来源为空', '1', '0', 'and', 's_http_header_referer', 'eq', '', '2018-03-26 15:51:37', '2018-03-26 15:51:37', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('18', '10', '访问来源伪造', '1', '17', 'or', 's_http_header_referer', 'regex', 'yapi/ajax', '2018-03-26 15:52:07', '2018-03-26 15:52:07', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('19', '10', '访问接口地址', '2', '18', 'and', 's_http_header_url', 'regex', 'yapi/ajax', '2018-03-26 15:52:34', '2018-03-26 15:52:34', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('20', '11', '刷内参页面', '1', '0', 'and', 's_http_header_url', 'regex', '/insider/simple', '2018-03-26 16:34:15', '2018-03-26 16:34:15', '172.16.20.254');
INSERT INTO `t_waf_rule_logic` VALUES ('21', '11', '每分钟刷新超过45次', '2', '20', 'and', 'd_count_uuid_min', 'gt', '45', '2018-03-26 16:35:06', '2018-03-26 16:35:06', '172.16.20.254');

-- ----------------------------
-- Table structure for t_waf_rule_node
-- ----------------------------
DROP TABLE IF EXISTS `t_waf_rule_node`;
CREATE TABLE `t_waf_rule_node` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '系统ID',
  `rule_node_name` varchar(100) NOT NULL COMMENT '规则名称',
  `rule_order` int(11) NOT NULL DEFAULT '0' COMMENT '规则优先级',
  `rule_site_id` int(11) NOT NULL COMMENT '站点规则ID',
  `action_type` varchar(50) NOT NULL DEFAULT 'white' COMMENT '规则动作,white,black,white_score,black_score',
  `action_target` varchar(50) NOT NULL DEFAULT 'session' COMMENT '动作目标,session,cookie_uuid,cookie_uid,ip',
  `action_value` varchar(50) NOT NULL DEFAULT '' COMMENT '动作值',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `update_ip` varchar(50) NOT NULL COMMENT '更新IP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_waf_rule_node
-- ----------------------------
INSERT INTO `t_waf_rule_node` VALUES ('2', '放行搜索引擎', '5', '2', 'white', 'ip', '', '2018-03-06 18:01:56', '2018-03-20 10:04:20', '172.16.20.254');
INSERT INTO `t_waf_rule_node` VALUES ('3', '拦截采集', '6', '2', 'black', 'ip', '', '2018-03-06 18:03:40', '2018-03-20 10:41:22', '172.16.20.254');
INSERT INTO `t_waf_rule_node` VALUES ('4', '拦截可疑加分到达阈值', '100', '2', 'black', 'ip', '', '2018-03-06 18:06:36', '2018-03-06 18:07:24', '172.16.20.254');
INSERT INTO `t_waf_rule_node` VALUES ('5', '记录可疑新闻概念采集', '50', '2', 'black_score', 'ip', '10', '2018-03-06 18:08:56', '2018-03-06 18:17:08', '172.16.20.254');
INSERT INTO `t_waf_rule_node` VALUES ('6', '记录可疑接口采集1', '48', '2', 'black_score', 'ip', '30', '2018-03-06 18:15:12', '2018-03-26 15:49:59', '172.16.20.254');
INSERT INTO `t_waf_rule_node` VALUES ('7', '拦截漏洞扫描', '11', '2', 'black', 'ip', '', '2018-03-06 18:18:09', '2018-03-06 18:18:51', '172.16.20.254');
INSERT INTO `t_waf_rule_node` VALUES ('8', '拦截请求新闻过于频繁', '60', '2', 'black', 'ip', '3600', '2018-03-06 18:29:39', '2018-03-06 18:31:28', '172.16.20.254');
INSERT INTO `t_waf_rule_node` VALUES ('9', '记录内参页面采集', '51', '2', 'black_score', 'ip', '10', '2018-03-26 15:24:59', '2018-03-26 15:27:38', '172.16.20.254');
INSERT INTO `t_waf_rule_node` VALUES ('10', '记录可疑接口采集2', '49', '2', 'black_score', 'ip', '20', '2018-03-26 15:50:35', '2018-03-26 15:53:28', '172.16.20.254');
INSERT INTO `t_waf_rule_node` VALUES ('11', '限制内参页面刷新速度', '70', '2', 'black', 'cookie_uuid', '600', '2018-03-26 16:30:27', '2018-03-26 16:35:06', '172.16.20.254');

-- ----------------------------
-- Table structure for t_waf_rule_site
-- ----------------------------
DROP TABLE IF EXISTS `t_waf_rule_site`;
CREATE TABLE `t_waf_rule_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '系统ID',
  `rule_site_name` varchar(100) NOT NULL COMMENT '站点规则名称',
  `http_host` varchar(250) NOT NULL COMMENT '站点host规则',
  `http_host_type` varchar(50) NOT NULL DEFAULT 'string' COMMENT '站点host规则类别,string,regex',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `update_ip` varchar(50) NOT NULL COMMENT '更新IP',
  `update_cache_time` datetime DEFAULT '2000-01-01 00:00:00' COMMENT '上次更新到缓存时间',
  `state` int(11) NOT NULL COMMENT '数据状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_waf_rule_site
-- ----------------------------
INSERT INTO `t_waf_rule_site` VALUES ('2', '云财经PC站', 'www.yuncaijing.com', 'string', '2018-03-06 17:59:35', '2018-03-26 16:30:27', '172.16.20.254', '2018-03-26 16:30:27', '1');
