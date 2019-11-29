# Duo 2FA Setup

1. Register an account at [official Duo page](https://duo.com/)

2. In Duo's admin panel go to `Applications -> Protect an application`.

3. Search for `Web SDK` and pick `Protect Application`.

4. Duo generates the following variables which you'll need when installing the server:
    - Integration key;
    - Secret key;
    - API hostname;
    
5. Your `akey` is a random string that you generate and keep secret from Duo. It should be at least 40 characters long and stored alongside your Web SDK application's integration key (ikey) and secret key (skey) in the configuration file.

    Example: Generate random `akey` string with python:

    ```python
    import os, hashlib
    print(hashlib.sha256(os.urandom(64)).hexdigest());
    ```

### Links:

- [Duo Web Official Guide](https://duo.com/docs/duoweb)
- [Duo PHP source and demos](https://github.com/duosecurity/duo_php)
- [Troubleshooting to integrate with JS application](https://stackoverflow.com/questions/48109090/unable-to-integrate-duo-web-sdk-with-angular-application)