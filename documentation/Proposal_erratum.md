# Proposal erratum

## Generating key pair

Private keys should be known only by the owner, so it should be the client creating the key pair and providing his public key to the server upon registration.

We also forgot to mention that we'll be using RSA with 2048-bit keys.

## Why use Diffie-Hellman?

The advantage of using Diffie-Hellman is that [it provides perfect forward secrecy](https://crypto.stackexchange.com/questions/66202/what-is-perfect-forward-secrecy). Because a new secret key is generated for each session, the compromise of one secret key will only compromise one session. And because the session key is never transmitted in the network nor dependent on long-term keys, the compromise of long-term private keys will not compromise past session keys.

However, [Diffie-Hellman is vulnerable to man-in-the-middle attacks](https://stackoverflow.com/questions/10471009/how-does-the-man-in-the-middle-attack-work-in-diffie-hellman), so both entities must sign their shared public values with their private keys.

## Using private key

For extra security, the private key should never leave the smartphone. Everytime the client requires this key to perform an action, it should be the smartphone performing that action and giving the client the result.

## Register

Because Diffie-Hellman is vulnerable to man-in-the-middle, it is important that the server signs his shared public values. Therefore, in order to register, the client shall first request the server's certificate and public key - we assume that the certificate authority is trusted. Even though an attacker could intercept the messages and establish a connection with the server, he could not deceive the client by impersonating the server, which is what matters. He could only DoS the client. And because no clients can authenticate before the server before they register, this does not pose a problem.

## Login

We did not add in our proposal brute force prevention through limiting login attempts within a certain time interval, but would be nice to have.

## Messages

We did not add in our proposal non-repudiation and freshness guarantees.

Non-repudiation could be achieved through digital signatures (e.g. signing the message hashes), logging and freshness.

Freshness can be achieved by having a [nonce and timestamp](https://crypto.stackexchange.com/questions/41170/what-advantage-is-there-for-using-a-nonce-and-a-timestamp) sent encrypted with each message.

**Why hash the message with random padding?**

Since we're sending the hash encrypted together with the message, random padding is unnecessary. But if we were sending the hash separately, random padding would be important in order to make equal messages produce a different hash.