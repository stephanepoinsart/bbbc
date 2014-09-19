CREATE TABLE `conf` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`confname` CHAR(188) NOT NULL,
	`username` CHAR(63) NOT NULL,
	`conftype` TINYINT(4) NULL DEFAULT NULL,
	`createtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	INDEX `username` (`username`),
	UNIQUE INDEX `confname_username` (`confname`, `username`)
)
ENGINE=InnoDB
AUTO_INCREMENT=2;
