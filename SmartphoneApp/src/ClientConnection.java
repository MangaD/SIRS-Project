import java.io.DataOutputStream;
import java.io.DataInputStream;
import java.io.IOException;
import java.net.Socket;

public class ClientConnection implements Runnable {

    private Thread t;
    private String threadName;
    private Socket clientSocket;
    private DataOutputStream out;
    private DataInputStream in;

    ClientConnection(Socket s, String name) throws IOException {
        threadName = name;
        clientSocket = s;
        out = new DataOutputStream(clientSocket.getOutputStream());
        in = new DataInputStream(clientSocket.getInputStream());
    }

    @Override
    public void run() {

        try {

            String inputLine;

            inputLine = read();

            System.out.println("Received: " + inputLine);

            if (inputLine.equals("login")) {

                   /*String user = read();
                    while (user.isEmpty()) {
                        user = read();
                    }
                    String password = read();
                    while (password.isEmpty()) {
                        password = read();
                    }

                    System.out.println("Received login from '" + user + "' with password '" + password + "'.");
                    */
                System.out.println("Recebi login info");
            } else if (inputLine.equals("signup")) {

                String user = read();
                while (user.isEmpty()) {
                    user = read();
                }
                String password = read();
                while (password.isEmpty()) {
                    password = read();
                }
                String pubKey = read();
                while (pubKey.isEmpty()) {
                    pubKey = read();
                }
                String privKey = read();
                while (privKey.isEmpty()) {
                    privKey = read();
                }

                System.out.println("Received sign up from '" + user + "' with password '" + password + "'.");
            }
        } catch (IOException e) {
        }

        System.out.println("Thread " + threadName + " exiting.");
    }

    private String read() throws IOException {
        try {
            return in.readUTF().trim();
        } catch (NullPointerException e) {
            return null;
        }
    }

    private void write(String message) throws IOException {
        out.writeUTF(message + "\n");
    }

    public void start() {
        System.out.println("Starting " + threadName);
        if (t == null) {
            t = new Thread(this, threadName);
            t.start();
        }
    }
}
