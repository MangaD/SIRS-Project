package smartphone.security;

import java.util.Base64;
// Android
//import android.util.Base64;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;

public class Utility {
	/**
	 * For sending the keys as a string
	 *
	 * https://stackoverflow.com/questions/2418485/how-do-i-convert-a-byte-array-to-base64-in-java
	 */
	public static String bytesToBase64(byte[] data) {
		return Base64.getEncoder().encodeToString(data);
		
		// Android
		//return Base64.encodeToString(data, Base64.NO_WRAP);
	}

	public static byte[] base64ToBytes(String base64) {
		return Base64.getDecoder().decode(base64);
		
		// Android
		//return Base64.decode(base64, Base64.NO_WRAP);
	}

	public static String toSHA512Base64(String s) throws NoSuchAlgorithmException {
		MessageDigest digest = MessageDigest.getInstance("SHA-512");
		byte[] sHash = digest.digest(s.getBytes());
		return Utility.bytesToBase64(sHash);
	}

	public static byte[] PEMtoPublicKeyBytes(String pubKeyPEM) {
		// PEM to PublicKey
		// https://www.xinotes.net/notes/note/1898/
		pubKeyPEM = pubKeyPEM.replaceAll("(-+BEGIN PUBLIC KEY-+\\r?\\n|-+END PUBLIC KEY-+\\r?\\n?)", "");
		pubKeyPEM = pubKeyPEM.replace("\n", "").replace("\r", "");
		return Utility.base64ToBytes(pubKeyPEM);
	}
}
