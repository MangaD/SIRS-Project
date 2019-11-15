# Create data base
CREATE DATABASE IF NOT EXISTS `sirs` CHARACTER SET utf8 COLLATE utf8_unicode_ci;

# Create user accessing from localhost
CREATE USER 'sirs'@'localhost' IDENTIFIED BY 'password';

# Create user accessing from remote hosts
CREATE USER 'sirs'@'%' IDENTIFIED BY 'password';

# Grant usages
GRANT USAGE ON * . * TO 'sirs'@'localhost' IDENTIFIED BY 'password' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;
GRANT USAGE ON * . * TO 'sirs'@'%' IDENTIFIED BY 'password' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

# Grant privileges
GRANT ALL PRIVILEGES ON `sirs` . * TO 'sirs'@'localhost';
GRANT ALL PRIVILEGES ON `sirs` . * TO 'sirs'@'%';

FLUSH PRIVILEGES;
