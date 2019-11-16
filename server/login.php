<?php

require_once 'inc/dbclass.php';
require_once 'inc/utilities.php';

cors();

$errors = array();
$data = array();

if (!isInstalled()) {
	$errors['not_installed'] = $app_title . ' server is not installed.';
}
elseif (SessionManager::isLoggedIn()) {
	$errors['already_logged'] = true;
	$data['username'] = $_SESSION['username'];
	$data['uid'] = $_SESSION['uid'];
}

$username = $password = "";
$username_err = $password_err = "";

$json = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$json = json_decode(file_get_contents('php://input'), true);
	if (empty(trim($json['username']))) {
		$errors['user'] = 'Please enter username.';
	}
	if (empty(trim($json['password']))) {
		$errors['pwd'] = 'Please enter your password.';
	}
	$username = trim($json['username']);
	$password = trim($json['password']);
	if (strlen($password) < 8) {
		$errors['pwd'] = 'Password must have a minimum of 8 characters.';
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

		$stmt->bindParam(':username', $username);

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

