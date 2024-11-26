/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50744 (5.7.44-log)
 Source Host           : localhost:3306
 Source Schema         : xm

 Target Server Type    : MySQL
 Target Server Version : 50744 (5.7.44-log)
 File Encoding         : 65001

 Date: 26/11/2024 17:22:42
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for xm_admin_roles
-- ----------------------------
DROP TABLE IF EXISTS `xm_admin_roles`;
CREATE TABLE `xm_admin_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色id',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_admin_id` (`role_id`,`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COMMENT='管理员角色表';

-- ----------------------------
-- Records of xm_admin_roles
-- ----------------------------
BEGIN;
INSERT INTO `xm_admin_roles` (`id`, `role_id`, `admin_id`) VALUES (1, 1, 1);
INSERT INTO `xm_admin_roles` (`id`, `role_id`, `admin_id`) VALUES (2, 2, 2);
INSERT INTO `xm_admin_roles` (`id`, `role_id`, `admin_id`) VALUES (13, 2, 11);
INSERT INTO `xm_admin_roles` (`id`, `role_id`, `admin_id`) VALUES (7, 3, 6);
INSERT INTO `xm_admin_roles` (`id`, `role_id`, `admin_id`) VALUES (5, 4, 2);
INSERT INTO `xm_admin_roles` (`id`, `role_id`, `admin_id`) VALUES (6, 4, 6);
COMMIT;

-- ----------------------------
-- Table structure for xm_admins
-- ----------------------------
DROP TABLE IF EXISTS `xm_admins`;
CREATE TABLE `xm_admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(64) NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `avatar` varchar(255) NOT NULL DEFAULT '/admin/avatar.png' COMMENT '头像',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '手机',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `login_at` int(10) NOT NULL DEFAULT '0' COMMENT '登录时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态{0禁用, 1启用}',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';

-- ----------------------------
-- Records of xm_admins
-- ----------------------------
BEGIN;
INSERT INTO `xm_admins` (`id`, `username`, `nickname`, `password`, `avatar`, `email`, `mobile`, `created_at`, `updated_at`, `login_at`, `status`) VALUES (1, 'admin', '超级管理员', '$2y$10$pozibDHaw1bu.dIlV4x3N.e00LjXFvDI05n8Znr1kl9zTotpOfw1q', '/admin/avatar.png', '1231@1231.com', '1341123323', 1729565631, 1732263102, 1732263102, 1);
INSERT INTO `xm_admins` (`id`, `username`, `nickname`, `password`, `avatar`, `email`, `mobile`, `created_at`, `updated_at`, `login_at`, `status`) VALUES (2, 'test1', 'test1', '$2y$10$E8n1XfnothdtayrUWitk4.aY5vRhua9YdQHcpeIl5SaP.O45SNNCO', '/admin/avatar.png', '123@qq.com', '13566762531', 1729565631, 1729838788, 1729838775, 1);
INSERT INTO `xm_admins` (`id`, `username`, `nickname`, `password`, `avatar`, `email`, `mobile`, `created_at`, `updated_at`, `login_at`, `status`) VALUES (6, 'test2', 'test2', '$2y$10$3GEq6VoLxZoh.L0RRUcuUetXyDPmjdEmtuO70yZ7ouX/XbzRlDHxq', '/avatar.png', '', '', 1729565631, 1729565713, 0, 1);
INSERT INTO `xm_admins` (`id`, `username`, `nickname`, `password`, `avatar`, `email`, `mobile`, `created_at`, `updated_at`, `login_at`, `status`) VALUES (11, 'test3', 'test3', '$2y$10$nGGl74oW1.fT7NlLbUK9vOvHQRgzmZY1n0Ia9ZecGq3DLLjzLEEqO', '/avatar.png', '', '', 1729842586, 1729842592, 0, 1);
COMMIT;

-- ----------------------------
-- Table structure for xm_article
-- ----------------------------
DROP TABLE IF EXISTS `xm_article`;
CREATE TABLE `xm_article` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增主键ID',
  `category` int(11) NOT NULL DEFAULT '0' COMMENT '分类id',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `thumb` varchar(500) NOT NULL DEFAULT '' COMMENT '缩略图',
  `content` text COMMENT '内容',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort_order` int(11) NOT NULL DEFAULT '500' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态{1显示, 0隐藏}',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='文章表';

-- ----------------------------
-- Records of xm_article
-- ----------------------------
BEGIN;
INSERT INTO `xm_article` (`id`, `category`, `title`, `thumb`, `content`, `created_at`, `updated_at`, `sort_order`, `status`) VALUES (1, 1, '测试文章', '', '<p>刚刚刚刚</p>', 1729600578, 1729665820, 1, 1);
INSERT INTO `xm_article` (`id`, `category`, `title`, `thumb`, `content`, `created_at`, `updated_at`, `sort_order`, `status`) VALUES (2, 2, 'test2', '/upload/files/20240830/66d18d5e4965.png', '<p>PSP</p>\n<p>PS4</p>\n<p>PS3</p>\n<p>SWITCH</p>\n<p>MD</p>\n<p>FC</p>\n<p>SFC</p>', 1729600991, 1731047194, 0, 1);
INSERT INTO `xm_article` (`id`, `category`, `title`, `thumb`, `content`, `created_at`, `updated_at`, `sort_order`, `status`) VALUES (3, 2, 'test3', '', '<p>软软团团</p>', 1729601750, 1730084228, 0, 1);
INSERT INTO `xm_article` (`id`, `category`, `title`, `thumb`, `content`, `created_at`, `updated_at`, `sort_order`, `status`) VALUES (5, 1, 'test55511', '', '<p>asdffff</p>', 1729651240, 1730084226, 0, 1);
COMMIT;

-- ----------------------------
-- Table structure for xm_config
-- ----------------------------
DROP TABLE IF EXISTS `xm_config`;
CREATE TABLE `xm_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID（主键ID）',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '配置中文名称',
  `key` varchar(64) NOT NULL DEFAULT '' COMMENT '配置英文标识',
  `type` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `group` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `val` varchar(2000) NOT NULL DEFAULT '' COMMENT '配置值',
  `extra` varchar(2000) NOT NULL DEFAULT '' COMMENT '配置值',
  `desc` varchar(100) NOT NULL DEFAULT '' COMMENT '配置说明',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态{0:禁用；1:启用}',
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_key` (`key`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表';

-- ----------------------------
-- Records of xm_config
-- ----------------------------
BEGIN;
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (1, '配置类型列表', 'SYS_CONFIG_TYPE', 4, 2, '1:数字\n2:字符\n3:文本\n4:数组\n5:枚举\n6:图片', '', '主要用于数据解析和页面表单的生成', 1, 1, 1723075200, 1732611293);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (2, '配置分组', 'SYS_CONFIG_GROUP', 4, 2, '1:基本\n2:系统\n3:分类\n4:日志\n5:支付\n6:跨域', '', '配置分组', 1, 2, 1723075200, 1732611293);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (3, '后台列表条数', 'SYS_PAGE_ROWS', 1, 2, '10', '', '', 1, 3, 1723075200, 1732611293);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (4, '后台登录验证码', 'SYS_LOGIN_CAPTCHA', 5, 2, '0', '0:关闭\n1:开启', '', 1, 4, 1723075200, 1732611293);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (5, '网站域名', 'BASIC_SITE_URL', 2, 1, 'http://xm.me', '', '', 1, 1, 1723075200, 1732263015);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (6, '网站标题', 'BASIC_SITE_TITLE', 2, 1, 'XiaoMaPay - 小马支付', '', '', 1, 2, 1723075200, 1732263015);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (7, '网站Logo', 'BASIC_SITE_LOGO', 6, 1, '/admin/images/logo.png', '', '', 1, 3, 1723075200, 1732263015);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (8, '网站备案号', 'BASIC_SITE_ICP', 2, 1, '粤ICP00000001', '', '', 1, 4, 1723075200, 1732263015);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (9, '网站公网安备号', 'BASIC_SITE_BEIAN', 2, 1, '公网安备0008888111', '', '', 1, 5, 1723075200, 1732263015);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (10, '网站版权信息', 'BASIC_SITE_COPYRIGHT', 3, 1, '© 2025 XiaoMaPay', '', '', 1, 6, 1723075200, 1732263015);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (11, '网站底部信息', 'BASIC_SITE_FOOTER', 3, 1, '联系热线：1350000000', '', '', 1, 7, 1723075200, 1732263015);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (12, '后台LOGO', 'SYS_SITE_LOGO', 6, 2, '/upload/img/20241114/6735ef8d56cd.png', '', '', 1, 6, 1723075200, 1732611293);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (13, '后台系统名称', 'SYS_SITE_NAME', 2, 2, 'Admin', '', '', 1, 5, 1723075200, 1732611293);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (14, '后台系统页脚内容', 'SYS_SITE_FOOTER', 2, 2, 'Released under the MIT license. XiaoMaPay', '', '', 1, 7, 1723075200, 1732611293);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (15, '后台主标签名称', 'SYS_DASHBOARD_NAME', 2, 2, '首页', '', '', 1, 8, 1723075200, 1732611293);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (16, '附件类型', 'CATE_ATTACH_TYPE', 4, 3, '1:附件分类1\n2:附件分类2\n3:附件分类3\n4:附件分类4\n5:附件分类5', '', '', 1, 1, 1723075200, 1729739155);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (17, '文章分类', 'CATE_ARTICLE_TYPE', 4, 3, '1:文章分类1\n2:文章分类2\n3:文章分类3\n4:文章分类4\n5:文章分类5', '', '', 1, 2, 1729594458, 1729739155);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (18, '日志类型', 'CATE_LOG_TYPE', 4, 3, '1:后台账户\n2:管理员操作\n3:角色操作\n4:菜单操作\n5:配置操作\n6:文章操作\n7:附件操作', '', '', 1, 3, 1729671388, 1729739155);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (19, '日志用户类型', 'CATE_LOG_USER', 4, 3, '1:A端后台用户\n2:B端用户\n3:C端用户', '', '', 1, 4, 1729674148, 1729739155);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (20, '日志开关', 'LOG_SWITCH', 5, 4, '0', '1:开启\n0:关闭', '系统全局操作日志开关', 1, 1, 1729739365, 1730084387);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (21, '后台账户日志开关', 'LOG_SWITCH_1', 5, 4, '1', '0:关闭\n1:开启', '', 1, 2, 1729739900, 1730084387);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (22, '管理员日志开关', 'LOG_SWITCH_2', 5, 4, '1', '0:关闭\n1:开启', '', 1, 3, 1729773844, 1730084387);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (23, '角色日志开关', 'LOG_SWITCH_3', 5, 4, '1', '0:关闭\n1:开启', '', 1, 4, 1729773891, 1730084387);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (24, '菜单日志开关', 'LOG_SWITCH_4', 5, 4, '1', '0:关闭\n1:开启', '', 1, 5, 1729773924, 1730084387);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (25, '配置日志开关', 'LOG_SWITCH_5', 5, 4, '1', '0:关闭\n1:开启', '', 1, 6, 1729773975, 1730084387);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (26, '文章日志开关', 'LOG_SWITCH_6', 5, 4, '1', '0:关闭\n1:开启', '', 1, 7, 1729774022, 1730084387);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (27, '附件日志开关', 'LOG_SWITCH_7', 5, 4, '1', '0:关闭\n1:开启', '', 1, 8, 1729774047, 1730084387);
INSERT INTO `xm_config` (`id`, `name`, `key`, `type`, `group`, `val`, `extra`, `desc`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES (28, '支付通道模式', 'PAY_CHANNEL_MODES', 4, 5, '0:平台代收\n1:商户直清', '', '0:平台代收，资金进入平台账户，手续费从每笔订单中扣除之后，结算给商户。\n1:商户直清，资金进入商户账户，手续费从商户余额扣除，如余额不足将无法支付。', 1, 0, 1732611463, 1732611624);
COMMIT;

-- ----------------------------
-- Table structure for xm_log
-- ----------------------------
DROP TABLE IF EXISTS `xm_log`;
CREATE TABLE `xm_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键ID',
  `category` smallint(5) NOT NULL DEFAULT '0' COMMENT '操作记录分类',
  `controller` varchar(255) NOT NULL DEFAULT '' COMMENT '控制器名',
  `action` varchar(64) NOT NULL DEFAULT '' COMMENT '方法名',
  `action_name` varchar(64) NOT NULL DEFAULT '' COMMENT '操作名称',
  `user_type` smallint(5) NOT NULL DEFAULT '1' COMMENT '用户类型{1A端 2B端 3C端}',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '操作用户ID',
  `user_name` varchar(64) NOT NULL DEFAULT '' COMMENT '冗余用户名称',
  `user_agent` varchar(500) NOT NULL DEFAULT '' COMMENT '浏览器UserAgent',
  `client_ip` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'IP地址',
  `related_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '关联记录ID',
  `before_data` varchar(2000) NOT NULL DEFAULT '' COMMENT '修改前数据',
  `after_data` varchar(2000) NOT NULL DEFAULT '' COMMENT '修改后数据',
  `msg` varchar(500) NOT NULL DEFAULT '' COMMENT '操作描述',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_related_id` (`related_id`) USING BTREE,
  KEY `ids_created_at` (`created_at`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='操作日志';

-- ----------------------------
-- Records of xm_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for xm_pay_channel
-- ----------------------------
DROP TABLE IF EXISTS `xm_pay_channel`;
CREATE TABLE `xm_pay_channel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(32) NOT NULL COMMENT '通道显示名称',
  `mode` tinyint(1) NOT NULL DEFAULT '0' COMMENT '通道模式{0:支付金额扣除手续费后加入商户余额, 1:支付完成不增加商户余额，同时需扣除手续费}',
  `method_id` int(11) unsigned NOT NULL COMMENT '支付方式ID',
  `driver_key` varchar(32) NOT NULL COMMENT '支付驱动标识',
  `ratio` decimal(5,2) NOT NULL DEFAULT '100.00' COMMENT '商户结算比例',
  `app_type` varchar(128) NOT NULL DEFAULT '' COMMENT '可用接口',
  `day_top` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '单日限额,0或留空为没有单日限额，超出限额会暂停使用该通道',
  `day_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '当日状态',
  `pay_min` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '单笔最小金额',
  `pay_max` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '单笔最大金额',
  `app_wxmp` int(11) NOT NULL DEFAULT '0' COMMENT '绑定的微信公众号ID',
  `app_wxa` int(11) NOT NULL DEFAULT '0' COMMENT '绑定的微信小程序ID',
  `cost_ratio` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '通道成本比例',
  `secret_config` varchar(1000) NOT NULL DEFAULT '' COMMENT '密钥配置',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort_order` int(10) NOT NULL DEFAULT '500' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `idx_type` (`method_id`),
  KEY `idx_name` (`name`) USING BTREE,
  KEY `idx_driver_key` (`driver_key`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='支付通道';

-- ----------------------------
-- Records of xm_pay_channel
-- ----------------------------
BEGIN;
INSERT INTO `xm_pay_channel` (`id`, `name`, `mode`, `method_id`, `driver_key`, `ratio`, `app_type`, `day_top`, `day_status`, `pay_min`, `pay_max`, `app_wxmp`, `app_wxa`, `cost_ratio`, `secret_config`, `status`, `created_at`, `updated_at`, `sort_order`) VALUES (1, '平台通道1', 0, 7, 'monipay', 90.00, '1,2,3', 500000.00, 0, 0.01, 200000.00, 0, 0, 0.00, '', 1, 0, 1732612401, 500);
INSERT INTO `xm_pay_channel` (`id`, `name`, `mode`, `method_id`, `driver_key`, `ratio`, `app_type`, `day_top`, `day_status`, `pay_min`, `pay_max`, `app_wxmp`, `app_wxa`, `cost_ratio`, `secret_config`, `status`, `created_at`, `updated_at`, `sort_order`) VALUES (2, '商户A通道', 1, 7, 'monipay', 0.00, '1,3,5', 0.00, 0, 0.00, 0.00, 0, 0, 0.00, '', 1, 0, 0, 500);
COMMIT;

-- ----------------------------
-- Table structure for xm_pay_driver
-- ----------------------------
DROP TABLE IF EXISTS `xm_pay_driver`;
CREATE TABLE `xm_pay_driver` (
  `id` int(10) NOT NULL DEFAULT '1' COMMENT '主键ID',
  `key` varchar(32) NOT NULL DEFAULT '' COMMENT '驱动标识名称',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '驱动显示名称',
  `author` varchar(64) NOT NULL DEFAULT '' COMMENT '作者',
  `link` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `pay_types` varchar(128) NOT NULL DEFAULT '' COMMENT '包含支付方式',
  `trans_types` varchar(128) NOT NULL DEFAULT '' COMMENT '包含转账方式',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_key` (`key`) USING BTREE COMMENT '驱动标识唯一索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支付驱动表';

-- ----------------------------
-- Records of xm_pay_driver
-- ----------------------------
BEGIN;
INSERT INTO `xm_pay_driver` (`id`, `key`, `name`, `author`, `link`, `pay_types`, `trans_types`) VALUES (1, 'monipay', '模拟支付', 'XiaoMaPay', 'https://www.xiaomapay.com/', '[\"alipay\",\"qqpay\",\"wxpay\",\"bank\",\"jdpay\"]', '[\"alipay\",\"bank\"]');
COMMIT;

-- ----------------------------
-- Table structure for xm_pay_method
-- ----------------------------
DROP TABLE IF EXISTS `xm_pay_method`;
CREATE TABLE `xm_pay_method` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '显示名称',
  `driver_key` varchar(32) NOT NULL DEFAULT '' COMMENT '驱动标识',
  `is_pc` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支持PC{0:否,1:是}',
  `is_mobile` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支持移动端{0:否,1:是}',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态{0:关闭,1:开启}',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COMMENT='支付方式表';

-- ----------------------------
-- Records of xm_pay_method
-- ----------------------------
BEGIN;
INSERT INTO `xm_pay_method` (`id`, `name`, `driver_key`, `is_pc`, `is_mobile`, `created_at`, `updated_at`, `sort_order`, `status`) VALUES (1, '支付宝', 'alipay', 1, 1, 1732266542, 1732501417, 1, 1);
INSERT INTO `xm_pay_method` (`id`, `name`, `driver_key`, `is_pc`, `is_mobile`, `created_at`, `updated_at`, `sort_order`, `status`) VALUES (2, '微信支付', 'wxpay', 1, 1, 1732266542, 1732501421, 2, 1);
INSERT INTO `xm_pay_method` (`id`, `name`, `driver_key`, `is_pc`, `is_mobile`, `created_at`, `updated_at`, `sort_order`, `status`) VALUES (3, 'QQ钱包', 'qqpay', 1, 1, 1732266592, 1732501425, 3, 1);
INSERT INTO `xm_pay_method` (`id`, `name`, `driver_key`, `is_pc`, `is_mobile`, `created_at`, `updated_at`, `sort_order`, `status`) VALUES (4, '网银支付', 'bank', 1, 1, 1732266609, 1732501429, 4, 1);
INSERT INTO `xm_pay_method` (`id`, `name`, `driver_key`, `is_pc`, `is_mobile`, `created_at`, `updated_at`, `sort_order`, `status`) VALUES (5, '京东支付', 'jdpay', 1, 1, 1732266623, 1732501433, 5, 1);
INSERT INTO `xm_pay_method` (`id`, `name`, `driver_key`, `is_pc`, `is_mobile`, `created_at`, `updated_at`, `sort_order`, `status`) VALUES (6, 'PayPal', 'paypal', 1, 1, 1732266639, 1732501437, 6, 1);
INSERT INTO `xm_pay_method` (`id`, `name`, `driver_key`, `is_pc`, `is_mobile`, `created_at`, `updated_at`, `sort_order`, `status`) VALUES (7, '模拟钱包', 'monipay', 1, 1, 1732266677, 1732501441, 7, 1);
COMMIT;

-- ----------------------------
-- Table structure for xm_roles
-- ----------------------------
DROP TABLE IF EXISTS `xm_roles`;
CREATE TABLE `xm_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(80) NOT NULL DEFAULT '' COMMENT '角色组',
  `rules` varchar(2000) NOT NULL DEFAULT '' COMMENT '权限',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COMMENT='管理员角色';

-- ----------------------------
-- Records of xm_roles
-- ----------------------------
BEGIN;
INSERT INTO `xm_roles` (`id`, `name`, `rules`, `created_at`, `updated_at`, `pid`) VALUES (1, '超级管理员', '*', 1729566138, 1729566169, 0);
INSERT INTO `xm_roles` (`id`, `name`, `rules`, `created_at`, `updated_at`, `pid`) VALUES (2, '测试', '74,75,76,119,89,90,117,140,141,142,94,95,96,97,98,99,100,101,136,120,129', 1729566138, 1729566169, 1);
INSERT INTO `xm_roles` (`id`, `name`, `rules`, `created_at`, `updated_at`, `pid`) VALUES (3, 'test2', '74,75,76,119', 1729566138, 1729566169, 2);
INSERT INTO `xm_roles` (`id`, `name`, `rules`, `created_at`, `updated_at`, `pid`) VALUES (4, '运营', '74,75,76,119,77,78,79,80,116,81,82,83,84,118,89,90,117,140,141,142,94,95,96,97,98,99,100,101,136,102,103,104,105,121,122,123,124,125,137,138,139,120,129', 1729566138, 1729566160, 1);
INSERT INTO `xm_roles` (`id`, `name`, `rules`, `created_at`, `updated_at`, `pid`) VALUES (5, '开发', '74,75,76,119,77,78,79,80,116,81,82,83,84,118,89,90,117,140,141,142,94,95,96,97,98,99,100,101,136,102,103,104,105,121,122,123,124,125,137,138,139,120,129,63,64,65,66,67,68,69,70,71,72,73,113', 1729566138, 1729566138, 1);
INSERT INTO `xm_roles` (`id`, `name`, `rules`, `created_at`, `updated_at`, `pid`) VALUES (11, '技术客服', '89,90,117,140,141,142,102,103,104,105,121,122,123,124,125,137,138,139,120,129', 1729842617, 1729842617, 4);
COMMIT;

-- ----------------------------
-- Table structure for xm_rules
-- ----------------------------
DROP TABLE IF EXISTS `xm_rules`;
CREATE TABLE `xm_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '标题',
  `icon` varchar(200) NOT NULL DEFAULT '' COMMENT '图标',
  `key` varchar(200) NOT NULL DEFAULT '' COMMENT '标识',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级菜单',
  `href` varchar(255) NOT NULL DEFAULT '' COMMENT 'url',
  `type` int(10) NOT NULL DEFAULT '1' COMMENT '类型',
  `weight` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态{1启用 0禁用}',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8mb4 COMMENT='权限规则';

-- ----------------------------
-- Records of xm_rules
-- ----------------------------
BEGIN;
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (1, '数据库', 'layui-icon-template-1', 'database', 0, '', 0, 1000, 0, 1723075200, 1729852957);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (2, '数据库管理', '', 'app\\admin\\controller\\TableController', 16, '/admin/table/index', 1, 800, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (3, '权限管理', 'layui-icon-vercode', 'auth', 0, '', 0, 900, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (4, '账户管理', '', 'app\\admin\\controller\\AdminController', 3, '/admin/admin/index', 1, 1000, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (5, '角色管理', '', 'app\\admin\\controller\\RoleController', 3, '/admin/role/index', 1, 900, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (6, '菜单管理', '', 'app\\admin\\controller\\RuleController', 3, '/admin/rule/index', 1, 800, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (9, '通用设置', 'layui-icon-set', 'common', 0, '', 0, 700, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (10, '个人资料', '', 'app\\admin\\controller\\AccountController', 9, '/admin/account/index', 1, 800, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (11, '附件管理', '', 'app\\admin\\controller\\UploadController', 143, '/admin/upload/index', 1, 610, 1, 1723075200, 1729662170);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (12, '字典设置', '', 'app\\admin\\controller\\DictController', 9, '/admin/dict/index', 1, 600, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (16, '开发辅助', 'layui-icon-fonts-code', 'dev', 0, '', 0, 500, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (17, '表单构建', '', 'app\\admin\\controller\\DevController', 16, '/admin/dev/form-build', 1, 800, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (18, '示例页面', 'layui-icon-templeate-1', 'demos', 0, '', 0, 400, 1, 1723075200, 1729599989);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (19, '工作空间', 'layui-icon-console', 'demo1', 18, '', 0, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (20, '控制后台', 'layui-icon-console', 'demo10', 19, '/demos/console/console1.html', 1, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (21, '数据分析', 'layui-icon-console', 'demo13', 19, '/demos/console/console2.html', 1, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (22, '百度一下', 'layui-icon-console', 'demo14', 19, 'http://www.baidu.com', 1, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (23, '主题预览', 'layui-icon-console', 'demo15', 19, '/demos/system/theme.html', 1, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (24, '常用组件', 'layui-icon-component', 'demo20', 18, '', 0, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (25, '功能按钮', 'layui-icon-face-smile', 'demo2011', 24, '/demos/document/button.html', 1, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (26, '表单集合', 'layui-icon-face-cry', 'demo2014', 24, '/demos/document/form.html', 1, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (27, '字体图标', 'layui-icon-face-cry', 'demo2010', 24, '/demos/document/icon.html', 1, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (28, '多选下拉', 'layui-icon-face-cry', 'demo2012', 24, '/demos/document/select.html', 1, 0, 1, 1723075200, 1729599997);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (29, '动态标签', 'layui-icon-face-cry', 'demo2013', 24, '/demos/document/tag.html', 1, 0, 1, 1723075200, 1729599998);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (30, '数据表格', 'layui-icon-face-cry', 'demo2031', 24, '/demos/document/table.html', 1, 0, 1, 1723075200, 1729599998);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (31, '分布表单', 'layui-icon-face-cry', 'demo2032', 24, '/demos/document/step.html', 1, 0, 1, 1723075200, 1729600000);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (32, '树形表格', 'layui-icon-face-cry', 'demo2033', 24, '/demos/document/treetable.html', 1, 0, 1, 1723075200, 1729599999);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (33, '树状结构', 'layui-icon-face-cry', 'demo2034', 24, '/demos/document/dtree.html', 1, 0, 1, 1723075200, 1729600001);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (34, '文本编辑', 'layui-icon-face-cry', 'demo2035', 24, '/demos/document/tinymce.html', 1, 0, 1, 1723075200, 1729600001);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (35, '卡片组件', 'layui-icon-face-cry', 'demo2036', 24, '/demos/document/card.html', 1, 0, 1, 1723075200, 1729600003);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (36, '抽屉组件', 'layui-icon-face-cry', 'demo2021', 24, '/demos/document/drawer.html', 1, 0, 1, 1723075200, 1729600003);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (37, '消息通知', 'layui-icon-face-cry', 'demo2022', 24, '/demos/document/notice.html', 1, 0, 1, 1723075200, 1729600004);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (38, '加载组件', 'layui-icon-face-cry', 'demo2024', 24, '/demos/document/loading.html', 1, 0, 1, 1723075200, 1729600005);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (39, '弹层组件', 'layui-icon-face-cry', 'demo2023', 24, '/demos/document/popup.html', 1, 0, 1, 1723075200, 1729600006);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (40, '多选项卡', 'layui-icon-face-cry', 'demo60131', 24, '/demos/document/tab.html', 1, 0, 1, 1723075200, 1729600007);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (41, '数据菜单', 'layui-icon-face-cry', 'demo60132', 24, '/demos/document/menu.html', 1, 0, 1, 1723075200, 1729600007);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (42, '哈希加密', 'layui-icon-face-cry', 'demo2041', 24, '/demos/document/encrypt.html', 1, 0, 1, 1723075200, 1729600008);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (43, '图标选择', 'layui-icon-face-cry', 'demo2042', 24, '/demos/document/iconPicker.html', 1, 0, 1, 1723075200, 1729600010);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (44, '省市级联', 'layui-icon-face-cry', 'demo2043', 24, '/demos/document/area.html', 1, 0, 1, 1723075200, 1729600010);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (45, '数字滚动', 'layui-icon-face-cry', 'demo2044', 24, '/demos/document/count.html', 1, 0, 1, 1723075200, 1729600011);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (46, '顶部返回', 'layui-icon-face-cry', 'demo2045', 24, '/demos/document/topBar.html', 1, 0, 1, 1723075200, 1729600012);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (47, '结果页面', 'layui-icon-auz', 'demo666', 18, '', 0, 0, 1, 1723075200, 1729599990);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (48, '成功', 'layui-icon-face-smile', 'demo667', 47, '/demos/result/success.html', 1, 0, 1, 1723075200, 1729600016);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (49, '失败', 'layui-icon-face-cry', 'demo668', 47, '/demos/result/error.html', 1, 0, 1, 1723075200, 1729600017);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (50, '错误页面', 'layui-icon-face-cry', 'demo-error', 18, '', 0, 0, 1, 1723075200, 1729599992);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (51, '403', 'layui-icon-face-smile', 'demo403', 50, '/demos/error/403.html', 1, 0, 1, 1723075200, 1729600020);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (52, '404', 'layui-icon-face-cry', 'demo404', 50, '/demos/error/404.html', 1, 0, 1, 1723075200, 1729600020);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (53, '500', 'layui-icon-face-cry', 'demo500', 50, '/demos/error/500.html', 1, 0, 1, 1723075200, 1729600021);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (54, '系统管理', 'layui-icon-set-fill', 'demo-system', 18, '', 0, 0, 1, 1723075200, 1729599991);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (55, '用户管理', 'layui-icon-face-smile', 'demo601', 54, '/demos/system/user.html', 1, 0, 1, 1723075200, 1729600024);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (56, '角色管理', 'layui-icon-face-cry', 'demo602', 54, '/demos/system/role.html', 1, 0, 1, 1723075200, 1729600024);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (57, '权限管理', 'layui-icon-face-cry', 'demo603', 54, '/demos/system/power.html', 1, 0, 1, 1723075200, 1729600026);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (58, '部门管理', 'layui-icon-face-cry', 'demo604', 54, '/demos/system/deptment.html', 1, 0, 1, 1723075200, 1729600025);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (59, '行为日志', 'layui-icon-face-cry', 'demo605', 54, '/demos/system/log.html', 1, 0, 1, 1723075200, 1729600027);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (60, '数据字典', 'layui-icon-face-cry', 'demo606', 54, '/demos/system/dict.html', 1, 0, 1, 1723075200, 1729600027);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (61, '常用页面', 'layui-icon-template-1', 'demo-common', 18, '', 0, 0, 1, 1723075200, 1729599991);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (62, '空白页面', 'layui-icon-face-smile', 'demo702', 61, '/demos/system/space.html', 1, 0, 1, 1723075200, 1729600031);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (63, '查看表', '', 'app\\admin\\controller\\TableController@view', 2, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (64, '查询表', '', 'app\\admin\\controller\\TableController@show', 2, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (65, '创建表', '', 'app\\admin\\controller\\TableController@create', 2, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (66, '修改表', '', 'app\\admin\\controller\\TableController@modify', 2, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (67, '一键菜单', '', 'app\\admin\\controller\\TableController@crud', 2, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (68, '查询记录', '', 'app\\admin\\controller\\TableController@select', 2, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (69, '插入记录', '', 'app\\admin\\controller\\TableController@insert', 2, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (70, '更新记录', '', 'app\\admin\\controller\\TableController@update', 2, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (71, '删除记录', '', 'app\\admin\\controller\\TableController@delete', 2, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (72, '删除表', '', 'app\\admin\\controller\\TableController@drop', 2, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (73, '表摘要', '', 'app\\admin\\controller\\TableController@schema', 2, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (74, '新增管理员', '', 'app\\admin\\controller\\AdminController@insert', 4, '', 2, 0, 1, 1723075200, 1729839374);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (75, '更新管理员', '', 'app\\admin\\controller\\AdminController@update', 4, '', 2, 0, 1, 1723075200, 1729839374);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (76, '删除管理员', '', 'app\\admin\\controller\\AdminController@delete', 4, '', 2, 0, 1, 1723075200, 1729839374);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (77, '列表', '', 'app\\admin\\controller\\RoleController@index', 5, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (78, '更新角色', '', 'app\\admin\\controller\\RoleController@update', 5, '', 2, 0, 1, 1723075200, 1729841531);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (79, '删除角色', '', 'app\\admin\\controller\\RoleController@delete', 5, '', 2, 0, 1, 1723075200, 1729841531);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (80, '获取角色权限', '', 'app\\admin\\controller\\RoleController@rules', 5, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (81, '查询', '', 'app\\admin\\controller\\RuleController@select', 6, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (82, '新增菜单', '', 'app\\admin\\controller\\RuleController@insert', 6, '', 2, 0, 1, 1723075200, 1729841659);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (83, '更新菜单', '', 'app\\admin\\controller\\RuleController@update', 6, '', 2, 0, 1, 1723075200, 1729841659);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (84, '删除菜单', '', 'app\\admin\\controller\\RuleController@delete', 6, '', 2, 0, 1, 1723075200, 1729841659);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (89, '更新个人资料', '', 'app\\admin\\controller\\AccountController@update', 10, '', 2, 0, 1, 1723075200, 1729838189);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (90, '修改密码', '', 'app\\admin\\controller\\AccountController@password', 10, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (94, '浏览附件', '', 'app\\admin\\controller\\UploadController@attachment', 11, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (95, '查询附件', '', 'app\\admin\\controller\\UploadController@select', 11, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (96, '更新附件', '', 'app\\admin\\controller\\UploadController@update', 11, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (97, '新增附件', '', 'app\\admin\\controller\\UploadController@insert', 11, '', 2, 0, 1, 1723075200, 1729845975);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (98, '上传文件', '', 'app\\admin\\controller\\UploadController@file', 11, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (99, '上传图片', '', 'app\\admin\\controller\\UploadController@image', 11, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (100, '上传头像', '', 'app\\admin\\controller\\UploadController@avatar', 11, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (101, '删除附件', '', 'app\\admin\\controller\\UploadController@delete', 11, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (102, '查询', '', 'app\\admin\\controller\\DictController@select', 12, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (103, '插入', '', 'app\\admin\\controller\\DictController@insert', 12, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (104, '更新', '', 'app\\admin\\controller\\DictController@update', 12, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (105, '删除', '', 'app\\admin\\controller\\DictController@delete', 12, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (113, '表单构建', '', 'app\\admin\\controller\\DevController@formBuild', 17, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (114, '配置管理', '', 'app\\admin\\controller\\ConfigController', 9, '/admin/config/list', 1, 500, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (115, '系统配置', '', 'app\\admin\\controller\\ConfigController@config', 9, '/admin/config/index', 1, 400, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (116, '新增角色', '', 'app\\admin\\controller\\RoleController@insert', 5, '', 2, 0, 1, 1723075200, 1729841531);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (117, '界面', '', 'app\\admin\\controller\\AccountController@index', 10, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (118, '列表', '', 'app\\admin\\controller\\RuleController@index', 6, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (119, '列表', '', 'app\\admin\\controller\\AdminController@index', 4, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (120, '保存系统配置', '', 'app\\admin\\controller\\ConfigController@save', 115, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (121, '查询', '', 'app\\admin\\controller\\ConfigController@select', 114, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (122, '配置管理', '', 'app\\admin\\controller\\ConfigController@list', 114, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (123, '新增配置项', '', 'app\\admin\\controller\\ConfigController@insert', 114, '', 2, 0, 1, 1723075200, 1729841659);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (124, '更新配置项', '', 'app\\admin\\controller\\ConfigController@update', 114, '', 2, 0, 1, 1723075200, 1729841659);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (125, '删除配置项', '', 'app\\admin\\controller\\ConfigController@delete', 114, '', 2, 0, 1, 1723075200, 1729841659);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (129, '配置界面', '', 'app\\admin\\controller\\ConfigController@index', 115, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (136, '附件列表', '', 'app\\admin\\controller\\UploadController@index', 11, '', 2, 0, 1, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (137, '获取后台默认配置', '', 'app\\admin\\controller\\ConfigController@getByDefault', 114, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (138, '旧配置管理(废弃的)', '', 'app\\admin\\controller\\ConfigController@oldIndex', 114, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (139, '旧配置更改(废弃的)', '', 'app\\admin\\controller\\ConfigController@oldSave', 114, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (140, '查询', '', 'app\\admin\\controller\\AccountController@select', 10, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (141, '添加', '', 'app\\admin\\controller\\AccountController@insert', 10, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (142, '删除', '', 'app\\admin\\controller\\AccountController@delete', 10, '', 2, 0, 0, 1723075200, 1723075200);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (143, '内容管理', 'layui-icon-tabs', 'content', 0, '', 0, 600, 1, 1729599348, 1729599348);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (144, '文章管理', '', 'app\\admin\\controller\\ArticleController', 143, '/admin/article/index', 1, 620, 1, 1729599453, 1729662186);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (145, '查询', '', 'app\\admin\\controller\\ArticleController@select', 144, '', 2, 0, 1, 1729599456, 1729599474);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (146, '新增文章', '', 'app\\admin\\controller\\ArticleController@insert', 144, '', 2, 0, 1, 1729599457, 1729841659);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (147, '更新文章', '', 'app\\admin\\controller\\ArticleController@update', 144, '', 2, 0, 1, 1729599457, 1729841659);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (148, '删除文章', '', 'app\\admin\\controller\\ArticleController@delete', 144, '', 2, 0, 1, 1729599457, 1729841723);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (149, '操作日志', '', 'app\\admin\\controller\\LogController', 9, '/admin/log/index', 1, 300, 1, 1729750529, 1729750924);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (150, '查询', '', 'app\\admin\\controller\\LogController@select', 149, '', 2, 0, 1, 1729750532, 1729750547);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (151, '添加', '', 'app\\admin\\controller\\LogController@insert', 149, '', 2, 0, 0, 1729750532, 1729750532);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (152, '更新', '', 'app\\admin\\controller\\LogController@update', 149, '', 2, 0, 0, 1729750532, 1729750532);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (153, '删除', '', 'app\\admin\\controller\\LogController@delete', 149, '', 2, 0, 0, 1729750532, 1729750532);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (154, '列表', '', 'app\\admin\\controller\\LogController@index', 149, '', 2, 0, 1, 1729750585, 1729750585);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (155, '文章列表', '', 'app\\admin\\controller\\ArticleController@index', 144, '', 2, 0, 1, 1729750653, 1732265992);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (156, '登录后台', '', 'app\\admin\\controller\\AccountController@login', 10, '', 2, 0, 0, 1729838098, 1729838120);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (157, '退出后台', '', 'app\\admin\\controller\\AccountController@logout', 10, '', 2, 0, 0, 1729838144, 1729838144);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (158, '支付接口', 'layui-icon-component', 'pay', 0, '', 0, 300, 1, 1732260269, 1732261321);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (159, '支付驱动', '', 'app\\admin\\controller\\PayDriverController', 158, '/admin/paydriver/index', 1, 0, 1, 1732260462, 1732260462);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (160, '支付方式', '', 'app\\admin\\controller\\PayMethodController', 158, '/admin/payMethod/index', 1, 0, 1, 1732262069, 1732265824);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (161, '支付通道', '', 'app\\admin\\controller\\PayChannelController', 158, '/admin/paychannel/index', 1, 0, 1, 1732262128, 1732262128);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (162, '新增支付方式', '', 'app\\admin\\controller\\PayMethodController@insert', 160, '', 2, 0, 1, 1732265637, 1732532042);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (163, '更新支付方式', '', 'app\\admin\\controller\\PayMethodController@update', 160, '', 2, 0, 1, 1732265637, 1732532042);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (164, '删除支付方式', '', 'app\\admin\\controller\\PayMethodController@delete', 160, '', 2, 0, 1, 1732265637, 1732532042);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (165, '查询', '', 'app\\admin\\controller\\PayMethodController@select', 160, '', 2, 0, 1, 1732265637, 1732265719);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (166, '列表', '', 'app\\admin\\controller\\PayMethodController@index', 160, '', 2, 0, 1, 1732265803, 1732265803);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (167, '刷新支付驱动列表', '', 'app\\admin\\controller\\PayDriverController@refresh', 159, '', 2, 0, 1, 1732532042, 1732532092);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (168, '查询', '', 'app\\admin\\controller\\PayDriverController@select', 159, '', 2, 0, 0, 1732532042, 1732532042);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (169, '添加', '', 'app\\admin\\controller\\PayDriverController@insert', 159, '', 2, 0, 0, 1732532042, 1732532042);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (170, '更新', '', 'app\\admin\\controller\\PayDriverController@update', 159, '', 2, 0, 0, 1732532042, 1732532042);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (171, '删除', '', 'app\\admin\\controller\\PayDriverController@delete', 159, '', 2, 0, 0, 1732532042, 1732532042);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (172, '新增支付通道', '', 'app\\admin\\controller\\PayChannelController@insert', 161, '', 2, 0, 0, 1732610121, 1732610121);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (173, '更新支付通道', '', 'app\\admin\\controller\\PayChannelController@update', 161, '', 2, 0, 0, 1732610121, 1732610121);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (174, '删除支付通道', '', 'app\\admin\\controller\\PayChannelController@delete', 161, '', 2, 0, 0, 1732610121, 1732610121);
INSERT INTO `xm_rules` (`id`, `title`, `icon`, `key`, `pid`, `href`, `type`, `weight`, `status`, `created_at`, `updated_at`) VALUES (175, '查询', '', 'app\\admin\\controller\\PayChannelController@select', 161, '', 2, 0, 0, 1732610121, 1732610121);
COMMIT;

-- ----------------------------
-- Table structure for xm_uploads
-- ----------------------------
DROP TABLE IF EXISTS `xm_uploads`;
CREATE TABLE `xm_uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `category` int(11) NOT NULL DEFAULT '1' COMMENT '类别',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件',
  `admin_id` int(11) NOT NULL COMMENT '管理员',
  `file_size` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `mime_type` varchar(255) NOT NULL DEFAULT '' COMMENT 'mime类型',
  `image_width` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '图片宽度',
  `image_height` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '图片高度',
  `ext` varchar(128) NOT NULL DEFAULT '' COMMENT '扩展名',
  `storage` varchar(255) NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '上传时间',
  `updated_at` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `admin_id` (`admin_id`),
  KEY `name` (`name`),
  KEY `ext` (`ext`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='附件';

-- ----------------------------
-- Records of xm_uploads
-- ----------------------------
BEGIN;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
