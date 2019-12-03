<?php

require_once 'inc/utilities.php';

if (!isInstalled()) {
	$errors['not_installed'] = $app_title . ' server is not installed.';
} elseif ($_SERVER['REQUEST_METHOD'] != 'POST') {
	$errors['post'] = 'Must send data over POST request method.';
}

/**
 * Tutorials:
 * https://www.php.net/manual/en/function.openssl-dh-compute-key.php
 * https://8gwifi.org/docs/php-asym.jsp
 * https://hotexamples.com/examples/-/-/openssl_dh_compute_key/php-openssl_dh_compute_key-function-examples.html
 */

 /**
  * Finite-field DH vs ECC DH
  * https://crypto.stackexchange.com/questions/67797/in-diffie-hellman-are-g-and-p-universal-constants-or-are-they-chosen-by-one
  */

// TODO Sign pubKey and pubValue
class DH {

	private $pubKey;
	private $privKey;
	private $pubKeyBytes;

	function __construct() {

		/**
		 * From example:
		 * First, generate the DH prime number
		 * openssl dhparam -out dhparam.pem 2048
		 * openssl dh -in dhparam.pem -noout  -text
		 */
		$configargs = array();
		$configargs['p'] = hex2bin('00a3251e733f44b92beef49d9f376a4bfd1dbdf4afdac810775941c65f73d2882939cd1c5fc39f0f22d29c20c1e4c01803b8b6d8daad3b39a6da8efe1230e9035d22baef18d27b69f95bcb78c60c8c6bf24992c249e0457772b3553630f2401789185003fa2d547a7f344c7332b688145114be805795e6a3f651ff17474f15d60e6c4753722c2a4c21cb7df34997c9475e40337b99527e7af3522780de1b266b40bb14110bfbe6d82fcfa0062f96b91c0bb4cbd3a6629c4867f681f2c6ff45030a9d679dce27d96b485dcafbc25d849b8bcb40c7a40c8a6ef4abbab610c3b8254dcf6096f4dbe8001c58477afb5186d122d74e94317ad5da3d53dedabb648d626b');
		$configargs['g'] = hex2bin('02');

		// Create the key pair
		$keypair = openssl_pkey_new(array('dh' => $configargs));
		$details = openssl_pkey_get_details($keypair);

		$this->pubKey = $details['dh']['pub_key'];
		$this->priv_key = $details['dh']['priv_key'];

		$this->pubKeyBytes = bin2hex($this->pubKey);

		//print_r( $details );
	}

	function getPubKeyBytes() {
		return $this->pubKeyBytes;
	}

}

?>
