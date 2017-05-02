-- create database survey_admin
DROP DATABASE IF EXISTS `survey_admin`;
CREATE DATABASE IF NOT EXISTS `survey_admin` DEFAULT CHARACTER SET utf8;

--
USE `survey_admin`;

-- drop tables
DROP TABLE IF EXISTS `admin`;
DROP TABLE IF EXISTS `permission`;
DROP TABLE IF EXISTS `permission_allow`;
DROP TABLE IF EXISTS `admin_permission`;

-- admin
CREATE TABLE IF NOT EXISTS `admin` (
	`id` varchar(7) NOT NULL,
	`email` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL, 
	`password` varchar(255) NOT NULL, 
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
  	PRIMARY KEY (`id`),
  	UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- permission
CREATE TABLE IF NOT EXISTS `permission` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`description` varchar(255) NOT NULL,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- permission_allow
CREATE TABLE IF NOT EXISTS `permission_allow` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`permission_id` int(11) NOT NULL,
	`allow_no` int(11) NOT NULL,
	`roles` varchar(10) NOT NULL,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	PRIMARY KEY (`id`),
	KEY `idx_permission_allow_no` (`permission_id`, `allow_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- admin_permission
CREATE TABLE IF NOT EXISTS `admin_permission` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`admin_id` varchar(7) NOT NULL,
	`permission_id` int(11) NOT NULL,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
