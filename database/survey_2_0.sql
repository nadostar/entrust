-- create database survey
DROP DATABASE IF EXISTS `survey_2_0`;
CREATE DATABASE IF NOT EXISTS `survey_2_0` DEFAULT CHARACTER SET utf8;

-- 
USE `survey_2_0`;

-- drop tables
DROP TABLE IF EXISTS `task`;
DROP TABLE IF EXISTS `block`;
DROP TABLE IF EXISTS `link`;
DROP TABLE IF EXISTS `link_history`;
DROP TABLE IF EXISTS `partner`;
DROP TABLE IF EXISTS `accesskeys`;
DROP TABLE IF EXISTS `stat`;

-- task
CREATE TABLE IF NOT EXISTS `task` (
	`id` bigint(20) unsigned NOT NULL,
	`name` varchar(255) NOT NULL,
	`client` varchar(255) NOT NULL,
	`sales` varchar(255) NOT NULL,
	`country` varchar(255) NOT NULL,
	`type` tinyint(1) NOT NULL DEFAULT 0,
	`sample` int(11) NOT NULL DEFAULT 0,
	`free` int(11) NOT NULL DEFAULT 0,
	`ir` float NOT NULL DEFAULT 0,
	`cpi` float NOT NULL DEFAULT 0,
	`ip` tinyint(1) NOT NULL DEFAULT 0,
	`invoice` tinyint(1) NOT NULL DEFAULT 0,
	`payment` tinyint(1) NOT NULL DEFAULT 0,
	`start_at` datetime NOT NULL,
	`end_at` datetime NOT NULL,
	`status` tinyint(1) NOT NULL DEFAULT 0,
	`disable` tinyint(1) NOT NULL DEFAULT 0,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- block
CREATE TABLE IF NOT EXISTS `block` (
	`id` bigint(20) unsigned NOT NULL,
	`accesskey` varchar(11) NOT NULL,
	`ip` varchar(16) NOT NULL,
	`data` text NOT NULL,
	`created_at` datetime DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY `idx_block_accesskey` (`accesskey`),
	KEY `idx_block_ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- link
CREATE TABLE IF NOT EXISTS `link` (
	`id` bigint(20) unsigned NOT NULL,
	`key` int(11) NOT NULL,
	`name` varchar(255) NOT NULL,
	`type` tinyint(1) NOT NULL DEFAULT 0,
	`url` varchar(1024) NOT NULL,
	`disable` tinyint(1) NOT NULL DEFAULT 0,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	`task_id` bigint(20) unsigned NOT NULL,
	PRIMARY KEY (`id`,`key`),
	KEY `idx_link_task_id` (`task_id`),
	KEY `idx_link_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- link_history
CREATE TABLE IF NOT EXISTS `link_history` (
	`id` bigint(20) unsigned NOT NULL,
	`accesskey` varchar(11) NOT NULL,
	`progress` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0:Join in, 1:Complated, 2:Screenout, 3:Quotafull',
	`uid` varchar(32) NOT NULL,
	`disable` tinyint(1) NOT null DEFAULT 0,
	`created_at` datetime DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY `idx_link_history_accesskey` (`accesskey`),
	KEY `idx_link_history_progress` (`progress`),
	KEY `idx_link_history_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- partner
CREATE TABLE IF NOT EXISTS `partner` (
	`id` bigint(20) unsigned NOT NULL,
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
	`link_id` bigint(20) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `idx_partner_link_id` (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- accesskeys
CREATE TABLE IF NOT EXISTS `accesskeys` (
	`accesskey` varchar(11) NOT NULL,
	`task_id` bigint(20) unsigned NOT NULL,
	`link_id` bigint(20) unsigned NOT NULL,
	`link_key` int(11) NOT NULL,
	`disable` tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY(`accesskey`, `disable`)
	KEY `idx_accesskeys_task_id` (`task_id`),
	KEY `idx_accesskeys_link_id` (`link_id`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- stat
CREATE TABLE IF NOT EXISTS `stat` (
	`task_id` bigint(20) unsigned NOT NULL,
	`link_id` bigint(20) unsigned NOT NULL,
	`complate_count` int(11) NOT NULL DEFAULT 0,
	`screenout_count` INT(11) NOT NULL DEFAULT 0,
	`quotafull_count` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY (`task_id`, `link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;