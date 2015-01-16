CREATE TABLE `lastlogin` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`confid` INT(11) NOT NULL,
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`guestname` VARCHAR(128) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `FK_lastlogin_conf` (`confid`),
	CONSTRAINT `FK_lastlogin_conf` FOREIGN KEY (`confid`) REFERENCES `conf` (`id`) ON DELETE CASCADE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;
