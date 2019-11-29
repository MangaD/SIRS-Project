2 Factor Authentication

1.Register your account in official Duo page: https://duo.com/

2.Login with your Duo account.

3.Go to the left menu and select "Applications" and select "Protect an application"

4.Search for "Web SDK" and choose "Protect Application"

5.Dou generate the following variables: -Integration key; -Secret key; -API hostname;

6.Keep track of those 3 variables.

7.In config.php file in server side, replace IKEY, SK and HOST values for the ones that Duo generated.

8.Your akey is a string that you generate and keep secret from Duo. It should be at least 40 characters long and stored alongside your Web SDK application's integration key (ikey) and secret key (skey) in the configuration file.
Generate random akey string with python:

import os, hashlib
print(hashlib.sha256(os.urandom(32)).hexdigest());


Links:
	
	//Official guide to use Duo
	https://duo.com/docs/duoweb
	
	//Repository to Duo src and Demos
	https://github.com/duosecurity/duo_php

	//Troubleshooting to integrate with JS application
	https://stackoverflow.com/questions/48109090/unable-to-integrate-duo-web-sdk-with-angular-application