<?php

/* Copyright (c) 2018 Insomnium Eye */

define('PROJECT_ROOT', dirname(dirname(__FILE__))."/");

require_once PROJECT_ROOT.'inc/config.php';

?>

<!DOCTYPE html>

<html lang="en">
<head>
	<title>Uninstall <?php echo $app_title; ?></title>
	<meta charset="UTF-8">

	<link rel="shortcut icon" href="../favicon.png" type="image/png">

	<style type="text/css">
		a.disabled {
			pointer-events: none;
		}
	</style>

	<!-- Bootstrap, Font Awesome -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!--Bootsrap 4 CDN-->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
		integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
		crossorigin="anonymous">

	<!--Fontawesome CDN-->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css"
		integrity="sha384-KA6wR/X5RY4zFAHpv/CnoG2UW1uogYfdnP67Uv7eULvTveboZJg0qUpmJZb5VqzN"
		crossorigin="anonymous" />

</head>

<body>
	<div class="col-sm-6 offset-sm-3">
		<h1>Uninstall <?php echo $app_title; ?></h1>
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="database-tab" data-toggle="tab"
				   href="#database" role="tab" aria-controls="database"
				   aria-selected="true">1. Delete database</a>
			</li>
			<li class="nav-item">
				<a class="nav-link disabled" id="config-tab" data-toggle="tab"
				   href="#config" role="tab" aria-controls="config"
				   aria-selected="true">2. Remove configuration file</a>
			</li>
			<li class="nav-item">
				<a class="nav-link disabled" id="done-tab" data-toggle="tab"
				   href="#done" role="tab" aria-controls="done"
				   aria-selected="true">3. Done</a>
			</li>
		</ul>
		<div class="tab-content">
			<div id="database" class="tab-pane fade show active"
			     role="tabpanel" aria-labelledby="database-tab">
				<div class="alert alert-warning" role="alert">
					Attention! All data will be lost after this operation.
				</div>
				<button id="remove-db" type="button" class="btn btn-default">
					 Remove database.</button>
			</div>
			<div id="config" class="tab-pane fade"
			     role="tabpanel" aria-labelledby="config-tab">
				<br />
				<pre id="remove-file"></pre>
				<button id="remove-config" type="button" class="btn btn-default">
					 Remove configuration file.</button>
			</div>
			<div id="done" class="tab-pane fade"
			     role="tabpanel" aria-labelledby="user-tab">
				 <br /><div class="alert alert-success">Application uninstalled successfully.</div>
			</div>
		</div>
	</div>

	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.4.1.min.js"
		integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
		crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
		integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
		crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
		integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
		crossorigin="anonymous"></script>

	<script type="text/javascript" src="form.js"></script>
</body>
</html>
