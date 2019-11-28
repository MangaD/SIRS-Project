<?php

require_once 'inc/utilities.php';

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
			}

			if ($file_size > 2097152) {
				$errors['size'] = 'File size exceeds limit: ' . $file_name . ' ' . $file_type;
			}

			if (empty($errors)) {
				move_uploaded_file($file_tmp, $file);
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
