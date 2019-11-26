package smartphone;

import org.java_websocket.WebSocket;
import org.java_websocket.handshake.ClientHandshake;
import org.java_websocket.server.WebSocketServer;
import org.json.JSONObject;

import java.net.InetSocketAddress;
import java.util.HashSet;
import java.util.Iterator;
import java.util.Set;

public class SmartphoneServer extends WebSocketServer {

    private static int TCP_PORT = 4444;
    private Set<WebSocket> conns;

    public SmartphoneServer() {
        super(new InetSocketAddress(TCP_PORT));
        conns = new HashSet<>();
    }

    @Override
    public void onOpen(WebSocket conn, ClientHandshake handshake) {
        conns.add(conn);
        System.out.println("New connection from " + conn.getRemoteSocketAddress().getAddress().getHostAddress());
    }

    @Override
    public void onClose(WebSocket conn, int code, String reason, boolean remote) {
        conns.remove(conn);
        System.out.println("Closed connection to " + conn.getRemoteSocketAddress().getAddress().getHostAddress());
    }

    @Override
    public void onMessage(WebSocket conn, String message) {
        JSONObject jObj = new JSONObject(message);

        //System.out.println(jObj.toString());

        for (Iterator<String> key = jObj.keys(); key.hasNext();) {
            String name = (String) jObj.get((String)key.next());
            System.out.println(name);
        }

        System.out.println("Message from client: " + message);
        for (WebSocket sock : conns) {
            //respond only to correct client
            if (sock == conn) {
                sock.send(message);
            }
        }
    }

    @Override
    public void onError(WebSocket conn, Exception ex) {
        //ex.printStackTrace();
        if (conn != null) {
            conns.remove(conn);
            // do some thing if required
        }
        System.out.println("ERROR from " + conn.getRemoteSocketAddress().getAddress().getHostAddress());
    }

    @Override
    public void onStart() {
        System.out.println("Server Started on Port: " + TCP_PORT);
    }
}