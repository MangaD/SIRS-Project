<?php
require_once 'inc/utilities.php';
require_once 'inc/dbclass.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set("log_errors", 1);
// Probably need to change path
ini_set("error_log", "php-error.log");

cors();

$error = array();
$data = array();

$json = "";

if (!isInstalled()) {
	$errors['not_installed'] = $app_title . ' server is not installed.';
} elseif (!SessionManager::isLoggedIn()) {
	$errors['not_logged'] = 'You are not logged in!';
} elseif ($_SERVER['REQUEST_METHOD'] != 'POST') {
	$errors['post'] = 'Must send data over POST request method.';
}

if (empty($errors)) {
	$json = json_decode(file_get_contents('php://input'), true);

	if (!array_key_exists("hash", $json)) {
    	$errors['arguments'] = "You did not provide a hash to identify the file for download.";
	} else {
    	$hash = trim($json['hash']);

		try {
			$dbclass = new DBClass();
			$conn = $dbclass->getConnection();

			$stmt = $conn->prepare(" SELECT path
				FROM files
				WHERE hash = :hash ");

			$stmt->bindValue(':hash', $hash, PDO::PARAM_STR);

			$stmt->execute();

			if (($row = $stmt->fetch()) !== false) {
				$data['path'] = $row;
			}
		}
		catch(PDOException $e) {
			$errors['exception'] = $e->getMessage();
		}
		$dbclass->closeConnection();
	}
}

if ( ! empty($errors)) {
	$data['errors']  = $errors;
	$data['success'] = false;
} else {
	$data['message'] = "Download";
	$data['success'] = true;
}

echo json_encode($data);
 ?>
