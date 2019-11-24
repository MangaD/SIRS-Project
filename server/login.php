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

$username = $password = "";

$json = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$json = json_decode(file_get_contents('php://input'), true);

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
				SessionManager::sessionStart();
				$_SESSION['username'] = $username;
				$_SESSION['uid'] = $row["uid"];
				$data['username'] = $_SESSION['username'];
				$data['uid'] = $_SESSION['uid'];
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

