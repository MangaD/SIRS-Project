package smartphone.security;

import java.security.cert.Certificate;
import java.security.InvalidKeyException;
import java.security.Key;
import java.security.KeyFactory;
import java.security.KeyPair;
import java.security.KeyPairGenerator;
import java.security.KeyStore;
import java.security.KeyStoreException;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.security.PrivateKey;
import java.security.PublicKey;
import java.security.SecureRandom;
import java.security.Signature;
import java.security.UnrecoverableEntryException;
import java.security.cert.CertificateException;
import java.security.cert.CertificateFactory;
import java.security.spec.InvalidKeySpecException;
import java.security.spec.PKCS8EncodedKeySpec;
import java.security.spec.X509EncodedKeySpec;

import javax.crypto.BadPaddingException;
import javax.crypto.Cipher;
import javax.crypto.IllegalBlockSizeException;
import javax.crypto.NoSuchPaddingException;
import javax.crypto.spec.SecretKeySpec;

import static java.nio.charset.StandardCharsets.UTF_8;

import java.io.ByteArrayInputStream;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;

/**
 * We use RSA because we did not find how to cipher with ECC public key
 * using javax.crypto.Cipher;
 * https://docs.oracle.com/javase/7/docs/api/javax/crypto/Cipher.html
 */

/**
 * Java ECC tutorial: https://stackoverflow.com/questions/11339788/tutorial-of-ecdsa-algorithm-to-sign-a-string
 * EC vs ECDH vc ECDSA: https://security.stackexchange.com/questions/190869/is-the-jdk-standard-ec-keypairgenerator-suitable-for-use-with-both-ecdsa-and-ecd
 * Convert key bytes to object: https://stackoverflow.com/questions/19353748/how-to-convert-byte-array-to-privatekey-or-publickey-type
 * Key store tutorial: http://tutorials.jenkov.com/java-cryptography/keystore.html
 * JAVA RSA example: https://gist.github.com/nielsutrecht/855f3bef0cf559d8d23e94e2aecd4ede
 */
public class AsymmetricEncryptionRSA {

    private Cipher cipher;
    private Signature signature;
    
    private static final int keysize = 2048;

    public AsymmetricEncryptionRSA() throws NoSuchAlgorithmException, NoSuchPaddingException {
        this.cipher = Cipher.getInstance("RSA");
        this.signature = Signature.getInstance("SHA256withRSA");
    }

    public static KeyPair generateKeyPair() throws NoSuchAlgorithmException {
        /**
         * Gets the type of algorithm to use in the key-pair generation.
         * Elliptical Curves (EC), RSA, ElGammal... EC is the key with
         * smaller size, more efficient with equivalent security.
         */
        KeyPairGenerator keyGen = KeyPairGenerator.getInstance("RSA");
        
        // https://stackoverflow.com/questions/27622625/securerandom-with-nativeprng-vs-sha1prng/27638413
        // getInstanceStrong() takes forever, don't use
        // Is SHA1PRNG secure? https://stackoverflow.com/questions/12731283/using-securerandom-with-sha-256
        SecureRandom random = SecureRandom.getInstance("SHA1PRNG");

        // Use 256 key size for EC and 2048 for RSA
        keyGen.initialize(keysize, random);
        KeyPair keys = keyGen.generateKeyPair();
        //Key pub = kp.getPublic();
        //Key pvt = kp.getPrivate();
        return keys;
    }
    
    public static void savePairInKeyStore(String filename,
    		KeyPair pair, String password)
    				throws KeyStoreException, FileNotFoundException, IOException,
    				       NoSuchAlgorithmException, CertificateException {
    	
    	// Can set a specific instance
    	KeyStore keyStore = KeyStore.getInstance(KeyStore.getDefaultType());
    	
    	// Password
    	char[] keyStorePassword = password.toCharArray();
    	KeyStore.ProtectionParameter kspp = new KeyStore.PasswordProtection(keyStorePassword);
    	
    	// Must load even if it doesn't exist (pass null)
    	keyStore.load(null, keyStorePassword);
    	
    	// Create certificate with public key
    	// https://www.codota.com/code/java/classes/java.security.cert.Certificate
    	CertificateFactory certificateFactory = CertificateFactory.getInstance("X509");
    	Certificate certificate = certificateFactory.generateCertificate(
    			new ByteArrayInputStream(pair.getPublic().getEncoded()));

    	
    	KeyStore.PrivateKeyEntry privKeyEntry =
    			new KeyStore.PrivateKeyEntry(pair.getPrivate(), new Certificate[] {certificate});
    	
    	keyStore.setEntry("privKey", privKeyEntry, kspp);
    	keyStore.setCertificateEntry("certificate", certificate);
    	
    	try (FileOutputStream keyStoreOutputStream = new FileOutputStream(filename)) {
    		keyStore.store(keyStoreOutputStream, keyStorePassword);
    	}
    }
    
