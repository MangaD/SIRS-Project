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
