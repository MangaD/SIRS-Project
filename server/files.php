<?php

require_once 'inc/utilities.php';
require_once 'inc/dbclass.php';

cors();

$error = array();
$data = array();

if (!isInstalled()) {
	$errors['not_installed'] = $app_title . ' server is not installed.';
} elseif (!SessionManager::isLoggedIn()) {
	$errors['not_logged'] = 'You are not logged in!';
} elseif ($_SERVER['REQUEST_METHOD'] != 'POST') {
	$errors['post'] = 'Must send data over POST request method.';
}

$usingSecureChannel = false;

if (empty($errors)) {

	$json = json_decode(file_get_contents('php://input'), true);
	
	if (array_key_exists("ciphertext", $json)) {
		$usingSecureChannel = true;
		$ciphertext = base64_decode(trim($json['ciphertext']));
		try {
			$plaintext = decryptWithSessionKey($ciphertext);
			$json = json_decode($plaintext, true);
		} catch(Exception $e) {
			$errors['decrypt'] = $e->getMessage();
		}
		if (isset($_SESSION['allow_register_only']) && $_SESSION['allow_register_only'] === true) {
			$errors['allow_register_only'] = "Because you did not sign your DH public value you are only allowed to register.";
		}
	}

}

if (empty($errors)) {
	try {
		$dbclass = new DBClass();
		$conn = $dbclass->getConnection();

		// TODO Select only files where user has permissions to view
		$stmt = $conn->prepare(" SELECT username, name, hash, f.created_at, f.size
			FROM files AS f INNER JOIN users AS u ON f.owner = u.uid");

		$stmt->execute();

		if (($row = $stmt->fetchAll()) !== false) {
			$data['list'] = $row;
		} else {
			$errors['show'] = 'fetchAll failed.';
		}
	}
	catch(PDOException $e) {
		$errors['exception'] = $e->getMessage();
	}
	$dbclass->closeConnection();
}

if ( ! empty($errors)) {
	$data['errors']  = $errors;
	$data['success'] = false;
} else {
	$data['message'] = "Fetched";
	$data['success'] = true;
}

$response_json_data = '';

if ($usingSecureChannel === true) {
	try {
		$ciphertext = base64_encode(encryptWithSessionKey(json_encode($data)));
		$data = array();
		$data['success'] = true;
		$data['ciphertext'] = $ciphertext;
		$response_json_data = json_encode($data);
	} catch(Exception $e) {
		$data = array();
		$error = array();
		$errors['encrypt'] = $e->getMessage();
		$data['errors']  = $errors;
		$data['success'] = false;
		$response_json_data = json_encode($data);
	}
} else {
	$response_json_data = json_encode($data);
}

echo $response_json_data;

?>
