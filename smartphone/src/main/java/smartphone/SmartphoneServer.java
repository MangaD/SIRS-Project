package smartphone;

import org.java_websocket.WebSocket;
import org.java_websocket.handshake.ClientHandshake;
import org.java_websocket.server.WebSocketServer;
import org.json.JSONObject;

import smartphone.security.AsymmetricEncryptionRSA;
import smartphone.security.DiffieHellman;
import smartphone.security.SymmetricEncryption_AES_GCM;
import smartphone.security.Utility;

import java.net.InetSocketAddress;
import java.security.NoSuchAlgorithmException;
import java.util.Vector;

import javax.crypto.NoSuchPaddingException;
import javax.crypto.SecretKey;
import java.security.PublicKey;

import static smartphone.Main.keyPair;

public class SmartphoneServer extends WebSocketServer {

	private int TCP_PORT;

	class Client {
		public WebSocket ws;
		public boolean isAuthenticated;
		public DiffieHellman dh;
		public SecretKey key;
		public SymmetricEncryption_AES_GCM aes_gcm;
		public AsymmetricEncryptionRSA aeRSA;
		public Client(WebSocket ws) throws NoSuchAlgorithmException, NoSuchPaddingException {
			this.ws = ws;
			this.dh = new DiffieHellman();
			this.key = null;
			this.aes_gcm = new SymmetricEncryption_AES_GCM();
			isAuthenticated = false;
			aeRSA = new AsymmetricEncryptionRSA();
		}
	}

	// Vector is thread safe
	private Vector<Client> conns;

	public SmartphoneServer(int port) {
		super(new InetSocketAddress(port));
		TCP_PORT = port;
		conns = new Vector<>();
	}

	@Override
	public void onOpen(WebSocket conn, ClientHandshake handshake) {
		try {
			conns.add(new Client(conn));
			System.out.println("New connection from " + getAddress(conn));
		} catch (Exception e) {
			System.out.println("Failed to create client from " + getAddress(conn) +
				" with: " + e.getMessage());
			removeClient(conn);
		}
	}

	@Override
	public void onClose(WebSocket conn, int code, String reason, boolean remote) {
		removeClient(conn);
		System.out.println("Closed connection to " + getAddress(conn));
	}

