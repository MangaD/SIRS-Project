import java.io.IOException;
import java.net.ServerSocket;
import java.net.Socket;
import java.util.Vector;

import org.java_websocket.WebSocket;
import org.java_websocket.handshake.ClientHandshake;
import org.java_websocket.server.WebSocketServer;


public class ClientListener implements Runnable {

    private int portNumber;
    private Thread t;
    private String threadName;
    protected Vector<ClientConnection> clientConnections;

    ClientListener(int port, String name) {
        this.portNumber = port;
        this.threadName = name;
        this.clientConnections = new Vector<ClientConnection>();
    }

    @Override
    public void run() {

        try (
                ServerSocket serverSocket = new ServerSocket(portNumber);
        ) {
            while (true) {
                Socket clientSocket = serverSocket.accept();
                try {
                    ClientConnection cn = new ClientConnection(clientSocket,
                            "Client: " + clientSocket.getInetAddress());
                    cn.start();
                    this.clientConnections.add(cn);
                } catch (IOException e) {
                    System.out.println("Failed to create client thread.");
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
            System.exit(0);
        }

        System.out.println("Thread " + threadName + " exiting.");
    }

    public void start() {
        System.out.println("Starting " + threadName + " on port " + portNumber + ".");
        if (t == null) {
            t = new Thread(this, threadName);
            t.start();
        }
    }
}