package smartphone;

import java.io.IOException;
import java.security.KeyPair;
import java.sql.SQLException;

import smartphone.security.*;

public class Main {

    public static Database db = null;
    public static String db_name = "smartphone.db";
    
    public static int port = 4444;

    public static void main(String[] args) throws SQLException {
    	
        // Encryption stuff
        try {
        	// Generate key pair
        	KeyPair pair = AsymmetricEncryptionRSA.generateKeyPair();
        	
			AsymmetricEncryptionRSA a = new AsymmetricEncryptionRSA();
			
			// Encrypt and decrypt bytes
			String s = "hello";
			byte[] encStrB = a.encrypt(pair.getPublic(), s.getBytes());
			byte[] decStrB = a.decrypt(pair.getPrivate(), encStrB);
			System.out.println(new String(decStrB, "UTF-8"));
			
			// Encrypt and decrypt string
			String s1 = "hello2";
			String encStr = a.encrypt(pair.getPublic(), s1);
			String decStr = a.decrypt(pair.getPrivate(), encStr);
			System.out.println(decStr);
			
			// Sign and verify
	        String signedTxt = a.sign("foobar", pair.getPrivate());
	        boolean isCorrect = a.verify("foobar", signedTxt, pair.getPublic());
	        System.out.println("Signature correct: " + isCorrect);
		} catch (Exception e) {
			e.printStackTrace();
			System.exit(0);
		}
    	
    	// Connect to database
        try {
            db = new Database(db_name);
        } catch (IOException e) {
            e.printStackTrace();
            System.exit(0);
        }

        // Listen to incoming connections
        // TODO Client should authenticate somehow
        new SmartphoneServer(port).start();
    }

}
