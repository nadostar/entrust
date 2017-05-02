-- create database survey_log
DROP DATABASE IF EXISTS `survey_log`;
CREATE DATABASE IF NOT EXISTS `survey_log` DEFAULT CHARACTER SET utf8;

--
USE `survey_log`;

-- drop tables
DROP TABLE IF EXISTS `admin_log`;
DROP TABLE IF EXISTS `access_log`;
DROP TABLE IF EXISTS `error_report`;

-- admin_log
CREATE TABLE IF NOT EXISTS `admin_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `day` varchar(2) NOT NULL,
  `admin_id` varchar(7) DEFAULT NULL,
  `category` varchar(32) DEFAULT NULL,
  `data` varchar(1024) DEFAULT NULL,
  `ip_address` varchar(16) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`day`),
  KEY `idx_admin_log_admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
/*!50500 PARTITION BY LIST COLUMNS(`day`)
(PARTITION admin_log_01 VALUES IN ('01') ENGINE = InnoDB,
 PARTITION admin_log_02 VALUES IN ('02') ENGINE = InnoDB,
 PARTITION admin_log_03 VALUES IN ('03') ENGINE = InnoDB,
 PARTITION admin_log_04 VALUES IN ('04') ENGINE = InnoDB,
 PARTITION admin_log_05 VALUES IN ('05') ENGINE = InnoDB,
 PARTITION admin_log_06 VALUES IN ('06') ENGINE = InnoDB,
 PARTITION admin_log_07 VALUES IN ('07') ENGINE = InnoDB,
 PARTITION admin_log_08 VALUES IN ('08') ENGINE = InnoDB,
 PARTITION admin_log_09 VALUES IN ('09') ENGINE = InnoDB,
 PARTITION admin_log_10 VALUES IN ('10') ENGINE = InnoDB,
 PARTITION admin_log_11 VALUES IN ('11') ENGINE = InnoDB,
 PARTITION admin_log_12 VALUES IN ('12') ENGINE = InnoDB,
 PARTITION admin_log_13 VALUES IN ('13') ENGINE = InnoDB,
 PARTITION admin_log_14 VALUES IN ('14') ENGINE = InnoDB,
 PARTITION admin_log_15 VALUES IN ('15') ENGINE = InnoDB,
 PARTITION admin_log_16 VALUES IN ('16') ENGINE = InnoDB,
 PARTITION admin_log_17 VALUES IN ('17') ENGINE = InnoDB,
 PARTITION admin_log_18 VALUES IN ('18') ENGINE = InnoDB,
 PARTITION admin_log_19 VALUES IN ('19') ENGINE = InnoDB,
 PARTITION admin_log_20 VALUES IN ('20') ENGINE = InnoDB,
 PARTITION admin_log_21 VALUES IN ('21') ENGINE = InnoDB,
 PARTITION admin_log_22 VALUES IN ('22') ENGINE = InnoDB,
 PARTITION admin_log_23 VALUES IN ('23') ENGINE = InnoDB,
 PARTITION admin_log_24 VALUES IN ('24') ENGINE = InnoDB,
 PARTITION admin_log_25 VALUES IN ('25') ENGINE = InnoDB,
 PARTITION admin_log_26 VALUES IN ('26') ENGINE = InnoDB,
 PARTITION admin_log_27 VALUES IN ('27') ENGINE = InnoDB,
 PARTITION admin_log_28 VALUES IN ('28') ENGINE = InnoDB,
 PARTITION admin_log_29 VALUES IN ('29') ENGINE = InnoDB,
 PARTITION admin_log_30 VALUES IN ('30') ENGINE = InnoDB,
 PARTITION admin_log_31 VALUES IN ('31') ENGINE = InnoDB)*/;

