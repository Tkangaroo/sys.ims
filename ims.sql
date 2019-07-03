
CREATE DATABASE `ims` /*!40100 COLLATE 'utf8mb4_bin' */;
USE `ims`;
# 创建IP白名单表记录
CREATE TABLE `t_ip_white_list` (
	`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '主键ID',
	`ip_addr` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'IP地址',
	`is_enable` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否激活可用 0-否 1-是',
	`comments` CHAR(50) NOT NULL DEFAULT '' COMMENT '备注信息',
	`create_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间戳',
	`update_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间戳'
)ENGINE=MYISAM AUTO_INCREMENT=0 COMMENT='IP白名单控制访问权限白名单';
# ES系统管理人员表
CREATE TABLE `t_system_managers` (
	`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '主键ID',
	`account` CHAR(10) NOT NULL DEFAULT '' COMMENT '账号',
	`password` CHAR(32) NOT NULL DEFAULT '' COMMENT '密码',
	`phone` CHAR(11) NOT NULL DEFAULT '' COMMENT '手机号',
	`build_sign_salt` CHAR(4) NOT NULL DEFAULT 'ESAD' COMMENT '构造签名加盐(四位字母或数字)',
	`latest_login_ip` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最近登录IP',
	`latest_login_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最近登录时间戳',
	`create_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间戳',
	`update_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间戳'
)ENGINE=MYISAM AUTO_INCREMENT=0 COMMENT='系统管理人员';
# 创建系统服务客户表
CREATE TABLE `t_service_customers` (
  `id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '主键ID',
  `customer_name` CHAR(10) NOT NULL DEFAULT '' COMMENT '客户名',
  `customer_contact_phone` CHAR(11) NOT NULL DEFAULT '' COMMENT '客户联系电话',
  `customer_company_name` CHAR(100) NOT NULL DEFAULT '' COMMENT '客户公司名',
  `customer_id` CHAR(6) NOT NULL DEFAULT '' COMMENT '客户ID ES+四位数字',
  `customer_es_key` CHAR(8) NOT NULL DEFAULT '' COMMENT '客户ES系统KEY, 生成方法: 客户名+客户ID',
  `is_enable` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否激活可用 0-否 1-是',
  `stock_update_callback_url` CHAR(50) NOT NULL DEFAULT '' COMMENT '库存更新通知回调地址',
	`comments` CHAR(50) NOT NULL DEFAULT '' COMMENT '备注信息',
	`create_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间戳',
	`update_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间戳'
)ENGINE=MYISAM AUTO_INCREMENT=0 COMMENT='系统服务客户表';
# 创建库存表
CREATE TABLE `t_sku`(
	`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '主键ID',
	`spu_no` CHAR(13) NOT NULL DEFAULT '' COMMENT 'SPU编码',
	`sku_no` CHAR(13) NOT NULL DEFAULT '' COMMENT 'SKU编码',
	`stock` INT(11) NOT NULL DEFAULT 0 COMMENT 'SKU库存',
	`stock_sellable` INT(11) NOT NULL DEFAULT 0 COMMENT 'SKU可售库存',
	`comments` CHAR(50) NOT NULL DEFAULT '' COMMENT '备注信息，例如名称、度数等',
	`create_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间戳',
	`update_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间戳'
)ENGINE=MYISAM AUTO_INCREMENT=0 COMMENT='库存表';
# 创建库存更新记录表
CREATE TABLE `t_stock_modify_log`(
  `id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '主键ID',
	`sku_no` CHAR(13) NOT NULL DEFAULT '' COMMENT 'SKU编码',
	`before_modify_stock` INT(11) NOT NULL DEFAULT 0 COMMENT '更新之前SKU库存',
	`after_modify_stock` INT(11) NOT NULL DEFAULT 0 COMMENT '更新之后SKU库存',
	`request_str` TEXT COMMENT '请求记录串',
	`response_str` TEXT COMMENT '响应记录串',
	`from_ip_addr` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源IP地址',
  `comments` CHAR(50) NOT NULL DEFAULT '' COMMENT '备注信息，例如订单号什么的',
	`modify_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间戳'
)ENGINE=MYISAM AUTO_INCREMENT=0 COMMENT='库存更新记录表';
