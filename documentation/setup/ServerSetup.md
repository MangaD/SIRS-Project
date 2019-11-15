# Setup Server

Make sure to have [installed the VMs](InstallVMs.md) first.

```sh
sudo apt update && sudo apt upgrade
sudo apt install apache2 mariadb-server php libapache2-mod-php php-mysql

sudo mysql_secure_installation
# Validate password plugin: n
# Everything else: y

sudo nano /etc/apache2/mods-enabled/dir.conf
# Move index.php to first in the list
sudo systemctl restart apache2
```

