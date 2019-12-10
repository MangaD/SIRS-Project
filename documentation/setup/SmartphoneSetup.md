# Setup Smartphone

**NOTE**: Due to time constraints and prioritizing the security aspect of this project, we did not develop an actual Android application, we developed a Desktop application instead. In theory we'd use WPA2 or WPA3 for the wireless protocol in order to assure confidentiality, integrity and authenticity.

**NOTE 2:** On Windows you may need to install [Java Cryptography Extension (JCE)](https://www.oracle.com/technetwork/java/javase/downloads/jce8-download-2133166.html) because of [this](https://stackoverflow.com/questions/24907530/java-security-invalidkeyexception-illegal-key-size-or-default-parameters-in-and).

Make sure to have [installed the VMs](InstallVMs.md) first.

## Install JDK

```sh
sudo apt update && sudo apt upgrade
sudo apt install default-jdk
```

## Install latest Gradle (currently version 6.0.1)

```sh
sudo mkdir /opt/gradle
cd /opt/gradle
sudo wget https://services.gradle.org/distributions/gradle-6.0.1-bin.zip
sudo unzip gradle-6.0.1-bin.zip
sudo rm gradle-6.0.1-bin.zip
sudo nano /etc/profile.d/gradle.sh
```
Add the following to the end of the file:
```
export GRADLE_HOME=/opt/gradle/gradle-6.0.1
export PATH=${GRADLE_HOME}/bin:${PATH}
```
Make script executable and load:
```sh
sudo chmod +x /etc/profile.d/gradle.sh
cd ~
source /etc/profile.d/gradle.sh
gradle -v
```

## Install and run application

```sh
# Get files
cd ~
git clone https://github.com/MangaD/SIRS-Project
cd SIRS-Project/smartphone
gradle clean run
```