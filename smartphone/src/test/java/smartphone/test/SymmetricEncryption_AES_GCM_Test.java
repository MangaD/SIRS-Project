package smartphone.test;

import org.junit.jupiter.api.Test;

import smartphone.security.SymmetricEncryption_AES_GCM;

import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.DisplayName;

import static org.junit.jupiter.api.Assertions.assertEquals;

import javax.crypto.KeyGenerator;
import javax.crypto.SecretKey;

public class SymmetricEncryption_AES_GCM_Test {

	public static final int AES_KEY_SIZE = 256;
	
	private static SymmetricEncryption_AES_GCM aes_gcm;
	private static SecretKey key;

    @BeforeAll
    static void beforeAll() {
        //System.out.println("Before all test methods");
    	try {
    		KeyGenerator keyGenerator = KeyGenerator.getInstance("AES");
            keyGenerator.init(AES_KEY_SIZE);
           
            // Generate Key
            key = keyGenerator.generateKey();
			aes_gcm = new SymmetricEncryption_AES_GCM();
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
    }
    
    @Test
    @DisplayName("Encrypt and decrypt string")
    void encryptDecryptString() throws Exception {
    	String s1 = "hello";
		byte[] encBytes = aes_gcm.encrypt(s1, key);
		String decStr = aes_gcm.decrypt(encBytes, key);
		assertEquals(s1, decStr);
    }
}
