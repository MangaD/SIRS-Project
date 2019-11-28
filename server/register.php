<?php

require_once 'inc/dbclass.php';
require_once 'inc/utilities.php';

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

$username = $password = $confirm_pass = "";

$json = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$json = json_decode(file_get_contents('php://input'), true);

	$username = trim($json['username']);
	$password = trim($json['password']);
	$confirm_pass = trim($json['confirm_password']);

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
} else {
	$errors['post'] = 'Must send data over POST request method.';
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
			$stmt = $conn->prepare(" INSERT INTO users (username, password) VALUES (:username, :password) ");
			$stmt->bindValue(':username', $username, PDO::PARAM_STR);
			$stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);

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

echo json_encode($data);

?>

