-- create database survey
DROP DATABASE IF EXISTS `survey`;
CREATE DATABASE IF NOT EXISTS `survey` DEFAULT CHARACTER SET utf8;

-- 
USE `survey`;

-- drop tables
DROP TABLE IF EXISTS `project`;
DROP TABLE IF EXISTS `block`;
DROP TABLE IF EXISTS `link`;
DROP TABLE IF EXISTS `link_history`;
DROP TABLE IF EXISTS `partner`;
DROP TABLE IF EXISTS `access_keys`;
DROP TABLE IF EXISTS `stat`;
--DROP TABLE IF EXISTS `snapshot`;

-- project
CREATE TABLE IF NOT EXISTS `project` (
	`id` varchar(8) NOT NULL,
	`name` varchar(255) NOT NULL,
	`client` varchar(255) NOT NULL,
	`country` varchar(2) NOT NULL,
	`sales` varchar(255) NOT NULL,
	`type` tinyint(1) NOT NULL DEFAULT 0,
	`sample` int(11) NOT NULL DEFAULT '0',
	`free` int(11) NOT NULL DEFAULT '0',
	`ir` float NOT NULL DEFAULT '0',
	`cpi` float NOT NULL DEFAULT '0',
	`ip_access` tinyint(1) NOT NULL DEFAULT 0,
	`invoice` tinyint(1) NOT NULL DEFAULT 0,
	`payment` tinyint(1) NOT NULL DEFAULT 0,
	`start_at` datetime NOT NULL,
	`end_at` datetime NOT NULL,
	`disable` tinyint(1) NOT NULL DEFAULT 0,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- block
CREATE TABLE IF NOT EXISTS `block` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`accesskey` varchar(16) NOT NULL,
	`ip_address` varchar(16) NOT NULL,
	`data` text NOT NULL,
	`created_at` datetime DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY `idx_block_accesskey` (`accesskey`),
	KEY `idx_block_ip_address` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- link
CREATE TABLE IF NOT EXISTS `link` (
	`id` varchar(8) NOT NULL,
	`k` int(11) NOT NULL,
	`name` varchar(255) NOT NULL,
	`type` tinyint(1) NOT NULL DEFAULT 0,
	`url` varchar(1024) NOT NULL,
	`disable` tinyint(1) NOT NULL DEFAULT 0,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	`pid` varchar(8) NOT NULL,
	PRIMARY KEY (`id`, `k`),
	KEY `idx_link_pid` (`pid`),
	KEY `idx_link_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- link_history ver 2.0
CREATE TABLE IF NOT EXISTS `link_history` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`accesskey` varchar(16) NOT NULL,
	`progress` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Survey, 1: Complate, 2: Screenout, 3: Quotafull',
	`uid` varchar(32) NOT NULL,
	`disable` tinyint(1) NOT NULL DEFAULT 0,
	`created_at` datetime DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY `idx_link_history_accesskey` (`accesskey`),
	KEY `idx_link_history_progress` (`progress`),
	KEY `idx_link_history_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- partner
CREATE TABLE IF NOT EXISTS `partner` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`country` varchar(2) NOT NULL,
	`complate_url` varchar(1024) NOT NULL,
	`screenout_url` varchar(1024) NOT NULL,
	`quotafull_url` varchar(1024) NOT NULL,
	`sample_size` int(11) NOT NULL,
	`request_limit` int(11) NOT NULL,
	`status` tinyint(1) NOT NULL DEFAULT 0,
	`disable` tinyint(1) NOT NULL DEFAULT 0,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	`link_id` varchar(8) NOT NULL,
	`pid` varchar(8) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `idx_partner_link_id` (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- access_keys
CREATE TABLE IF NOT EXISTS `access_keys` (
	`access_key` varchar(16) NOT NULL,
	`pid` varchar(8) NOT NULL,
	`link_id` varchar(8) NOT NULL,
	`link_key` int(11) NOT NULL,
	`disable` tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`access_key`, `disable`),
	KEY `idx_access_keys_link_id` (`link_id`),
	KEY `idx_access_keys_pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- stat
CREATE TABLE IF NOT EXISTS `stat` (
	`pid` varchar(8) NOT NULL,
	`link_id` varchar(8) NOT NULL,
	`complate_count` int(11) NOT NULL DEFAULT 0,
	`screenout_count` int(11) NOT NULL DEFAULT 0,
	`quotafull_count` int(11) NOT NULL DEFAULT 0,
	PRIMARY KEY(`pid`, `link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
