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

 Date: 15/07/2021 00:35:12
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

SET FOREIGN_KEY_CHECKS = 1;
