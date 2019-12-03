<?php

/**
 * Tutorials:
 * https://www.php.net/manual/en/function.openssl-dh-compute-key.php
 * https://8gwifi.org/docs/php-asym.jsp
 * https://hotexamples.com/examples/-/-/openssl_dh_compute_key/php-openssl_dh_compute_key-function-examples.html
 */

// TODO Sign pubKey and pubValue
class DH {

	private $pubKey;
	private $privKey;

	private static $config = array(
		"digest_alg" => "sha512",
		"private_key_bits" => 2048,
		"private_key_type" => OPENSSL_KEYTYPE_DH,
	);

	function __construct() {
		$this->name = $name;

		// Create the key pair
		$res = openssl_pkey_new(DH::$config);

		// Extract the private key from $res to $privKey
		openssl_pkey_export($res, $this->privKey);

		// Extract the public key from $res to $pubKey
		$this->pubKey = openssl_pkey_get_details($res);
		$this->pubKey = $pubKey["key"];

		/*
		$data = 'plaintext data goes here';
		// Encrypt the data to $encrypted using the public key
		openssl_public_encrypt($data, $encrypted, $pubKey);
		// Decrypt the data using the private key and store the results in $decrypted
		openssl_private_decrypt($encrypted, $decrypted, $privKey);
		echo $decrypted;
		*/
	}

}

?>
