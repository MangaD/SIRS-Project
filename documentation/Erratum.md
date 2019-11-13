# Erratum

## Generating key pair

Private keys should be known only by the owner, so it should be the client creating the key pair and providing his public key to the server upon registration.

We also forgot to mention that we'll be using RSA with 2048-bit keys.

## Why use Diffie-Hellman?

The advantage of using Diffie-Hellman is that [it provides perfect forward secrecy](https://crypto.stackexchange.com/questions/66202/what-is-perfect-forward-secrecy).

## Register

Our proposal was wrong in using Diffie-Hellman for this step because [Diffie-Hellman is vulnerable to man-in-the-middle attacks](https://stackoverflow.com/questions/10471009/how-does-the-man-in-the-middle-attack-work-in-diffie-hellman) and we have no way to authenticate the client before he registers (BUT WE AUTHENTICATE THE SERVER????).

Therefore, in order to register, the client shall first request the server's certificate and public key - we assume that the certificate authority is trusted - then the client shall generate a random AES key which will be used to encrypt his registering data and this key shall be encrypted with the server's public key. Then the client shall send this encrypted data and encrypted key to the server.

## Login

We did not add in our proposal brute force prevention through limiting login attempts within a certain time interval, but would be nice to have.

Diffie-Hellman does not provide authenticity, so both entities must sign their shared public values with their private keys.



## Messages

We did not add in our proposal non-repudiation and freshness guarantees, but this can be achieved through logging user actions, having a nonce and timestamp sent encrypted with each message and signed message hash.