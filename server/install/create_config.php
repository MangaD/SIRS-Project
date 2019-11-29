<?php

$errors = array();
$data = array();

$file = realpath("../inc/config.php");

if ( empty($_REQUEST["server"]) ) {
	$errors['server'] = 'Server is required.';
}
if ( empty($_REQUEST["user"]) ) {
	$errors['user'] = 'User is required.';
}
if ( empty($_REQUEST["pwd"]) ) {
	$errors['pwd'] = 'Password is required.';
}
if ( empty($_REQUEST["db"]) ) {
	$errors['db'] = 'Database is required.';
}
if ( empty($_REQUEST["akey"]) ) {
	$errors['akey'] = 'AKEY is required.';
}
if ( empty($_REQUEST["ikey"]) ) {
	$errors['ikey'] = 'IKEY is required.';
}
if ( empty($_REQUEST["skey"]) ) {
	$errors['skey'] = 'SKEY is required.';
}
if ( empty($_REQUEST["host"]) ) {
	$errors['host'] = 'HOST is required.';
}
if( ini_get('allow_url_fopen') == "0" ) {
	$errors['allow_url_fopen'] = "'allow_url_fopen' must be set to 1 in your php.ini file.";
}

if ( ! empty($errors)) {
	$data['errors']  = $errors;
	$data['success'] = false;
} else {
	$servername = $_REQUEST["server"];
	$username = $_REQUEST["user"];
	$password = $_REQUEST["pwd"];
	$db = $_REQUEST["db"];

	$akey = $_REQUEST["akey"];
	$ikey = $_REQUEST["ikey"];
	$skey = $_REQUEST["skey"];
	$host = $_REQUEST["host"];

	$credentials = "/* Database configuration */\n" .
	               "define('DB_SERVER', '{$servername}');\n" .
	               "define('DB_USERNAME', '{$username}');\n" .
	               "define('DB_PASSWORD', '{$password}');\n" .
	               "define('DB_NAME', '{$db}');\n\n" .
	               "/* Duo configuration */\n" .
	               "/*\n" .
	               " * Your akey is a random string that you generate and keep secret from Duo. It\n" .
	               " * should be at least 40 characters long and stored alongside your Web SDK\n" .
	               " * application's integration key (ikey) and secret key (skey) in a configuration file.\n" .
	               " */\n" .
	               "define('AKEY', '{$akey}');\n" .
	               "/*\n" .
	               " * IKEY, SKEY, and HOST should come from the Duo Security admin dashboard\n" .
	               " * on the integrations page. For security reasons, these keys are best stored\n" .
	               " * outside of the webroot in a production implementation.\n" .
	               " */\n" .
	               "define('IKEY', '{$ikey}');\n" .
	               "define('SKEY', '{$skey}');\n" .
	               "define('HOST', '{$host}');\n\n";

	$content = file_get_contents($file);

	if($content === false) {
		$errors['file_missing'] = 'Configuration file is missing.';
	} else if(strpos($content, 'DB_SERVER') !== false) {
		//Success
	} else {
		$content = str_replace('?>', $credentials . '?>', $content);

		if (file_put_contents($file, $content, LOCK_EX) == false) {
			$errors['file_writing'] = 'Failed to write to configuration file.<br />' .
			"You may write the code manually to the file and click the button again.";
		}
		$content = file_get_contents($file);
		if(strpos($content, 'DB_SERVER') == false) {
			$errors['file_writing'] = 'Failed to write to configuration file.<br />' .
			"You may write the code manually to the file and click the button again.";
		}
	}

	if ( ! empty($errors)) {
		$data['errors']  = $errors;
		$data['success'] = false;
	} else {
		$data['message'] = "Configuration file written successfully.";
		$data['success'] = true;
	}
}

echo json_encode($data);

?>

