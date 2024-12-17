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

 Date: 17/12/2024 18:39:01
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for xm_fee_rule
-- ----------------------------
DROP TABLE IF EXISTS `xm_fee_rule`;
CREATE TABLE `xm_fee_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `channel_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '通道ID',
  `merchant_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商户ID',
  `limit_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单最小金额',
  `receive_merchant_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收款商户ID',
  `rate` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '分账百分比',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态{1:启用,0:禁用}',
  PRIMARY KEY (`id`),
  KEY `idx_channel_id` (`channel_id`) USING BTREE,
  KEY `idx_merchant_id` (`merchant_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='手续费分账规则';

-- ----------------------------
-- Records of xm_fee_rule
-- ----------------------------
BEGIN;
INSERT INTO `xm_fee_rule` (`id`, `channel_id`, `merchant_id`, `limit_amount`, `receive_merchant_id`, `rate`, `created_at`, `updated_at`, `status`) VALUES (1, 4, 0, 0.00, 100001, 60.00, 0, 1734426883, 1);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
