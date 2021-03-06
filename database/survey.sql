-- create database survey
DROP DATABASE IF EXISTS `survey`;
CREATE DATABASE IF NOT EXISTS `survey` DEFAULT CHARACTER SET utf8;

-- 
USE `survey`;

-- drop tables
DROP TABLE IF EXISTS `project`;
DROP TABLE IF EXISTS `block`;
DROP TABLE IF EXISTS `link`;
DROP TABLE IF EXISTS `useful_link`;
DROP TABLE IF EXISTS `history`;
DROP TABLE IF EXISTS `partner`;
DROP TABLE IF EXISTS `stat`;
DROP TABLE IF EXISTS `share`;
DROP TABLE IF EXISTS `snapshot`;
DROP TABLE IF EXISTS `test`;

-- project
CREATE TABLE IF NOT EXISTS `project` (
	`id` varchar(8) NOT NULL,
	`name` varchar(255) NOT NULL,
	`client` varchar(255) NOT NULL,
	`country` varchar(2) NOT NULL,
	`sales` varchar(255) NOT NULL,
	`type` tinyint(1) NOT NULL DEFAULT 0,
	`sample` int(11) NOT NULL DEFAULT 0,
	`free` int(11) NOT NULL DEFAULT 0,
	`ir` float NOT NULL DEFAULT 0,
	`cpi` float NOT NULL DEFAULT 0,
	`ip_access` tinyint(1) NOT NULL DEFAULT 0,
	`invoice` tinyint(1) NOT NULL DEFAULT 0,
	`payment` tinyint(1) NOT NULL DEFAULT 0,
	`start_at` datetime NOT NULL,
	`end_at` datetime NOT NULL,
	`disable` tinyint(1) NOT NULL DEFAULT 0,
	`status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0:Pending, 1:Active, 2:Closed',
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- block
CREATE TABLE IF NOT EXISTS `block` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`pid` varchar(8) NOT NULL,
	`ip_address` varchar(16) NOT NULL,
	`data` text NOT NULL,
	`created_at` datetime DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY `idx_block_pid` (`pid`),
	KEY `idx_block_ip_address` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- link
CREATE TABLE IF NOT EXISTS `link` (
	`id` varchar(8) NOT NULL,
	`name` varchar(255) NOT NULL,
	`type` tinyint(1) NOT NULL DEFAULT 0,
	`disable` tinyint(1) NOT NULL DEFAULT 0,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	`pid` varchar(8) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `idx_link_pid` (`pid`),
	KEY `idx_link_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- useful_link
CREATE TABLE IF NOT EXISTS `useful_link` (
	`link_id` varchar(8) NOT NULL,
	`link_no` int(11) NOT NULL DEFAULT 0,
	`url` varchar(1024) NOT NULL,
	`useful` tinyint(1) NOT NULL DEFAULT 0,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	PRIMARY KEY (`link_id`, `link_no`),
	KEY `idx_useful_useful` (`useful`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- link_history ver 2.0
CREATE TABLE IF NOT EXISTS `history` (
	`accessid` varchar(64) NOT NULL,
	`accesskey` varchar(16) NOT NULL,
	`uid` varchar(64) NOT NULL,
	`url` varchar(255),
	`progress` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Survey, 1: Complate, 2: Screenout, 3: Quotafull',
	`disable` tinyint(1) NOT NULL DEFAULT 0,
	`created_at` datetime DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`accessid`),
	KEY `idx_history_accesskey` (`accesskey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- partner
CREATE TABLE IF NOT EXISTS `partner` (
	`id` varchar(8) NOT NULL,
	`name` varchar(255) NOT NULL,
	`country` varchar(2) NOT NULL,
	`complate_url` varchar(1024) NOT NULL,
	`screenout_url` varchar(1024) NOT NULL,
	`quotafull_url` varchar(1024) NOT NULL,
	`sample_size` int(11) NOT NULL DEFAULT 0,
	`hits_limit` int(11) NOT NULL DEFAULT 0,
	`status` tinyint(1) NOT NULL DEFAULT 0,
	`disable` tinyint(1) NOT NULL DEFAULT 0,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	`link_id` varchar(8) NOT NULL,
	`pid` varchar(8) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `idx_partner_link_id` (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- snapshot
CREATE TABLE IF NOT EXISTS `snapshot` (
	`accesskey` varchar(16) NOT NULL,
	`pid` varchar(8) NOT NULL,
	`link_id` varchar(8) NOT NULL,
	`partner_id` varchar(8) NOT NULL,
	`extra` text NOT NULL,
	PRIMARY KEY (`accesskey`),
	KEY `idx_snapshot_pid` (`pid`),
	KEY `idx_snapshot_link_id` (`link_id`),
	KEY `idx_snapshot_partner_id` (`partner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- stat
CREATE TABLE IF NOT EXISTS `stat` (
	`pid` varchar(8) NOT NULL,
	`link_id` varchar(8) NOT NULL,
	`partner_id` varchar(8) NOT NULL,
	`complate_count` int(11) NOT NULL DEFAULT 0,
	`screenout_count` int(11) NOT NULL DEFAULT 0,
	`quotafull_count` int(11) NOT NULL DEFAULT 0,
	PRIMARY KEY(`pid`, `link_id`, `partner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- share
CREATE TABLE IF NOT EXISTS `share` (
	`pid` varchar(8) NOT NULL,
	`admin_id` varchar(7) NOT NULL,
	`disable` tinyint(1) NOT NULL DEFAULT 0,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	PRIMARY KEY(`pid`, `admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `test` (
	`accessid` varchar(64) NOT NULL,
	`accesskey` varchar(16) NOT NULL,
	`uid` varchar(64) NOT NULL,
	`url` varchar(1024) NOT NULL,
	`created_at` datetime DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`accessid`),
	KEY `idx_test_accesskey` (`accesskey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
