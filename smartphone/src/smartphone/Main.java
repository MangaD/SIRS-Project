package smartphone;

import java.io.IOException;
import java.sql.SQLException;

public class Main {

    public static Database db = null;
    public static String db_name = "smartphone.db";

    public static void main(String[] args) throws SQLException {


        try {
            db = new Database(db_name);
        } catch (IOException e) {
            e.printStackTrace();
            System.exit(0);
        }

        db.selectAllHashKeyTable();

        String key = db.getKey("ola");
        System.out.println(key);



        new WebsocketServer().start();
    }
}
