<?php

require_once 'inc/utilities.php';
require_once 'inc/dbclass.php';
require_once 'inc/DH.php';
require_once 'inc/AES.php';

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
				$username = '';
				if (isset($_SESSION['username'])) {
					$username = $_SESSION['username'];
				} else if (array_key_exists("username", $json)) {
					$username = trim($json['username']);
				}

				if ($username === '') {
					$_SESSION['allow_register_only'] = true;
				} else {
					try {
						$dbclass = new DBClass();
						$conn = $dbclass->getConnection();
				
						$stmt = $conn->prepare(" SELECT pub_key
							FROM users
							WHERE username = :username ");
				
						$stmt->bindValue(':username', $username, PDO::PARAM_STR);
				
						$stmt->execute();
				
						if (($row = $stmt->fetch()) !== false) {
							// Get client's public key
							$clientPubKeyRSA = $row["pub_key"];

							// Verify signed public value

							$signature_alg = "sha256";
							if (!in_array($signature_alg, openssl_get_md_methods())) {
								$errors['verify_failed'] = "Signature algorithm '" . $signature_alg . "' not available.";
							} else {
								$signedPubKeyPEM = base64_decode(trim($json['signedPubKeyPEM']));
								$pubkeyid = openssl_pkey_get_public($clientPubKeyRSA);
								if ($pubkeyid === false) {
									$errors['verify_failed'] = "Failed to load your RSA public key.";
								} else {
									if (openssl_verify($request, $signedPubKeyPEM, $pubkeyid, $signature_alg) !== 1) {
										$errors['verify_failed'] = "Signature of your DH public value is invalid.";
									} else {
										$_SESSION['allow_register_only'] = false;
									}
									openssl_free_key($pubkeyid);
								}
							}
						} else {
							$errors['user'] = 'No account found with that username.';
						}
				
					}
					catch(PDOException $e) {
						$errors['exception'] = $e->getMessage();
					}
					$dbclass->closeConnection();
				}

				if (empty($errors)) {
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
}

if ( ! empty($errors)) {
	$data['errors']  = $errors;
	$data['success'] = false;
} else {
	$data['success'] = true;
}

echo json_encode($data);

?>
