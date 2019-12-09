<?php

require_once 'inc/utilities.php';
require_once 'DH.php';
require_once 'AES.php';

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
