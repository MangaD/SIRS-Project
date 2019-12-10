# Setup Smartphone

**NOTE**: Due to time constraints and prioritizing the security aspect of this project, we did not develop an actual Android application, we developed a Desktop application instead. In theory we'd use WPA2 or WPA3 for the wireless protocol in order to assure confidentiality, integrity and authenticity.

Make sure to have [installed the VMs](InstallVMs.md) first.

## Install JDK + Gradle

```sh
sudo apt update && sudo apt upgrade
sudo apt install default-jdk gradle
```

## Install and run application

```sh
# Get files
cd ~
git clone https://github.com/MangaD/SIRS-Project
cd SIRS-Project/smartphone
gradle clean run
```