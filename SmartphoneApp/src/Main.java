import java.io.IOException;

public class Main {

    public static int port = 12345;

    public static void main(String[] args) {

        //ClientListener service = new ClientListener(port, "ServiceThread");
        //service.start();

        new WebsocketServer().start();
    }
}