    public static KeyStore savePairInKeyStore(KeyPair pair, String password)
    				throws KeyStoreException, NoSuchAlgorithmException, CertificateException, IOException {
    	
    	// Can set a specific instance
    	KeyStore keyStore = KeyStore.getInstance(KeyStore.getDefaultType());
    	
    	// Password
    	char[] keyStorePassword = password.toCharArray();
    	KeyStore.ProtectionParameter kspp = new KeyStore.PasswordProtection(keyStorePassword);
    	
    	// Must load even if it doesn't exist (pass null)
    	keyStore.load(null, keyStorePassword);
    	
    	// Create certificate with public key
    	// https://www.codota.com/code/java/classes/java.security.cert.Certificate
    	CertificateFactory certificateFactory = CertificateFactory.getInstance("X509");
    	Certificate certificate = certificateFactory.generateCertificate(
    			new ByteArrayInputStream(pair.getPublic().getEncoded()));

    	
    	KeyStore.PrivateKeyEntry privKeyEntry =
    			new KeyStore.PrivateKeyEntry(pair.getPrivate(), new Certificate[] {certificate});
    	
    	keyStore.setEntry("privKey", privKeyEntry, kspp);
    	keyStore.setCertificateEntry("certificate", certificate);
    	
    	return keyStore;
    }
    
    public static KeyPair loadKeyStore(String filename, String password)
    		    throws KeyStoreException, FileNotFoundException, IOException,
    		           NoSuchAlgorithmException, CertificateException, UnrecoverableEntryException {
    	KeyStore keyStore = KeyStore.getInstance(KeyStore.getDefaultType());
    	char[] keyStorePassword = password.toCharArray();
    	KeyStore.ProtectionParameter kspp = new KeyStore.PasswordProtection(keyStorePassword);
    	try(InputStream keyStoreData = new FileInputStream(filename)){
    	    keyStore.load(keyStoreData, keyStorePassword);
    	}
    	Certificate certificate = keyStore.getCertificate("certificate");
    	KeyStore.PrivateKeyEntry privateKeyEntry = 
    			(KeyStore.PrivateKeyEntry) keyStore.getEntry("privKey", kspp);
    	PublicKey pubKey = certificate.getPublicKey();
    	PrivateKey privKey = privateKeyEntry.getPrivateKey();
    	
    	return new KeyPair(pubKey, privKey);
    }
    
    public static KeyPair loadKeyStore(KeyStore keyStore, String password)
		    throws KeyStoreException, FileNotFoundException, IOException,
		           NoSuchAlgorithmException, CertificateException, UnrecoverableEntryException {
		char[] keyStorePassword = password.toCharArray();
		KeyStore.ProtectionParameter kspp = new KeyStore.PasswordProtection(keyStorePassword);
		Certificate certificate = keyStore.getCertificate("certificate");
		KeyStore.PrivateKeyEntry privateKeyEntry = 
				(KeyStore.PrivateKeyEntry) keyStore.getEntry("privKey", kspp);
		PublicKey pubKey = certificate.getPublicKey();
		PrivateKey privKey = privateKeyEntry.getPrivateKey();
		
		return new KeyPair(pubKey, privKey);
	}

    public static byte[] publicKeyToByteArray(PublicKey publicKey) {
        return publicKey.getEncoded();
    }

    public static PublicKey publicKeyFromByteArray(byte[] data)
            throws NoSuchAlgorithmException, InvalidKeySpecException {
        X509EncodedKeySpec keySpec = new X509EncodedKeySpec(data);
        KeyFactory kf = KeyFactory.getInstance("RSA");
        return kf.generatePublic(keySpec);
    }