	@Override
	public void onMessage(WebSocket conn, String message) {
		
		//System.out.println("Message from client: " + message);
		
		JSONObject jObj = new JSONObject(message);

		// Check if client is authenticated
		Client c = getClient(conn);
		if (!c.isAuthenticated) {
			if (!jObj.has("action") || !jObj.getString("action").equals("login")) {
				removeClient(conn);
				return;
			}
		}

		if (jObj.has("action")) {
			
			String action = jObj.getString("action");
			
			JSONObject response = new JSONObject();
			response.put("action", action);
			
			// do: What is the action (eg. login, DH) for? login, register...
			String doAction = "";
			if (jObj.has("do")) {
				doAction = jObj.getString("do");
				response.put("do", doAction);
			}
			
			if (action.equals("login")) {

				System.out.println("Received login request " +
					(doAction.isEmpty() ? "" : "with do='" + doAction + "' ") +
					"from: " + getAddress(conn));

				if (c.isAuthenticated) {
					response.put("success", true);
					response.put("message", "Authentication succeeeded.");
					conn.send(response.toString());
					return;
				}
				
				if (!jObj.has("password")) {
					response.put("success", false);
					response.put("message", "Please input a password.");
					conn.send(response.toString());
					removeClient(conn);
					return;
				}
				
				String password = jObj.getString("password");
				
				try {
					Main.loadKeys(password);
					c.isAuthenticated = true;
					response.put("success", true);
					response.put("message", "Authentication succeeeded.");
					conn.send(response.toString());
				} catch(Exception e) {
					response.put("success", false);
					response.put("message", "Authentication failed.");
					conn.send(response.toString());
					removeClient(conn);
				}
			} else if (action.equals("dh")) {
				
				System.out.println("Received DH exchange request " +
					(doAction.isEmpty() ? "" : "with do='" + doAction + "' ") +
					"from: " + getAddress(conn));
				
				if (!jObj.has("signedKey") || !jObj.has("p") ||
						!jObj.has("key") || !jObj.has("g") || !jObj.has("l") ||
						!jObj.has("pubKeyRSA")) {
					response.put("success", false);
					response.put("message", "DH requires a signed DH public key, p, g and l values. " +
						"It also requires the server's RSA public key.");
					conn.send(response.toString());
					return;
				}
				
				String pBase64 = jObj.getString("p");
				String gBase64 = jObj.getString("g");
				String key = jObj.getString("key");
				String signedKeyBase64 = jObj.getString("signedKey");
				int l = jObj.getInt("l");
				// TODO - Should verify if certificate is valid
				String pubKeyRSA_PEM = jObj.getString("pubKeyRSA");
				
				try {
					// Server PEM key to PublicKey
					byte[] pubKeyBytes = Utility.PEMtoPublicKeyBytes(pubKeyRSA_PEM);
					PublicKey serverPubKey = AsymmetricEncryptionRSA.publicKeyFromByteArray(pubKeyBytes);

					// Verify DH public value's signature
					boolean isCorrect = c.aeRSA.verify(key, signedKeyBase64, serverPubKey);
					if (!isCorrect) {
						throw new Exception("DH public value's signature verify failed.");
					}

					// Generate our DH key pair
					String responsePubKeyBase64 = c.dh.generateKeyPair(pBase64, gBase64, l);
					response.put("pubKeyPEM", responsePubKeyBase64);
					// Sign our DH public value
					String signedPubKeyBase64 = c.aeRSA.sign(responsePubKeyBase64, keyPair.getPrivate());
					response.put("signedPubKeyPEM", signedPubKeyBase64);

					// Generate shared secret
					c.dh.generateSharedSecret(key);
					c.key = c.dh.generateAESFromSharedSecret();
					response.put("success", true);

					//response.put("pubKeyPEM", responsePubKeyBase64);
				} catch (Exception e) {
					response.put("success", false);
					response.put("message", e.getMessage());
				}
				conn.send(response.toString());
				
			} else if (action.equals("encrypt")) {

				System.out.println("Received encrypt request " +
					(doAction.isEmpty() ? "" : "with do='" + doAction + "' ") +
					"from: " + getAddress(conn));
				
				if (!jObj.has("message")) {
					response.put("success", false);
					response.put("message", "You did not provide a message to encrypt.");
					conn.send(response.toString());
					return;
				} else if (c.key == null) {
					response.put("success", false);
					response.put("message", "You did not make a DH exchange.");
					conn.send(response.toString());
					return;
				}

				String plaintext = jObj.getString("message");

				try {
					byte[] ciphertext = c.aes_gcm.encrypt(plaintext, c.key);
					response.put("success", true);
					response.put("ciphertext", Utility.bytesToBase64(ciphertext));
				} catch (Exception e) {
					response.put("success", false);
					response.put("message", e.getMessage());
				}
				conn.send(response.toString());
			} else if (action.equals("decrypt")) {

				System.out.println("Received decrypt request " +
					(doAction.isEmpty() ? "" : "with do='" + doAction + "' ") +
					"from: " + getAddress(conn));

				if (!jObj.has("message")) {
					response.put("success", false);
					response.put("message", "You did not provide a message to decrypt.");
					conn.send(response.toString());
					return;
				} else if (c.key == null) {
					response.put("success", false);
					response.put("message", "You did not make a DH exchange.");
					conn.send(response.toString());
					return;
				}

				String ciphertext = jObj.getString("message");

				try {
					String plaintext = c.aes_gcm.decryptBase64(ciphertext, c.key);
					response.put("success", true);
					response.put("plaintext", plaintext);
				} catch (Exception e) {
					response.put("success", false);
					response.put("message", e.getMessage());
				}
				conn.send(response.toString());
			}

		}

	}

	@Override
	public void onError(WebSocket conn, Exception ex) {
		removeClient(conn);
		System.out.println("ERROR from " + getAddress(conn));
	}

	@Override
	public void onStart() {
		System.out.println("Listening on port: " + TCP_PORT);
	}

	private Client getClient(WebSocket conn) {
		Client c = null;
		if (conn == null) return null;
		for (Client c1 : conns) {
			if (c1.ws.equals(conn)) {
				c = c1;
				break;
			}
		}
		return c;
	}

	private void removeClient(WebSocket conn) {
		if (conn == null) return;
		conn.close();
		for (Client c : conns) {
			if (c.ws.equals(conn)) {
				conns.remove(c);
				break;
			}
		}
	}

	private String getAddress(WebSocket conn) {
		return conn.getRemoteSocketAddress().getAddress().getHostAddress();
	}
}