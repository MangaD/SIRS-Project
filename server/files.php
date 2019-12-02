<?php

/**
 * In your "php.ini" file, search for the file_uploads directive, and set it to On:
 * file_uploads = On
 * 
 * Depending on maximum file size to allow, you can also modify:
 * upload_max_filesize = 2M
 */

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

setlocale(LC_ALL,'en_US.UTF-8');

$errors = array();
$data = array();

// Must have trailing slash
$folderPath = 'files/';
$validExtensions = ['txt', 'pdf'];
$validTypes = ['text/plain', 'application/pdf',
	'application/wps-office.pdf'];
// Must also change 'upload_max_filesize' in your "php.ini" file.
$maxFileSize = 2*1024*1024; // 2MiB

if (!isInstalled()) {
	$errors['not_installed'] = $app_title . ' server is not installed.';
} elseif (!SessionManager::isLoggedIn()) {
	$errors['not_logged'] = 'You are not logged in!';
} elseif ($_SERVER['REQUEST_METHOD'] != 'POST') {
	$errors['post'] = 'Must send data over POST request method.';
}

if (empty($errors)) {
	if (isset($_FILES['files'])) {

		$all_files = count($_FILES['files']['tmp_name']);

		for ($i = 0; $i < $all_files; $i++) {
			$file_name = $_FILES['files']['name'][$i];
			// https://stackoverflow.com/questions/37008227/what-is-the-difference-between-name-and-tmp-name
			$file_tmp = $_FILES['files']['tmp_name'][$i];
			$file_type = $_FILES['files']['type'][$i];
			$file_size = $_FILES['files']['size'][$i];
			// https://stackoverflow.com/questions/173868/how-do-i-get-extract-a-file-extension-in-php
			$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

			$file = $folderPath . $file_name;

			if (!in_array($file_ext, $validExtensions)) {
				$errors['extension'] = 'Extension not allowed. File name: ' . $file_name .
					'. File extension: ' . $file_ext;
			}

			if (!in_array($file_type, $validTypes)) {
				$errors['size'] = 'File type not allowed. File name: ' . $file_name .
				'. File type: ' . $file_type;
			}

			if ($file_size > $maxFileSize) {
				$errors['size'] = 'File size exceeds limit. File name: ' . $file_name .
					'. File size: ' . $file_size .
					'. Max. size: ' . $maxFileSize/1024/1024 . "MiB";
			}

			if (empty($errors)) {

				try {
					$dbclass = new DBClass();
					$conn = $dbclass->getConnection();

					$stmt = $conn->prepare(" INSERT INTO files(owner, name, path, hash) " .
						" VALUES (:owner, :name, :path, :hash) ");

					$fileHash = hash_file('sha256', $file_tmp);

					$new_file_name = $folderPath . $fileHash . "." . $file_ext;

					$stmt->bindValue(':owner', $_SESSION['uid'], PDO::PARAM_INT);
					$stmt->bindValue(':name', $file_name, PDO::PARAM_STR);
					$stmt->bindValue(':path', $new_file_name, PDO::PARAM_STR);
					$stmt->bindValue(':hash', $fileHash, PDO::PARAM_STR);

					$stmt->execute();

					if (!move_uploaded_file($file_tmp, $new_file_name)) {
						$stmt = $conn->prepare(" DELETE FROM files WHERE " .
						" owner = :owner AND hash = :hash ");
	
						$stmt->bindValue(':owner', $_SESSION['uid'], PDO::PARAM_INT);
						$stmt->bindValue(':hash', $fileHash, PDO::PARAM_STR);
	
						$stmt->execute();
	
						$errors['move_uploaded_file'] = "Sorry, there was an error uploading your file " .
							"on 'move_uploaded_file' function.";
					}
				} catch(PDOException $e) {
					if ($e->errorInfo[1] == 1062) {
						$errors['already_exists'] = "You have already uploaded this file.";
					} else {
						$errors['exception'] = $msg;
					}
				}

				$dbclass->closeConnection();
			}
		}
	} else {
		$errors['_FILES'] = "\$_FILES['files'] not set.";
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
