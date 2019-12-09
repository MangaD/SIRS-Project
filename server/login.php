<?php

require_once '2fa/Web.php';
require_once 'inc/dbclass.php';
require_once 'inc/utilities.php';
require_once 'DH.php';
require_once 'AES.php';

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
	$errors['not_installed'] = 'Server is not installed.';
}
elseif (SessionManager::isLoggedIn()) {
	$errors['already_logged'] = true;
	$data['username'] = $_SESSION['username'];
	$data['uid'] = $_SESSION['uid'];
}

$username = $password = "";

$json = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$json = json_decode(file_get_contents('php://input'), true);

	if (array_key_exists("ciphertext", $json) && isset($_SESSION['dh']) && isset($_SESSION['aes'])) {
		$ciphertext = base64_decode(trim($json['ciphertext']));
		$key = $_SESSION['dh']->getSharedKey();
		$plaintext = $_SESSION['aes']->decrypt($ciphertext , $key);
		if ($plaintext === false) {
			$errors['decrypt'] = "Decryption failed.";
		} else {
			$json = json_decode($plaintext, true);
		}
	}

	if (empty($errors)) {

		if (!array_key_exists("username", $json) || !array_key_exists("password", $json)) {
			$errors['arguments'] = "You did not provide username and/or password.";
		} else {

			$username = trim($json['username']);
			$password = trim($json['password']);

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
			}
		}
	}	
} else {
	$errors['post'] = 'Must send data over POST request method.';
}

if (empty($errors)) {

	try {
		$dbclass = new DBClass();
		$conn = $dbclass->getConnection();

		$stmt = $conn->prepare(" SELECT uid, username, password
			FROM users
			WHERE username = :username ");

		$stmt->bindValue(':username', $username, PDO::PARAM_STR);

		$stmt->execute();

		if (($row = $stmt->fetch()) !== false) {
			if(password_verify($password, $row["password"])) {

				if (!array_key_exists("twoFAresponse", $json) || empty(trim($json['twoFAresponse']))) {
					$errors['2fa_response'] = "You did not provide 2FA response.";
					$errors['missing2FA'] = true;
					// Define Duo sig_request
					$data['sig_request'] = Duo\Web::signRequest(IKEY, SKEY, AKEY, $username);
					$data['host'] = HOST;
				} else {
					$sig_response = trim($json['twoFAresponse']);

					$resp = Duo\Web::verifyResponse(IKEY, SKEY, AKEY, $sig_response);
					if ($resp === $username) {
						SessionManager::sessionStart();
						$_SESSION['username'] = $username;
						$_SESSION['uid'] = $row["uid"];
						$data['username'] = $username;
						$data['uid'] = $row["uid"];
					} else {
						$errors['2fa_response'] = "Your 2FA response is invalid.";
						$errors['missing2FA'] = true;
						// Define Duo sig_request
						$data['sig_request'] = Duo\Web::signRequest(IKEY, SKEY, AKEY, $data['username']);
						$data['host'] = HOST;
					}
				}

			} else {
				$errors['pwd'] = 'The password you entered was not valid.';
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


if ( ! empty($errors)) {
	$data['errors']  = $errors;
	$data['success'] = false;
} else {
	$data['message'] = "Login successful.";
	$data['success'] = true;
}

echo json_encode($data);

?>

