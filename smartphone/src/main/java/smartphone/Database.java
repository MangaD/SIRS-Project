package smartphone;

import java.io.File;
import java.io.IOException;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.SQLException;
import java.sql.ResultSet;
import java.sql.Statement;

public class Database {

    private Connection conn;
    private String url;

    Database(String url) throws IOException {
        this.url = url;
        this.conn = null;

        /**
         * Check if database file exists because the connect method creates it if it doesn't
         */
        File f = new File(this.url);
        if (!(f.exists() && !f.isDirectory())) {
            throw new RuntimeException("Database file not found.");
        }

        connect();
    }


    /**
     * Connect to a database
     * http://www.sqlitetutorial.net/sqlite-java/sqlite-jdbc-driver/
     */
    private void connect() {
        String url = "jdbc:sqlite:" + this.url;
        try {
            this.conn = DriverManager.getConnection(url);
            System.out.println("Connection to SQLite has been established.");

        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }
    }

    /**
     * Select all Hash/Key Table
     * http://www.sqlitetutorial.net/sqlite-java/select/
     * 
     * Select with parameters: http://www.sqlitetutorial.net/sqlite-java/select/
     */
    public void printFilesTable() {
        String sql = "SELECT hash, enc_key FROM files";

        // try-with-resources
        // https://docs.oracle.com/javase/tutorial/essential/exceptions/tryResourceClose.html
        try (Statement stmt = this.conn.createStatement();
             ResultSet rs = stmt.executeQuery(sql)) {

            // loop through the result set
            while (rs.next()) {
                System.out.println(rs.getString("hash") + "\t" +
                        rs.getString("enc_key"));
            }
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }
    }

    public String getEncKey(String hash) {
        String sql = "SELECT enc_key FROM files WHERE hash = ?";

        try (PreparedStatement pstmt  = conn.prepareStatement(sql)) {
            pstmt.setString(1, hash);

            ResultSet rs  = pstmt.executeQuery();

            while (rs.next()) {
                return rs.getString("enc_key");
            }

        } catch (SQLException e) {
            System.out.println(e.getMessage());
            return null;
        }

        return null;
    }

    public void addFile(String hash, String encKey) throws SQLException {

        String sql = "INSERT INTO files (hash, enc_key) VALUES(?, ?)";

        PreparedStatement pstmt  = conn.prepareStatement(sql);
        pstmt.setString(1, hash);
        pstmt.setString(2, encKey);

        pstmt.executeUpdate();
    }


}