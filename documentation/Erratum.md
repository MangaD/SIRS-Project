# Methodology

## Register

Our proposal was wrong in using Diffie-Hellman for this step because Diffie-Hellman is vulnerable to man-in-the-middle and we have no way to authenticate the client before he registers.

Therefore, in order to register, the client shall first request the server's certificate and public key - we assume that the certificate authority is trusted - then the client shall generate a random AES key which will be used to encrypt his registering data and this key shall be encrypted with the server's public key. Then the client shall send this encrypted data and encrypted key to the server.

## Login

We did not add in our proposal brute force prevention through limiting login attempts within a certain time interval, but would be nice to have.

Diffie-Hellman does not provide authenticity, so we need to.... __**TODO**__

## Messages

We did not add in our proposal non-repudiation guarantees, but this can be achieved through logging user actions, having a nonce and timestamp sent encrypted with each message and signed message hash.