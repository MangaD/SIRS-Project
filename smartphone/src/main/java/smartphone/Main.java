package smartphone;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.nio.file.Files;
import java.security.InvalidKeyException;
import java.security.KeyPair;
import java.security.NoSuchAlgorithmException;
import java.security.PublicKey;
import java.security.spec.InvalidKeySpecException;
import java.security.PrivateKey;
import java.sql.SQLException;

import javax.crypto.BadPaddingException;
import javax.crypto.IllegalBlockSizeException;
import javax.crypto.NoSuchPaddingException;

import static smartphone.security.AsymmetricEncryptionRSA.*;

public class Main {

    public static Database db = null;
    public static String db_name = "smartphone.db";
    
    public static int port = 4444;
    
    public static String pubKeyName = "publicKey.pub";
    public static String privKeyName = "privateKey";
    public static KeyPair keyPair;

    public static void main(String[] args) throws SQLException, IOException,
    		NoSuchAlgorithmException, InvalidKeySpecException, InvalidKeyException,
    		NoSuchPaddingException, BadPaddingException, IllegalBlockSizeException {
    	
        // Encryption stuff
    	loadKeys();
    	
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
    
    public static void createKeys() throws NoSuchAlgorithmException, InvalidKeyException,
    		NoSuchPaddingException, BadPaddingException, IllegalBlockSizeException {
    	String prompt = "Hello there!\n" + 
    			"It looks like this is your first time launching this app.\n" +
    			"Please enter a strong password for your authentication: ";
    	String password = Utils.readPassword(prompt);
    	if (password == null || password.isEmpty()) {
    		Utils.println("Maybe next time. Bye!");
    		System.exit(0);
    	}
    	keyPair = generateKeyPair();
    	byte[] pubKeyBytes = publicKeyToByteArray(keyPair.getPublic());
    	byte[] encPrivKeyBytes = encryptPrivateKey(keyPair.getPrivate(), password);
    	try (FileOutputStream pubFos = new FileOutputStream(pubKeyName);
    			FileOutputStream privFos = new FileOutputStream(privKeyName)) {
    		pubFos.write(pubKeyBytes);
    		privFos.write(encPrivKeyBytes);
	    } catch(IOException ex) {
	        Utils.println("Exception occurred while saving keys in files. " + ex.getMessage());
	        System.exit(0);
	    }
    }
    
    public static void loadKeys() throws IOException, NoSuchAlgorithmException,
    		InvalidKeySpecException, InvalidKeyException,
    		NoSuchPaddingException, BadPaddingException, IllegalBlockSizeException {
    	File pubKeyFile = new File(pubKeyName);
    	File privKeyFile = new File(privKeyName);
    	if (!pubKeyFile.exists() || !privKeyFile.exists()) {
    		createKeys();
    	} else {
    		String password = Utils.readPassword("Please enter password:");
    		if (password == null || password.isEmpty()) {
        		Utils.println("Maybe next time. Bye!");
        		System.exit(0);
        	}
    		byte[] pubKeyBytes = Files.readAllBytes(pubKeyFile.toPath());
    		byte[] privKeyBytes = Files.readAllBytes(privKeyFile.toPath());
    		PublicKey pubKey = publicKeyFromByteArray(pubKeyBytes);
    		PrivateKey privKey = decryptPrivateKey(privKeyBytes, password);
    		keyPair = new KeyPair(pubKey, privKey);
    	}
    }

}
