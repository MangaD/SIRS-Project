# Implementation Problems

1. **Duo registration**

   Currently the user's device (smartphone, etc) is associated with Duo at first login. It should be done at registration to avoid an attacker finding the user's login password and associating his device with the account, should the attacker make the first login. 

   However, because inserting the user in our database could fail after associating the user's device with Duo, this would require deleting the user's account at Duo's Admin panel. For this, we'd need to use a PHP third-party library for making REST requests to the Duo's API and integrate it with our Duo account.

   This would give us some work to implement and is not the most important security fault of this project, so we chose to focus on other aspects instead given the time we had available.