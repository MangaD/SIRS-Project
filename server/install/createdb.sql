SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


--
-- Table structure of `users`
--

CREATE TABLE IF NOT EXISTS `users` (
	`uid` int NOT NULL AUTO_INCREMENT,
	`username` varchar(50) NOT NULL,
	`password` varchar(255) NOT NULL,
	`admin` tinyint(1) NOT NULL DEFAULT 0,
	-- https://stackoverflow.com/questions/5133580/which-mysql-datatype-to-use-for-an-ip-address
	`last_ipv4` int unsigned DEFAULT NULL,
	`last_ipv6` binary(16) DEFAULT NULL,
	`created_by` int DEFAULT NULL,
	`created_at` datetime DEFAULT CURRENT_TIMESTAMP,
	`deleted` boolean DEFAULT FALSE,
	PRIMARY KEY (`uid`),
	UNIQUE (`username`),
	FOREIGN KEY (`created_by`) REFERENCES `users`(`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


