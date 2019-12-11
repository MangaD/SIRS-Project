<?php

require_once 'inc/utilities.php';
require_once 'inc/dbclass.php';

cors();

$error = array();
$data = array();

$json = "";

$name = $hash = $path = "";

if (!isInstalled()) {
	$errors['not_installed'] = $app_title . ' server is not installed.';
} elseif (!SessionManager::isLoggedIn()) {
	$errors['not_logged'] = 'You are not logged in!';
} elseif ($_SERVER['REQUEST_METHOD'] != 'POST') {
	$errors['post'] = 'Must send data over POST request method.';
}

if (empty($errors)) {

	$json = json_decode(file_get_contents('php://input'), true);

	if (!array_key_exists("hash", $json) || !array_key_exists("name", $json)) {
		$errors['arguments'] = "You did not provide a hash and/or name for the file to download.";
	} else {

		$name = trim($json['name']);
		$hash = trim($json['hash']);

		try {
			$dbclass = new DBClass();
			$conn = $dbclass->getConnection();

			$stmt = $conn->prepare(" SELECT name, path
				FROM files
				WHERE hash = :hash ");

			$stmt->bindValue(':hash', $hash, PDO::PARAM_STR);

			$stmt->execute();

			if (($row = $stmt->fetch()) !== false) {
				$data['name'] = $row["name"];
				$path = $row["path"];
			} else {
				$errors['not_exists'] = "File '" . $name . "' does not exist in the database.";
			}
		}
		catch(PDOException $e) {
			$errors['exception'] = $e->getMessage();
		}
		$dbclass->closeConnection();
	}
}

if (!file_exists($path)) {
	$errors['not_exists'] = "File '" . $name . "' does not exist in the database.";
}

if ( ! empty($errors)) {
	$data['errors']  = $errors;
	$data['success'] = false;
} else {
	$data['message'] = "Download success.";
	$data['success'] = true;
}

if ($_SERVER['HTTP_ACCEPT'] === 'application/json') {
	echo json_encode($data);
} else if ($data['success'] === true) {

	// CREATE TEMPORARY FILE CIPHERED TO SEND

	// Get original file as a string
	$file_contents = file_get_contents($path);
	//Encrypt file contents with secret key
	$file_contents = encryptWithSessionKey($file_contents);
	// Write temporary file
	$temp = tmpfile();
	// https://stackoverflow.com/questions/11212569/retrieve-path-of-tmpfile
	$metaDatas = stream_get_meta_data($temp);
	$tmpFilename = $metaDatas['uri'];
	fwrite($temp, $file_contents);
	fseek($temp, 0);
	$path = $tmpFilename;


	// https://serverfault.com/questions/316814/php-serve-a-file-for-download-without-providing-the-direct-link
	// https://www.php.net/manual/en/function.readfile.php

	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.$path.'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($path));

	// https://stackoverflow.com/questions/8041564/php-readfile-adding-extra-bytes-to-downloaded-file
	// add these two lines
	ob_clean();   // discard any data in the output buffer (if possible)
	flush();      // flush headers (if possible)

	// Get file from path
	readfile($path);

	// Close temporary file
	fclose($temp);
}

?>
