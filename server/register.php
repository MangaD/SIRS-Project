<?php

require_once 'inc/dbclass.php';
require_once 'inc/utilities.php';

cors();

$errors = array();
$data = array();

if (!isInstalled()) {
	$errors['not_installed'] = 'Server is not installed.';
} elseif (SessionManager::isLoggedIn()) {
	$errors['already_logged'] = true;
	$data['username'] = $_SESSION['username'];
	$data['uid'] = $_SESSION['uid'];
} elseif ($_SERVER['REQUEST_METHOD'] != 'POST') {
	$errors['post'] = 'Must send data over POST request method.';
}

$username = $password = $confirm_pass = $pubKeyRSA_PEM = "";

$json = "";

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
	}

	if (empty($errors)) {

		if (!array_key_exists("username", $json) || !array_key_exists("password", $json) ||
				!array_key_exists("confirm_password", $json) ||
				!array_key_exists("pubKeyRSA_PEM", $json)) {
			$errors['arguments'] = "You did not provide one of the following: username, password, RSA public key.";
		} else {

			$username = trim($json['username']);
			$password = trim($json['password']);
			$confirm_pass = trim($json['confirm_password']);
			$pubKeyRSA_PEM = trim($json['pubKeyRSA_PEM']);

			$pubKeyRes = openssl_pkey_get_public($pubKeyRSA_PEM);
			if (!$pubKeyRes) {
				$errors['invalid_key'] = "The RSA public key you provided is not valid.";
			} else {
				openssl_pkey_free($pubKeyRes);
			}

			if (empty($username)) {
				$errors['user'] = 'Please enter username.';
			} else if (strlen($username) < 6 || strlen($username) > 12) {
				$errors['user'] = 'Username must have 6 to 12 characters.';
			} else if (!preg_match("/^[a-zA-Z0-9-_]{6,12}$/",$username)) {
				$errors['user'] = "Username must contain only alphanumeric, hyphen or underscore.";
			}

			if (empty($password)) {
				$errors['pwd'] = 'Please enter your password.';
			} else if (strlen($password) < 10) {
				$errors['pwd'] = 'Password must have a minimum of 10 characters.';
			} else if (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#\$%\^&]).{10,}$/",$password)) {
				$errors['pwd'] = "Password must contain uppercase, lowercase, digit and a special character.";
			} else if ($password !== $confirm_pass) {
				$errors['pwd'] = "Passwords don't match.";
			}
		}
	}
}

if (empty($errors)) {

	try {
		$dbclass = new DBClass();
		$conn = $dbclass->getConnection();

		$stmt = $conn->prepare(" SELECT uid
			FROM users
			WHERE username = :username ");

		$stmt->bindValue(':username', $username, PDO::PARAM_STR);

		$stmt->execute();

		if (($row = $stmt->fetch()) !== false) {
			$errors['user'] = 'This username is already taken.';
		} else {
			$stmt = $conn->prepare(" INSERT INTO users (username, password, pub_key) " .
				" VALUES (:username, :password, :pub_key) ");
			$stmt->bindValue(':username', $username, PDO::PARAM_STR);
			$stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
			$stmt->bindValue(':pub_key', $pubKeyRSA_PEM, PDO::PARAM_STR);

			if ($stmt->execute() === false) {
				// Because we have 'connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);'
				// in dbclass.php, this code should never be reached.
				$errors['exception'] = 'Unknown error.';
			}
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
	$data['message'] = "Registration successful.";
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

