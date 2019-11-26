package smartphone;

import java.io.IOException;
import java.sql.SQLException;

public class Main {

    public static Database db = null;
    public static String db_name = "smartphone.db";
    
    public static int port = 4444;

    public static void main(String[] args) throws SQLException {

        try {
            db = new Database(db_name);
        } catch (IOException e) {
            e.printStackTrace();
            System.exit(0);
        }

        // Debug
        db.printFilesTable();

        String key = db.getEncKey("ola");
        System.out.println(key);

        new SmartphoneServer(port).start();
    }
}
