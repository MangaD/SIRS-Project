# Setup Server

Make sure to have [installed the VMs](InstallVMs.md) first.

## Install LAMP

```sh
sudo apt update && sudo apt upgrade
sudo apt install apache2 mariadb-server php libapache2-mod-php php-mysql

sudo mysql_secure_installation
# Validate password plugin: n
# Everything else: y

sudo nano /etc/apache2/mods-enabled/dir.conf
# Move index.php to first in the list

sudo nano /etc/apache2/apache2.conf
# Under '<Directory /var/www/>' change 'AllowOverride None' to 'AllowOverride All'

sudo systemctl restart apache2
```

## Add website

```sh
# Get files
cd ~
git clone https://github.com/MangaD/SIRS-Project
sudo mv SIRS-Project/ /var/www/html/project
cd /var/www/html/

# Setup database
sudo mysql
> CREATE DATABASE `sirs` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> CREATE USER 'sirs'@'localhost' IDENTIFIED BY 'sirs';
> GRANT ALL PRIVILEGES ON `sirs` . * TO 'sirs'@'localhost';
> FLUSH PRIVILEGES;
# Access website in browser:
# http://127.0.0.1/project/server/install
```