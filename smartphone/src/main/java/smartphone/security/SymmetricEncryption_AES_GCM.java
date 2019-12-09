package smartphone.security;

import java.security.InvalidAlgorithmParameterException;
import java.security.InvalidKeyException;
import java.security.NoSuchAlgorithmException;
import java.security.SecureRandom;

import javax.crypto.BadPaddingException;
import javax.crypto.Cipher;
import javax.crypto.IllegalBlockSizeException;
import javax.crypto.KeyGenerator;
import javax.crypto.NoSuchPaddingException;
import javax.crypto.SecretKey;
import javax.crypto.spec.GCMParameterSpec;
import javax.crypto.spec.SecretKeySpec;

/**
 * Symmetric encryption tutorial: https://www.baeldung.com/java-cipher-input-output-stream
 * https://gist.github.com/itarato/abef95871756970a9dad
 * Assymetric encryption tutorial: https://www.novixys.com/blog/how-to-generate-rsa-keys-java/
 * Why use EC: https://blog.cloudflare.com/ecdsa-the-digital-signature-algorithm-of-a-better-internet/
 * AES-GCM Tutorial: https://javainterviewpoint.com/java-aes-256-gcm-encryption-and-decryption/
 */
public class SymmetricEncryption_AES_GCM {
	
    public static final int AES_KEY_SIZE = 256;
    public static final int GCM_IV_LENGTH = 12;
    public static final int GCM_TAG_LENGTH = 16;

    private Cipher cipher;

    public SymmetricEncryption_AES_GCM() throws NoSuchAlgorithmException, NoSuchPaddingException {
        this.cipher = Cipher.getInstance("AES/GCM/NoPadding");
    }

    /**
     * Symmetric encryption
     */
    public static SecretKey generateAESKey() throws NoSuchAlgorithmException {
        return KeyGenerator.getInstance("AES").generateKey();
    }

    public static byte[] secretKeyToByteArray(SecretKey secretKey) {
        return secretKey.getEncoded();
    }

    public static SecretKey secretKeyFromByteArray(byte[] data) {
        return new SecretKeySpec(data, 0, data.length, "AES");
    }

    public byte[] encrypt(String content, SecretKey secretKey)
            throws InvalidKeyException, BadPaddingException, IllegalBlockSizeException,
            InvalidAlgorithmParameterException {

        // Generating IV.
        byte[] iv = new byte[GCM_IV_LENGTH];
        SecureRandom random = new SecureRandom();
        random.nextBytes(iv);
        
        // Create GCMParameterSpec
        GCMParameterSpec gcmParameterSpec = new GCMParameterSpec(GCM_TAG_LENGTH * 8, iv);
        
        // Initialize Cipher for ENCRYPT_MODE
        cipher.init(Cipher.ENCRYPT_MODE, secretKey, gcmParameterSpec);
        
        byte[] encrypted = cipher.doFinal(content.getBytes());

        // In Java the tag is unfortunately added at the end of the ciphertext. 
        // https://stackoverflow.com/questions/23864440/aes-gcm-implementation-with-authentication-tag-in-java
        byte[] encryptedIVAndText = new byte[GCM_IV_LENGTH + encrypted.length];
        System.arraycopy(iv, 0, encryptedIVAndText, 0, GCM_IV_LENGTH);
        System.arraycopy(encrypted, 0, encryptedIVAndText, GCM_IV_LENGTH, encrypted.length);

        return encryptedIVAndText;
    }

    public String decrypt(byte[] encryptedIvTextBytes, SecretKey secretKey)
            throws InvalidKeyException, InvalidAlgorithmParameterException, BadPaddingException,
            IllegalBlockSizeException, NoSuchAlgorithmException, NoSuchPaddingException {

        // Extract IV.
        byte[] iv = new byte[GCM_IV_LENGTH];
        System.arraycopy(encryptedIvTextBytes, 0, iv, 0, iv.length);
        GCMParameterSpec gcmParameterSpec = new GCMParameterSpec(GCM_TAG_LENGTH * 8, iv);

        // Extract encrypted part.
        int encryptedSize = encryptedIvTextBytes.length - GCM_IV_LENGTH;
        byte[] encryptedBytes = new byte[encryptedSize];
        System.arraycopy(encryptedIvTextBytes, GCM_IV_LENGTH, encryptedBytes, 0, encryptedSize);

        // Decrypt.
        this.cipher.init(Cipher.DECRYPT_MODE, secretKey, gcmParameterSpec);
        byte[] decrypted = this.cipher.doFinal(encryptedBytes);

        return new String(decrypted);
    }
    
    public String decryptAES(String encryptedIvTextBytesBase64, SecretKey secretKey)
            throws InvalidKeyException, InvalidAlgorithmParameterException, BadPaddingException,
            IllegalBlockSizeException, NoSuchAlgorithmException, NoSuchPaddingException {
    	byte[] encryptedIvTextBytes = Utility.base64ToBytes(encryptedIvTextBytesBase64);
    	return this.decrypt(encryptedIvTextBytes, secretKey);
    }
}