    public static byte[] privateKeyToByteArray(PrivateKey privateKey) {
        return privateKey.getEncoded();
    }

    public static PrivateKey privateKeyFromByteArray(byte[] data)
            throws NoSuchAlgorithmException, InvalidKeySpecException {
        PKCS8EncodedKeySpec ks = new PKCS8EncodedKeySpec(data);
        KeyFactory kf = KeyFactory.getInstance("RSA");
        return kf.generatePrivate(ks);
    }

    /**
     * https://stackoverflow.com/questions/31915617/how-to-encrypt-string-with-public-key-and-decrypt-with-private-key
     */
    public byte[] encrypt(PublicKey publicKey, byte[] inputData)
            throws Exception {
        cipher.init(Cipher.ENCRYPT_MODE, publicKey);
        return cipher.doFinal(inputData);
    }
    
    public String encrypt(PublicKey publicKey, String plainText)
    		throws Exception {
        cipher.init(Cipher.ENCRYPT_MODE, publicKey);
        return Utility.bytesToBase64(cipher.doFinal(plainText.getBytes(UTF_8)));
    }

    public byte[] decrypt(PrivateKey privateKey, byte[] inputData)
            throws Exception {
        cipher.init(Cipher.DECRYPT_MODE, privateKey);
        return cipher.doFinal(inputData);
    }

    public String decrypt(PrivateKey privateKey, String cipherText)
    		throws Exception {
        cipher.init(Cipher.DECRYPT_MODE, privateKey);
        return new String(cipher.doFinal(Utility.base64ToBytes(cipherText)), UTF_8);
    }
    
    public byte[] sign(byte[] inputData, PrivateKey privateKey) throws Exception {
        signature.initSign(privateKey);
        signature.update(inputData);
        return signature.sign();
    }

    public String sign(String plainText, PrivateKey privateKey) throws Exception {
        signature.initSign(privateKey);
        signature.update(plainText.getBytes(UTF_8));
        return Utility.bytesToBase64(signature.sign());
    }
    
    public boolean verify(byte[] inputData, byte[] signedInputData, PublicKey publicKey) throws Exception {
    	signature.initVerify(publicKey);
    	signature.update(inputData);
    	return signature.verify(signedInputData);
    }

    public boolean verify(String plainText, String signedPlainText, PublicKey publicKey) throws Exception {
    	signature.initVerify(publicKey);
    	signature.update(plainText.getBytes(UTF_8));
    	return signature.verify(Utility.base64ToBytes(signedPlainText));
    }
    
    /**
     * Encrypt a private key using AES instead of KeyStore 
     */
    public static byte[] encryptPrivateKey(PrivateKey privateKey, String password)
            throws NoSuchAlgorithmException, NoSuchPaddingException, InvalidKeyException,
            BadPaddingException, IllegalBlockSizeException {

        // Generate key of 256 bits from key password entered by user
        MessageDigest digest = MessageDigest.getInstance("SHA-256");
        byte[] passwordHash = digest.digest(password.getBytes());

        Key aesKey = new SecretKeySpec(passwordHash, "AES");
        Cipher cipher = Cipher.getInstance("AES");
        cipher.init(Cipher.ENCRYPT_MODE, aesKey);
        return cipher.doFinal(privateKeyToByteArray(privateKey));

    }

    public static PrivateKey decryptPrivateKey(byte[] encryptedKey, String password)
            throws NoSuchAlgorithmException, NoSuchPaddingException, InvalidKeyException,
            BadPaddingException, IllegalBlockSizeException, InvalidKeySpecException {

        // Generate key of 256 bits from key password entered by user
        MessageDigest digest = MessageDigest.getInstance("SHA-256");
        byte[] passwordHash = digest.digest(password.getBytes());

        Key aesKey = new SecretKeySpec(passwordHash, "AES");
        Cipher cipher = Cipher.getInstance("AES");
        cipher.init(Cipher.DECRYPT_MODE, aesKey);
        return privateKeyFromByteArray(cipher.doFinal(encryptedKey));
    }

}