-- access_log
CREATE TABLE `access_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `day` varchar(2) NOT NULL,
  `accesskey` varchar(11) NOT NULL,
  `kind` tinyint(1) NOT NULL DEFAULT '0',
  `data` text,
  `ip_address` varchar(16) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
/*!50500 PARTITION BY LIST COLUMNS(`day`)
(PARTITION access_log_01 VALUES IN ('01') ENGINE = InnoDB,
 PARTITION access_log_02 VALUES IN ('02') ENGINE = InnoDB,
 PARTITION access_log_03 VALUES IN ('03') ENGINE = InnoDB,
 PARTITION access_log_04 VALUES IN ('04') ENGINE = InnoDB,
 PARTITION access_log_05 VALUES IN ('05') ENGINE = InnoDB,
 PARTITION access_log_06 VALUES IN ('06') ENGINE = InnoDB,
 PARTITION access_log_07 VALUES IN ('07') ENGINE = InnoDB,
 PARTITION access_log_08 VALUES IN ('08') ENGINE = InnoDB,
 PARTITION access_log_09 VALUES IN ('09') ENGINE = InnoDB,
 PARTITION access_log_10 VALUES IN ('10') ENGINE = InnoDB,
 PARTITION access_log_11 VALUES IN ('11') ENGINE = InnoDB,
 PARTITION access_log_12 VALUES IN ('12') ENGINE = InnoDB,
 PARTITION access_log_13 VALUES IN ('13') ENGINE = InnoDB,
 PARTITION access_log_14 VALUES IN ('14') ENGINE = InnoDB,
 PARTITION access_log_15 VALUES IN ('15') ENGINE = InnoDB,
 PARTITION access_log_16 VALUES IN ('16') ENGINE = InnoDB,
 PARTITION access_log_17 VALUES IN ('17') ENGINE = InnoDB,
 PARTITION access_log_18 VALUES IN ('18') ENGINE = InnoDB,
 PARTITION access_log_19 VALUES IN ('19') ENGINE = InnoDB,
 PARTITION access_log_20 VALUES IN ('20') ENGINE = InnoDB,
 PARTITION access_log_21 VALUES IN ('21') ENGINE = InnoDB,
 PARTITION access_log_22 VALUES IN ('22') ENGINE = InnoDB,
 PARTITION access_log_23 VALUES IN ('23') ENGINE = InnoDB,
 PARTITION access_log_24 VALUES IN ('24') ENGINE = InnoDB,
 PARTITION access_log_25 VALUES IN ('25') ENGINE = InnoDB,
 PARTITION access_log_26 VALUES IN ('26') ENGINE = InnoDB,
 PARTITION access_log_27 VALUES IN ('27') ENGINE = InnoDB,
 PARTITION access_log_28 VALUES IN ('28') ENGINE = InnoDB,
 PARTITION access_log_29 VALUES IN ('29') ENGINE = InnoDB,
 PARTITION access_log_30 VALUES IN ('30') ENGINE = InnoDB,
 PARTITION access_log_31 VALUES IN ('31') ENGINE = InnoDB)*/;

-- error_report
CREATE TABLE IF NOT EXISTS `error_report` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pid` varchar(8) DEFAULT NULL,
  `kind` varchar(16) DEFAULT NULL,
  `code` varchar(5) DEFAULT NULL,
  `message` varchar(256) DEFAULT NULL,
  `data` text DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DELIMITER $$

-- usp_truncate_partition_daily
DROP PROCEDURE IF EXISTS `usp_truncate_partition_daily` $$
CREATE PROCEDURE `usp_truncate_partition_daily`()
BEGIN

    SET @day = date_format((date_sub(now(), interval 27 day)), '%d');
    SELECT concat('truncate partition ', @day, '...') as comment;

    SET @sql1 = concat('ALTER TABLE `admin_log` TRUNCATE PARTITION `admin_log_', @day, '`');
    SET @sql2 = concat('ALTER TABLE `access_log` TRUNCATE PARTITION `access_log_', @day, '`');

    PREPARE stmt1 FROM @sql1;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;

    PREPARE stmt2 FROM @sql2;
    EXECUTE stmt2;
    DEALLOCATE PREPARE stmt2;
END$$


-- e_truncate_partition_daily
DROP EVENT IF EXISTS `e_truncate_partition_daily`$$
CREATE EVENT `e_truncate_partition_daily`
    ON SCHEDULE EVERY '1' DAY
    STARTS str_to_date( date_format(now(), '%Y%m%d 0300'), '%Y%m%d %H%i' ) + INTERVAL 1 DAY
    DO
        CALL usp_truncate_partition_daily()$$

-- call usp_truncate_partition_daily()

DELIMITER ;