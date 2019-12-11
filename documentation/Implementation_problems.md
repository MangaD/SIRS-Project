# Implementation Problems

1. **Duo registration**

   Currently the user's device (smartphone, etc) is associated with Duo at first login. It should be done at registration to avoid an attacker finding the user's login password and associating his device with the account, should the attacker make the first login. 

   However, because inserting the user in our database could fail after associating the user's device with Duo, this would require deleting the user's account at Duo's Admin panel. For this, we'd need to use a PHP third-party library for making REST requests to the Duo's API and integrate it with our Duo account.

   This would give us some work to implement and is not the most important security fault of this project, so we chose to focus on other aspects instead given the time we had available.
   
2. **Confidentiality**

    Despite messages being encrypted it is still visible what resources we're requesting (e.g. login.php, files.php...). This could be solved by having one PHP file for receiving all of the messages. Still, it is always possible to see that a communication between the client and server is going on, even if it is not possible to know the content.
    
3. **JavaScript Crypto**

   We considered using the smartphone to take care of all the cryptographic aspects of the client's needs, but later found out that JavaScript has a cryptographic API called [SubtleCrypto](https://developer.mozilla.org/en-US/docs/Web/API/SubtleCrypto).

4. **Encrypt files on upload**

   Due to time constraints we were unable to implement encryption of the file on upload.