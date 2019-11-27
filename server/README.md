# Server

### Database credentials

Database: sirs  
Username: sirs  
Password: sirs

## Install

[../documentation/setup/ServerSetup.md](../documentation/setup/ServerSetup.md)

1. `chmod` all directories 755 and files 644. Except `inc/config.php` which should be `chmod` 777 at first and `files` which should be `chmod` 777.

2. Create a mysql user and a database for the application.

   ```sh
   sudo mysql
   > CREATE DATABASE `sirs` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   > CREATE USER 'sirs'@'localhost' IDENTIFIED BY 'sirs';
   > GRANT ALL PRIVILEGES ON `sirs` . * TO 'sirs'@'localhost';
   > FLUSH PRIVILEGES;
   ```

3. Run install script by going to http(s)://your-url/server/install

4. `chmod` 400 `inc/config.php`

5. Remove `install` and `uninstall` directories or place a `.htaccess` file in them with `deny from all` so that they become inaccessible.

## Debugging

1. Create `php-error.log` file and `chmod` it with 777.
2. Put the following on top of your PHP code:
    ```php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    ini_set("log_errors", 1);
    // Probably need to change path
    ini_set("error_log", "/srv/http/server/php-error.log");
    // For testing
    error_log( "Hello, errors!" );
    ```
