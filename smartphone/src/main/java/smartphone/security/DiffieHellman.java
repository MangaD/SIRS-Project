package smartphone.security;

import java.io.IOException;
import java.math.BigInteger;
import java.security.InvalidAlgorithmParameterException;
import java.security.InvalidKeyException;
import java.security.KeyFactory;
import java.security.KeyPair;
import java.security.KeyPairGenerator;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.security.PublicKey;
import java.security.spec.InvalidKeySpecException;
import java.security.spec.X509EncodedKeySpec;

import javax.crypto.KeyAgreement;
import javax.crypto.SecretKey;
import javax.crypto.spec.DHParameterSpec;
import javax.crypto.spec.SecretKeySpec;

// Interesting reads:
// https://stackoverflow.com/questions/18039401/how-can-i-transform-between-the-two-styles-of-public-key-format-one-begin-rsa/29707204
// https://stackoverflow.com/questions/58906789/how-to-use-the-public-key-generated-by-using-php-on-the-server

// Tutorial:
// https://docs.oracle.com/javase/7/docs/technotes/guides/security/crypto/CryptoSpec.html#DH2Ex
// https://stackoverflow.com/questions/34237971/conducting-diffie-hellman-between-java-and-crypto-c
public class DiffieHellman {
	
	private KeyPair keyPair;
	private byte[] sharedSecret;

	public String generateKeyPair(String pBase64, String gBase64, int l)
			throws NoSuchAlgorithmException, InvalidKeySpecException,
				InvalidAlgorithmParameterException, InvalidKeyException, IOException {
		
		BigInteger p = new BigInteger(Utility.base64ToBytes(pBase64));
		BigInteger g = new BigInteger(Utility.base64ToBytes(gBase64));
		
		DHParameterSpec dhParamFromRemotePubKey = new DHParameterSpec(p, g, l);
		// Bob creates his own DH key pair
		KeyPairGenerator kPairGen = KeyPairGenerator.getInstance("DH");
		kPairGen.initialize(dhParamFromRemotePubKey);
		keyPair = kPairGen.generateKeyPair();
		// Bob encodes his public key in PEM format, and sends it over to Alice.
		String pubKeyPEM = "-----BEGIN PUBLIC KEY-----\n";
		pubKeyPEM += Utility.bytesToBase64(keyPair.getPublic().getEncoded());
		pubKeyPEM += "\n-----END PUBLIC KEY-----";
		return pubKeyPEM;
	}
	
	public void generateSharedSecret(String pubKeyPEM) throws NoSuchAlgorithmException,
			InvalidKeyException, InvalidKeySpecException {

		byte[] pubKeyBytes = Utility.PEMtoPublicKeyBytes(pubKeyPEM);

		// Bob creates and initializes his DH KeyAgreement object
		KeyAgreement keyAgree = KeyAgreement.getInstance("DH");
		keyAgree.init(keyPair.getPrivate());
		
		KeyFactory keyFac = KeyFactory.getInstance("DH");
		X509EncodedKeySpec x509KeySpec = new X509EncodedKeySpec(pubKeyBytes);
		PublicKey pubKey = keyFac.generatePublic(x509KeySpec);
		keyAgree.doPhase(pubKey, true);
		sharedSecret = keyAgree.generateSecret();
	}
	
	public SecretKey generateAESFromSharedSecret() throws Exception {
		if (sharedSecret == null) {
			throw new Exception("Shared secret must be computed first");
		}
		
		// Generate key of 256 bits from key password entered by user
		MessageDigest digest = MessageDigest.getInstance("SHA-256");
		byte[] sharedSecretHash = digest.digest(sharedSecret);
		
		// Debug hash
		// https://stackoverflow.com/questions/5470219/get-md5-string-from-message-digest
		/*
		StringBuffer hexString = new StringBuffer();
		for (int i = 0; i < sharedSecretHash.length; i++) {
			if ((0xff & sharedSecretHash[i]) < 0x10) {
				hexString.append("0"
						+ Integer.toHexString((0xFF & sharedSecretHash[i])));
			} else {
				hexString.append(Integer.toHexString(0xFF & sharedSecretHash[i]));
			}
		}
		System.out.println(hexString);
		*/
		
		return new SecretKeySpec(sharedSecretHash, "AES");
	}
}
