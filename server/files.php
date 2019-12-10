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

echo json_encode($data);

 ?>
