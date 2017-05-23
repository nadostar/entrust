use `survey`;

DROP TABLE IF EXISTS `invoice`;
CREATE TABLE IF NOT EXISTS `invoice` (
	`id` varchar(20) NOT NULL,
	`pid` varchar(8) NOT NULL,
	`tid` varchar(8) NOT NULL,
	`sample` int(11) NOT NULL DEFAULT '0',
	`price` float NOT NULL DEFAULT '0',
	`other_free` float NOT NULL DEFAULT '0',
	`quantity` int(11) NOT NULL DEFAULT '0',
	`remark` varchar(512),
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	`last_updated_user` varchar(32) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `idx_invoice_pid` (`pid`),
	KEY `idx_invoice_tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;