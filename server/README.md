# Server

## Install

[../documentation/setup/ServerSetup.md](../documentation/setup/ServerSetup.md)

## Debugging

1. Create `php-error.log` file and `chmod` it with 777.
2. Put the following on top of your PHP code:
    ```sh
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    ini_set("log_errors", 1);
    // Probably need to change path
    ini_set("error_log", "/srv/http/server/php-error.log");
    // For testing
    error_log( "Hello, errors!" );
    ```
