<?php

$errors = array();
$data = array();

if ( empty(trim($_REQUEST["server"])) ) {
	$errors['server'] = 'Server is required.';
}
if ( empty(trim($_REQUEST["user"])) ) {
	$errors['user'] = 'User is required.';
}
if ( empty(trim($_REQUEST["pwd"])) ) {
	$errors['pwd'] = 'Password is required.';
}
if ( empty(trim($_REQUEST["db"])) ) {
	$errors['db'] = 'Database is required.';
}

if ( ! empty($errors)) {
	$data['errors']  = $errors;
	$data['success'] = false;
} else {
	$servername = trim($_REQUEST["server"]);
	$username = trim($_REQUEST["user"]);
	$password = trim($_REQUEST["pwd"]);
	$db = trim($_REQUEST["db"]);
	try {
		$conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
		// set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = file_get_contents('createdb.sql');

		$conn->exec($sql);

		$data['message'] = "Database created successfully.";
		$data['success'] = true;

	}
	catch(PDOException $e) {
		$errors['exception'] = $e->getMessage();
		$data['errors'] = $errors;
		$data['success'] = false;
	}
	$conn = null;
}

echo json_encode($data);

?>
