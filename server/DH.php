<?php

require_once 'inc/utilities.php';
require_once 'AES.php';

cors();

$errors = array();
$data = array();

if (!isInstalled()) {
	$errors['not_installed'] = $app_title . ' server is not installed.';
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

	private $p;
	private $g;

	private $bits;

	private $pubKeyPEM;
	private $privKeyPEM;
	private $sharedSecret;
	private $sharedKey;
	private $hasShared;

	function __construct() {
		$this->hasShared = false;

		/**
		 * From example:
		 * First, generate the DH prime number
		 * openssl dhparam -out dhparam.pem 2048
		 * openssl dh -in dhparam.pem -noout  -text
		 */

		$this->bits = 2048;
		
		$configargs = array();
		$configargs['p'] = hex2bin('00a3251e733f44b92beef49d9f376a4bfd1dbdf4afdac810775941c65f73d2882939cd1c5fc39f0f22d29c20c1e4c01803b8b6d8daad3b39a6da8efe1230e9035d22baef18d27b69f95bcb78c60c8c6bf24992c249e0457772b3553630f2401789185003fa2d547a7f344c7332b688145114be805795e6a3f651ff17474f15d60e6c4753722c2a4c21cb7df34997c9475e40337b99527e7af3522780de1b266b40bb14110bfbe6d82fcfa0062f96b91c0bb4cbd3a6629c4867f681f2c6ff45030a9d679dce27d96b485dcafbc25d849b8bcb40c7a40c8a6ef4abbab610c3b8254dcf6096f4dbe8001c58477afb5186d122d74e94317ad5da3d53dedabb648d626b');
		$configargs['g'] = hex2bin('02');

		$this->p = $configargs['p'];
		$this->g = $configargs['g'];

		// Create the key pair
		$keypair = openssl_pkey_new(array('dh' => $configargs));

		// "p" prime number (shared)
		// "g" generator of Z_p (shared)
		// "priv_key" private DH value x
		// "pub_key" public DH value g^x
		$details = openssl_pkey_get_details($keypair);

		$this->pubKeyPEM = $details['key'];
		openssl_pkey_export($keypair, $this->privKeyPEM);

		// Tried using this byte representation of public values
		// and didn't work...
		//$this->pubKey = $details['dh']['pub_key'];
		//$this->privKey = $details['dh']['priv_key'];
	}

	function computeKey($remotePubKeyPEM) {
		// Get our private key
		$privRes = openssl_pkey_get_private($this->privKeyPEM);
		// Get remote public key
		$details = openssl_pkey_get_details(openssl_pkey_get_public($remotePubKeyPEM));
		$remote_public_key = $details['dh']['pub_key'];
		$this->sharedSecret = openssl_dh_compute_key($remote_public_key, $privRes);
		if ($this->sharedSecret) {
			$this->sharedKey = hash('sha256', $this->sharedSecret);
			$this->hasShared = true;
		} else {
			throw new Exception("'openssl_dh_compute_key' failed.");
		}
	}

	function getPubKeyPEM() {
		return $this->pubKeyPEM;
	}

	function getP() {
		return $this->p;
	}

	function getG() {
		return $this->g;
	}

	function getBits() {
		return $this->bits;
	}

	function getSharedKey() {
		return $this->sharedKey;
	}

	function hasSharedSecret() {
		return $this->hasShared;
	}
}

if (!function_exists('openssl_pkey_new') || 
		!function_exists('openssl_pkey_get_details') ||
		!function_exists('openssl_dh_compute_key')) {
	$errors['missing_library'] = "DH exchange: It appears that OpenSSL functions are missing.";
}

$json = "";
$request = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$json = json_decode(file_get_contents('php://input'), true);

	if (!array_key_exists("request", $json)) {
		$errors['arguments'] = "DH exchange: Malformed request.";
	} else {
		$request = trim($json['request']);
	}

} else {
	$errors['post'] = 'Must send data over POST request method.';
}

if (empty($errors)) {
	if (empty($request)) {
		SessionManager::sessionStart();
		$dh = new DH();
		$_SESSION['dh'] = $dh;
		$data['pBase64'] = base64_encode($dh->getP());
		$data['gBase64'] = base64_encode($dh->getG());
		$data['l'] = $dh->getBits();
		$data['key'] = $dh->getPubKeyPEM();
		// No longer necessary
		//$data['pubKeyBase64'] = base64_encode($dh->getPubKey());
	} else {
		SessionManager::sessionStart();
		if (!isset($_SESSION['dh'])) {
			$errors['dh_not_initiated'] = "DH exchange: Must request server to initiate DH first.";
		} else {
			$dh = $_SESSION['dh'];
			try {
				$dh->computeKey($request);
				$_SESSION['aes'] = new AES();
			} catch(Exception $e) {
				$errors['dh_failed'] = "DH exchange: " . $e->getMessage();
			}
		}
	}
}

if ( ! empty($errors)) {
	$data['errors']  = $errors;
	$data['success'] = false;
} else {
	$data['success'] = true;
}

echo json_encode($data);

?>
