<?php

$errors = array();
$data = array();

require_once '../inc/config.php';

if(!defined('DB_SERVER') || !defined('DB_NAME') || !defined('DB_USERNAME')
   || !defined('DB_PASSWORD')) {
	$errors['not_installed'] = $app_title . ' is not installed.';
} else {
	try {
		$conn = new PDO("mysql:host=" .
		                 constant("DB_SERVER") . ";dbname=" .
						 constant("DB_NAME"), constant("DB_USERNAME"),
						 constant("DB_PASSWORD"));
		// set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stmt = $conn->prepare(" SELECT concat('DROP TABLE IF EXISTS `', table_name, '`;') " .
		" FROM information_schema.tables " .
		" WHERE table_schema = '" . constant("DB_NAME") . "'; ");

		$stmt->execute();

		$result = $stmt->fetchAll();
		$query = "SET FOREIGN_KEY_CHECKS = 0;";
		foreach ($result as $value) {
			$query .= $value[0];
		}
		$query .= "SET FOREIGN_KEY_CHECKS = 1;";

		try {
			$conn->beginTransaction();
			$conn->exec($query);
			$conn->commit();
		} catch (PDOException $e) {
			$conn->rollBack();
			throw $e;
		}

		//$stmt = $conn->exec("DROP DATABASE IF EXISTS " . constant("DB_NAME"));

	}
	catch(PDOException $e) {
		if ($e->getCode() == 1049) {
			$errors['exception'] = "Database is already removed.";
		} else {
			$errors['exception'] = $e->getMessage();
		}
	}
	$conn = null;
}

if ( ! empty($errors)) {
	$data['errors']  = $errors;
	$data['success'] = false;
} else {
	$data['message'] = "Database removed successfully.";
	$data['success'] = true;
}

echo json_encode($data);

?>
