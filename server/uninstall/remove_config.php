<?php

$errors = array();
$data = array();

$file = realpath("../inc/config.php");

if( ini_get('allow_url_fopen') == "0" ) {
	$errors['allow_url_fopen'] = "'allow_url_fopen' must be set to 1 in your php.ini file.";
}

$content = file_get_contents($file);

if($content === false) {
	$errors['file_missing'] = 'Configuration file is missing.';
} else {
	$pattern = "/^define(.+)\n\n?/im";
	$replacement = '';
	$content = preg_replace($pattern, $replacement, $content);

	if(strpos($content, 'DB_SERVER') == true) {
		if (file_put_contents($file, $content, LOCK_EX) == false) {
			$errors['file_writing'] = 'Failed to write to configuration file.<br />' .
			"You may write the code manually to the file and click the button again.";
		}
		$content = file_get_contents($file);
		if(strpos($content, 'DB_SERVER') == true) {
			$errors['file_writing'] = 'Failed to write to configuration file.<br />' .
			"You may remove the code manually from the file and click the button again.";
		}
	}
}

if ( ! empty($errors)) {
	$data['errors']  = $errors;
	$data['success'] = false;
} else {
	$data['message'] = "Configuration file written successfully.";
	$data['success'] = true;
}

echo json_encode($data);

?>
