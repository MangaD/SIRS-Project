<?php

require_once 'config.php';

class DBClass {

	private $host = DB_SERVER;
	private $username = DB_USERNAME;
	private $password = DB_PASSWORD;
	private $database = DB_NAME;

	public $connection;

	// May throw exception
	public function getConnection() {

		$this->connection = null;

		$this->connection = new PDO("mysql:host=" . $this->host .
			";dbname=" . $this->database,
			$this->username, $this->password);

		// set the PDO error mode to exception
		$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$this->connection->exec("set names utf8mb4");

		return $this->connection;
	}

	public function closeConnection() {
		$this->connection = null;
	}
}
?>

