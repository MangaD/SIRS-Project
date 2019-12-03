package smartphone.test;

import org.junit.jupiter.api.Test;

import smartphone.security.AsymmetricEncryptionRSA;

import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.DisplayName;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertTrue;

import java.security.KeyPair;
import java.security.PrivateKey;

// JUnit 5 Tutorial:
// https://www.petrikainulainen.net/programming/testing/junit-5-tutorial-writing-our-first-test-class/
// Assertions: https://www.petrikainulainen.net/programming/testing/junit-5-tutorial-writing-assertions-with-junit-5-api/
class AsymmetricEncryptionRSATest {
	
	private static KeyPair pair;
	private static AsymmetricEncryptionRSA aeRSA;

    @BeforeAll
    static void beforeAll() {
        //System.out.println("Before all test methods");
    	try {
			pair = AsymmetricEncryptionRSA.generateKeyPair();
			aeRSA = new AsymmetricEncryptionRSA();
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
    }
 
    @Test
    @DisplayName("Encrypt and decrypt byte array")
    void encryptDecryptBytes() throws Exception {
    	String s = "hello";
		byte[] encStrB = aeRSA.encrypt(pair.getPublic(), s.getBytes());
		byte[] decStrB = aeRSA.decrypt(pair.getPrivate(), encStrB);
		assertEquals(s, new String(decStrB, "UTF-8"));
    }
 
    @Test
    @DisplayName("Encrypt and decrypt string")
    void encryptDecryptString() throws Exception {
    	String s1 = "hello";
		String encStr = aeRSA.encrypt(pair.getPublic(), s1);
		String decStr = aeRSA.decrypt(pair.getPrivate(), encStr);
		assertEquals(s1, decStr);
    }
    
    @Test
    @DisplayName("Sign and verify")
    void signVerify() throws Exception {
    	String s = "hello";
		String signedTxt = aeRSA.sign(s, pair.getPrivate());
		boolean isCorrect = aeRSA.verify(s, signedTxt, pair.getPublic());
		assertTrue(isCorrect);
    }
    
    @Test
    @DisplayName("Key store set and get")
    void encryptDecryptPrivateKey() throws Exception {
    	
    	String pwd = "hello";
		byte[] encPriv = AsymmetricEncryptionRSA.encryptPrivateKey(pair.getPrivate(), pwd);
		PrivateKey privKey = AsymmetricEncryptionRSA.decryptPrivateKey(encPriv, pwd);
		assertEquals(pair.getPrivate(), privKey);
    }
}
