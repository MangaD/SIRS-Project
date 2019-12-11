<?php

require_once 'inc/utilities.php';
require_once 'inc/DH.php';
require_once 'inc/AES.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set("log_errors", 1);
// Probably need to change path
ini_set("error_log", "/srv/http/server/php-error.log");

cors();

$errors = array();
$data = array();

if (!isInstalled()) {
	$errors['not_installed'] = $app_title . ' server is not installed.';
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

	SessionManager::sessionStart();

	if (empty($request)) {

		$dh = new DH();
		$_SESSION['dh'] = $dh;
		$data['pBase64'] = base64_encode($dh->getP());
		$data['gBase64'] = base64_encode($dh->getG());
		$data['l'] = $dh->getBits();
		$data['key'] = $dh->getPubKeyPEM();
		
		// Sign DH public key with RSA private key
		// fetch RSA private key from file and ready it

		// It's actually "file://key.pem" when you want to give a relative path using unix systems. 
		// It will be three '/' in case of absolute path (e.g "file:///home/username/..."). 
		// https://www.php.net/manual/en/function.openssl-pkey-get-private.php#114998
		$pkeyid = openssl_pkey_get_private("file://inc/private_key.pem");
		if ($pkeyid === false) {
			$errors['load_failed'] = "Failed to load server private key.";
		} else {
			$signature = '';
			$signature_alg = "sha256";
			if (!in_array($signature_alg, openssl_get_md_methods())) {
				$errors['sign_failed'] = "Signature algorithm '" . $signature_alg . "' not available.";
			} else {
				if (!openssl_sign($dh->getPubKeyPEM(), $signature, $pkeyid, $signature_alg)) {
					$errors['sign_failed'] = "Failed to sign DH public key.";
				} else {
					$data['signedKey'] = base64_encode($signature);
					$data['pubKeyRSA'] = file_get_contents("inc/public_key.pem");
				}
			}
			openssl_free_key($pkeyid);
		}

	} else {
		
		if (!isset($_SESSION['dh'])) {
			$errors['dh_not_initiated'] = "DH exchange: Must request server to initiate DH first.";
		} else {

			if (!array_key_exists("signedPubKeyPEM", $json)) {
				$errors['dh_pub_not_signed'] = "You did not sign your public DH value.";
			} else {
				// TODO - Get client's public key from database
				$signedPubKeyPEM = trim($json['signedPubKeyPEM']);

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
}

if ( ! empty($errors)) {
	$data['errors']  = $errors;
	$data['success'] = false;
} else {
	$data['success'] = true;
}

echo json_encode($data);

?>
