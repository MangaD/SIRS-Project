# Setup Client

Make sure to have [installed the VMs](InstallVMs.md) first.

## Install Apache

```sh
sudo apt update && sudo apt upgrade
sudo apt install apache2
sudo systemctl start apache2
```

## Add website

```sh
# Get files
cd ~
git clone https://github.com/MangaD/SIRS-Project
sudo mv SIRS-Project/ /var/www/html/project
cd /var/www/html/

# Access website in browser:
# http://127.0.0.1/project/client
```