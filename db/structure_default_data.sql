/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 100414
 Source Host           : localhost:3306
 Source Schema         : fsoftpro_barcode_p3

 Target Server Type    : MySQL
 Target Server Version : 100414
 File Encoding         : 65001

 Date: 15/07/2021 00:35:31
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for mb_master_barcode
-- ----------------------------
DROP TABLE IF EXISTS `mb_master_barcode`;
CREATE TABLE `mb_master_barcode` (
  `id_barcode` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_group` int(11) DEFAULT NULL,
  `barcode_prefix` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '3 ตัวหน้า barcode',
  `barcode_code` varchar(9) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '5 ตัวหลัง barcode หากหลักไม่ครบเป็น 0',
  `barcode_status` int(1) unsigned zerofill DEFAULT 0,
  `barcode_flag` int(1) unsigned zerofill DEFAULT 0,
  `group_received` int(11) NOT NULL DEFAULT 0 COMMENT 'การกดรับในหน้า Barcode Received',
  `date_added` date DEFAULT NULL,
  `date_modify` date DEFAULT NULL,
  PRIMARY KEY (`id_barcode`),
  KEY `id_group` (`id_group`) USING BTREE,
  KEY `barcode_prefix` (`barcode_prefix`) USING BTREE,
  KEY `barcode_code` (`barcode_code`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of mb_master_barcode
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for mb_master_barcode_range
-- ----------------------------
DROP TABLE IF EXISTS `mb_master_barcode_range`;
CREATE TABLE `mb_master_barcode_range` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `round` int(11) DEFAULT NULL,
  `group_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `barcode_start` int(11) DEFAULT NULL,
  `barcode_end` int(11) DEFAULT NULL,
  `barcode_qty` int(11) DEFAULT NULL,
  `barcode_status` int(11) DEFAULT NULL,
  `date_added` date DEFAULT NULL,
  `date_modify` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of mb_master_barcode_range
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for mb_master_config
-- ----------------------------
DROP TABLE IF EXISTS `mb_master_config`;
CREATE TABLE `mb_master_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `config_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_modify` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of mb_master_config
-- ----------------------------
BEGIN;
INSERT INTO `mb_master_config` VALUES (1, 'config_date_size', '0', NULL, '2021-04-29 10:12:49');
INSERT INTO `mb_master_config` VALUES (2, 'config_date_year', '1095', NULL, '2021-04-29 10:12:49');
INSERT INTO `mb_master_config` VALUES (3, 'config_maximum_alert', '50', NULL, '2021-04-29 10:12:49');
INSERT INTO `mb_master_config` VALUES (4, 'config_lastweek', '14', '2020-11-27 14:31:20', '2021-04-29 10:12:49');
INSERT INTO `mb_master_config` VALUES (5, 'load_freegroup', NULL, '2020-12-23 11:03:00', '2021-07-15 00:31:43');
INSERT INTO `mb_master_config` VALUES (6, 'load_year', NULL, '2020-12-23 11:03:00', '2021-07-15 00:31:43');
INSERT INTO `mb_master_config` VALUES (7, 'load_barcode', '1', '2020-12-23 11:03:00', '2021-03-08 19:38:20');
INSERT INTO `mb_master_config` VALUES (8, 'load_date', '1', '2020-12-23 11:03:00', '2021-02-22 16:50:35');
COMMIT;

-- ----------------------------
-- Table structure for mb_master_config_barcode
-- ----------------------------
DROP TABLE IF EXISTS `mb_master_config_barcode`;
CREATE TABLE `mb_master_config_barcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start` varchar(9) COLLATE utf8_unicode_ci DEFAULT NULL,
  `end` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `remaining` int(11) DEFAULT NULL,
  `now` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `group` (`group`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of mb_master_config_barcode
-- ----------------------------
BEGIN;
INSERT INTO `mb_master_config_barcode` VALUES (1, 'A01', 'A0100000', 'A0199999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (2, 'A06', 'A0600000', 'A0699999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (3, 'A07', 'A0700000', 'A0799999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (4, 'A08', 'A0800000', 'A0899999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (5, 'A09', 'A0900000', 'A0999999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (6, 'A15', 'A1500000', 'A1599999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (7, 'A16', 'A1600000', 'A1699999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (8, 'A17', 'A1700000', 'A1799999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (9, 'A18', 'A1800000', 'A1899999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (10, 'A19', 'A1900000', 'A1999999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (11, 'A25', 'A2500000', 'A2599999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (12, 'A26', 'A2600000', 'A2699999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (13, 'A27', 'A2700000', 'A2799999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (14, 'A28', 'A2800000', 'A2899999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (15, 'A29', 'A2900000', 'A2999999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (16, 'A35', 'A3500000', 'A3599999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (17, 'A36', 'A3600000', 'A3699999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (18, 'A37', 'A3700000', 'A3799999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (19, 'A38', 'A3800000', 'A3899999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (20, 'A39', 'A3900000', 'A3999999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (21, 'A45', 'A4500000', 'A4599999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (22, 'A46', 'A4600000', 'A4699999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (23, 'A47', 'A4700000', 'A4799999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (24, 'A48', 'A4800000', 'A4899999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (25, 'A49', 'A4900000', 'A4999999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (26, 'A55', 'A5500000', 'A5599999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (27, 'A56', 'A5600000', 'A5699999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (28, 'A57', 'A5700000', 'A5799999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (29, 'A58', 'A5800000', 'A5899999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (30, 'A59', 'A5900000', 'A5999999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (31, 'A65', 'A6500000', 'A6599999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (32, 'A66', 'A6600000', 'A6699999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (33, 'A67', 'A6700000', 'A6799999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (34, 'A68', 'A6800000', 'A6899999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (35, 'A69', 'A6900000', 'A6999999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (36, 'A75', 'A7500000', 'A7599999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (37, 'A76', 'A7600000', 'A7699999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (38, 'A77', 'A7700000', 'A7799999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (39, 'A78', 'A7800000', 'A7899999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (40, 'A79', 'A7900000', 'A7999999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (41, 'A85', 'A8500000', 'A8599999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (42, 'A86', 'A8600000', 'A8699999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (43, 'A87', 'A8700000', 'A8799999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (44, 'A88', 'A8800000', 'A8899999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (45, 'A89', 'A8900000', 'A8999999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (46, 'A95', 'A9500000', 'A9599999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (47, 'A96', 'A9600000', 'A9699999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (48, 'A97', 'A9700000', 'A9799999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (49, 'A98', 'A9800000', 'A9899999', 100000, 100000, '0', '2021-07-13 19:51:50');
INSERT INTO `mb_master_config_barcode` VALUES (50, 'A99', 'A9900000', 'A9999999', 100000, 100000, '0', '2021-07-13 19:51:50');
COMMIT;

-- ----------------------------
-- Table structure for mb_master_config_relationship
-- ----------------------------
DROP TABLE IF EXISTS `mb_master_config_relationship`;
CREATE TABLE `mb_master_config_relationship` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` int(11) DEFAULT NULL,
  `size` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `comment` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_modify` datetime DEFAULT NULL,
  `del` int(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `group` (`group`) USING BTREE,
  KEY `size` (`size`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of mb_master_config_relationship
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for mb_master_config_status
-- ----------------------------
DROP TABLE IF EXISTS `mb_master_config_status`;
CREATE TABLE `mb_master_config_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_modify` datetime DEFAULT NULL,
  `del` int(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of mb_master_config_status
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for mb_master_group
-- ----------------------------
DROP TABLE IF EXISTS `mb_master_group`;
CREATE TABLE `mb_master_group` (
  `id_group` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `group_code` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '3 ตัวหน้า barcode เทียบกับ barcode คือ barcode_prefix',
  `start` varchar(9) COLLATE utf8_unicode_ci DEFAULT '0' COMMENT 'เลข start ครั้งถัดไป',
  `end` varchar(9) COLLATE utf8_unicode_ci DEFAULT '0' COMMENT 'ไม่ใช้',
  `remaining_qty` int(11) DEFAULT 0 COMMENT 'จำนวนสั่งซื้อครั้งล่าสุด',
  `default_start` int(11) DEFAULT 0 COMMENT 'เลข ตั้งต้นของชุด barcode ดึงมาจาก config_barcode',
  `default_end` int(11) DEFAULT 0 COMMENT 'เลข สุดท้ายของชุด barcode ดึงมาจาก config_barcode',
  `default_range` int(11) DEFAULT 0 COMMENT 'จำนวน barcode ทั้งหมด',
  `barcode_use` int(11) DEFAULT NULL,
  `config_remaining` int(11) DEFAULT NULL,
  `date_wk` date DEFAULT NULL,
  `del` int(11) DEFAULT 0,
  `date_purchase` date NOT NULL,
  `date_added` date DEFAULT NULL,
  `date_modify` date DEFAULT NULL,
  `change_qty` int(11) NOT NULL,
  `change_end` int(11) NOT NULL,
  `round` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_group`) USING BTREE,
  KEY `id_group` (`id_group`) USING BTREE,
  KEY `group_code` (`group_code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of mb_master_group
-- ----------------------------
BEGIN;
INSERT INTO `mb_master_group` VALUES (1, 1, 'A01', 'A0100000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-15', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (2, 1, 'A06', 'A0600000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-14', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (3, 1, 'A07', 'A0700000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (4, 1, 'A08', 'A0800000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (5, 1, 'A09', 'A0900000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (6, 1, 'A15', 'A1500000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (7, 1, 'A16', 'A1600000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (8, 1, 'A17', 'A1700000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (9, 1, 'A18', 'A1800000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (10, 1, 'A19', 'A1900000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (11, 1, 'A25', 'A2500000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (12, 1, 'A26', 'A2600000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (13, 1, 'A27', 'A2700000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (14, 1, 'A28', 'A2800000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (15, 1, 'A29', 'A2900000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (16, 1, 'A35', 'A3500000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (17, 1, 'A36', 'A3600000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (18, 1, 'A37', 'A3700000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (19, 1, 'A38', 'A3800000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (20, 1, 'A39', 'A3900000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (21, 1, 'A45', 'A4500000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (22, 1, 'A46', 'A4600000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (23, 1, 'A47', 'A4700000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (24, 1, 'A48', 'A4800000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (25, 1, 'A49', 'A4900000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (26, 1, 'A55', 'A5500000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (27, 1, 'A56', 'A5600000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (28, 1, 'A57', 'A5700000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (29, 1, 'A58', 'A5800000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (30, 1, 'A59', 'A5900000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (31, 1, 'A65', 'A6500000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (32, 1, 'A66', 'A6600000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (33, 1, 'A67', 'A6700000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (34, 1, 'A68', 'A6800000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (35, 1, 'A69', 'A6900000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (36, 1, 'A75', 'A7500000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (37, 1, 'A76', 'A7600000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (38, 1, 'A77', 'A7700000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (39, 1, 'A78', 'A7800000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (40, 1, 'A79', 'A7900000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (41, 1, 'A85', 'A8500000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (42, 1, 'A86', 'A8600000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (43, 1, 'A87', 'A8700000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (44, 1, 'A88', 'A8800000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (45, 1, 'A89', 'A8900000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (46, 1, 'A95', 'A9500000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (47, 1, 'A96', 'A9600000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (48, 1, 'A97', 'A9700000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (49, 1, 'A98', 'A9800000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
INSERT INTO `mb_master_group` VALUES (50, 1, 'A99', 'A9900000', '0', 0, 0, 99999, 10000, 0, 0, NULL, 0, '0000-00-00', '2021-07-13', '2021-07-13', 0, 0, NULL);
COMMIT;

-- ----------------------------
-- Table structure for mb_master_history
-- ----------------------------
DROP TABLE IF EXISTS `mb_master_history`;
CREATE TABLE `mb_master_history` (
  `id_history` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_group` int(11) DEFAULT NULL,
  `barcode_start` int(11) DEFAULT NULL,
  `barcode_end` int(11) DEFAULT NULL,
  `barcode_qty` int(11) DEFAULT NULL,
  `barcode_use` int(11) DEFAULT 0,
  `date_purchase` date DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_modify` datetime DEFAULT NULL,
  `del` int(11) DEFAULT 0,
  PRIMARY KEY (`id_history`),
  KEY `id_group` (`id_group`) USING BTREE,
  KEY `barcode_start` (`barcode_start`) USING BTREE,
  KEY `barcode_end` (`barcode_end`) USING BTREE,
  KEY `barcode_qty` (`barcode_qty`) USING BTREE,
  KEY `date_purchase` (`date_purchase`) USING BTREE,
  KEY `date_received` (`date_received`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of mb_master_history
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for mb_master_product
-- ----------------------------
DROP TABLE IF EXISTS `mb_master_product`;
CREATE TABLE `mb_master_product` (
  `id_product` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_group` int(11) DEFAULT NULL,
  `size_product_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sum_product` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_name` int(11) DEFAULT NULL,
  `remaining_qty` int(11) DEFAULT NULL,
  `propose` int(11) DEFAULT NULL,
  `propose_remaining_qty` int(11) DEFAULT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_wk` date DEFAULT NULL,
  `date_added` date DEFAULT NULL,
  `date_modify` date DEFAULT NULL,
  PRIMARY KEY (`id_product`) USING BTREE,
  KEY `id_product` (`id_product`) USING BTREE,
  KEY `id_group` (`id_group`) USING BTREE,
  KEY `size_product_code` (`size_product_code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=397 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of mb_master_product
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for mb_master_user
-- ----------------------------
DROP TABLE IF EXISTS `mb_master_user`;
CREATE TABLE `mb_master_user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `id_user_group` int(11) DEFAULT NULL,
  `username` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_modify` datetime DEFAULT NULL,
  `date_last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id_user`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of mb_master_user
-- ----------------------------
BEGIN;
INSERT INTO `mb_master_user` VALUES (1, 1, 'fsoftpro', '81dc9bdb52d04dc20036dbd8313ed055', NULL, NULL, '2021-07-14 23:34:18');
INSERT INTO `mb_master_user` VALUES (2, 2, 'admin', '827ccb0eea8a706c4c34a16891f84e7b', NULL, NULL, '2021-07-08 22:14:43');
INSERT INTO `mb_master_user` VALUES (6, 2, 'h311261', 'b59c67bf196a4758191e42f76670ceba', '2020-12-22 09:25:56', '2020-12-22 09:25:56', '2021-02-25 12:51:21');
INSERT INTO `mb_master_user` VALUES (7, 3, 'h343832', 'b59c67bf196a4758191e42f76670ceba', '2020-12-22 09:26:15', '2020-12-22 09:26:15', NULL);
COMMIT;

-- ----------------------------
-- Table structure for mb_master_user_group
-- ----------------------------
DROP TABLE IF EXISTS `mb_master_user_group`;
CREATE TABLE `mb_master_user_group` (
  `id_user_group` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_modify` datetime DEFAULT NULL,
  PRIMARY KEY (`id_user_group`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of mb_master_user_group
-- ----------------------------
BEGIN;
INSERT INTO `mb_master_user_group` VALUES (1, 'super admin', NULL, NULL);
INSERT INTO `mb_master_user_group` VALUES (2, 'admin', NULL, NULL);
INSERT INTO `mb_master_user_group` VALUES (3, 'user', NULL, NULL);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
