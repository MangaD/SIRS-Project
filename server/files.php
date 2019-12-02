<?php
require_once 'inc/utilities.php';
require_once 'inc/dbclass.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set("log_errors", 1);
// Probably need to change path
ini_set("error_log", "php-error.log");
// For testing
error_log( "Hello, errors!" );


cors();

$errors = array();
$data = array();

if (!isInstalled()) {
	$errors['not_installed'] = $app_title . ' server is not installed.';
}
elseif (!SessionManager::isLoggedIn()) {
	$errors['not_logged'] = 'You are not logged in!';
}

if (empty($errors) && $_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_FILES['files'])) {
		$errors = [];
		$path = 'files/';
		$extensions = ['txt', 'pdf'];

		$all_files = count($_FILES['files']['tmp_name']);

		for ($i = 0; $i < $all_files; $i++) {
			$file_name = $_FILES['files']['name'][$i];
			$file_tmp = $_FILES['files']['tmp_name'][$i];
			$file_type = $_FILES['files']['type'][$i];
			$file_size = $_FILES['files']['size'][$i];
			$file_ext = strtolower(end(explode('.', $_FILES['files']['name'][$i])));

			$file = $path . $file_name;

			if (!in_array($file_ext, $extensions)) {
				$errors['extension'] = 'Extension not allowed: ' . $file_name . ' ' . $file_type;
				error_log( "Invalid" );
			}

			if ($file_size > 2097152) {
				$errors['size'] = 'File size exceeds limit: ' . $file_name . ' ' . $file_type;
			}

			if (empty($errors)) {
				$dbclass = new DBClass();
				$conn = $dbclass->getConnection();

				$stmt = $conn->prepare(" INSERT INTO files(owner, name, path, hash) " .
					" VALUES (:owner, :name, :path, :hash) ");

				$new_file_name = $path. hash_file('sha256', $file_tmp) . "." . $file_ext;

				$stmt->bindValue(':owner',  $_SESSION['uid'], PDO::PARAM_INT);
				$stmt->bindValue(':name', $file_name, PDO::PARAM_STR);
				$stmt->bindValue(':path', $new_file_name, PDO::PARAM_STR);
				$stmt->bindValue(':hash', hash_file('sha256', $file_tmp), PDO::PARAM_STR);

				$stmt->execute();

				move_uploaded_file($file_tmp, $new_file_name);
			}
		}
	}
}

if ( ! empty($errors)) {
	$data['errors']  = $errors;
	$data['success'] = false;
	error_log( "Invalid 2" );
} else {
	$data['success'] = true;
}

echo json_encode($data);


?>
