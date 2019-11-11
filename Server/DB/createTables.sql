CREATE TABLE `sirs`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(40) NOT NULL UNIQUE,
  `password` VARCHAR(64) NOT NULL,
  `phoneNumber` VARCHAR(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE `sirs`.`roles` (
  `role` INT NOT NULL,
  `friendlyName` VARCHAR(5) NOT NULL UNIQUE,
  PRIMARY KEY (`role`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE `sirs`.`users-permissions` (
  `role` INT NOT NULL,
  `id` INT NOT NULL,
  PRIMARY KEY (`id`, `role`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

INSERT INTO `sirs`.`roles` (`role`, `friendlyName`) VALUES ('1', 'admin');
INSERT INTO `sirs`.`roles` (`role`, `friendlyName`) VALUES ('2', 'user');

INSERT INTO `sirs`.`users` (`username`, `password`, `phoneNumber`) VALUES ('admin', 'admin', '911234567');

INSERT INTO `sirs`.`users-permissions` (`role`, `id`) VALUES ('1', '1');
