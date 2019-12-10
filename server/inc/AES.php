<?php

// GCM info
// https://crypto.stackexchange.com/questions/26783/ciphertext-and-tag-size-and-iv-transmission-with-aes-in-gcm-mode

class AES {

	private $cipher;
	private $taglength;

	function __construct() {
		// GCM mode is available only in PHP 7.1+
		$this->cipher = "aes-256-gcm";
		$this->taglength = 16;
		if (!in_array($this->cipher, openssl_get_cipher_methods())) {
			throw new Exception("Cipher '" . $cipher . "' not found.");
		}
	}

	function encrypt($plainText, $key) {
		$ivlen = openssl_cipher_iv_length($this->cipher); // returns 12 for GCM
		if ($ivlen !== 12) {
			throw new Exception("'openssl_cipher_iv_length' didn't return 12.");
		}
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext = openssl_encrypt($plainText, $this->cipher, $key,
			$options=(OPENSSL_RAW_DATA | OPENSSL_NO_PADDING), $iv, $tag, "", $this->taglength);
		// In Java the tag is unfortunately added at the end of the ciphertext. 
		// https://stackoverflow.com/questions/23864440/aes-gcm-implementation-with-authentication-tag-in-java
		return $iv . $ciphertext . $tag;
	}

	function decrypt($ciphertext, $key) {
		$ivlen = openssl_cipher_iv_length($this->cipher); // returns 12 for GCM
		if ($ivlen !== 12) {
			throw new Exception("'openssl_cipher_iv_length' didn't return 12.");
		}
		$iv = substr($ciphertext, 0, $ivlen);
		$tag = substr($ciphertext, -$this->taglength);
		$ciphertext = substr($ciphertext, $ivlen, -$this->taglength);
		return openssl_decrypt($ciphertext, $this->cipher, $key,
			$options=(OPENSSL_RAW_DATA | OPENSSL_NO_PADDING), $iv, $tag);
	}

}

/*
$aes = new AES();
$ciphertext = $aes->encrypt("hola", "70743af02bc7331a499cb2a240278dbcc17ab505e5010613f4755ce81d4e5caf");
$originaltext = $aes->decrypt($ciphertext , "70743af02bc7331a499cb2a240278dbcc17ab505e5010613f4755ce81d4e5caf");
echo ($originaltext);
*/

?>