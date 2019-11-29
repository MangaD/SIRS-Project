<?php

define('PROJECT_ROOT', dirname(dirname(__FILE__))."/");

require_once PROJECT_ROOT.'inc/config.php';

?>

<!DOCTYPE html>

<html lang="en">
<head>
	<title>Install <?php echo $app_title; ?></title>
	<meta charset="UTF-8">
	<link rel='shortcut icon' type='image/x-icon' href='../favicon.ico' />

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
		<h1>Install <?php echo $app_title; ?></h1>
		<!-- Tab list -->
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="table-tab" data-toggle="tab"
				   href="#tables" role="tab" aria-controls="tables"
				   aria-selected="true">1. Create tables</a>
			</li>
			<li class="nav-item">
				<a class="nav-link disabled" id="config-tab" data-toggle="tab"
				   href="#config" role="tab" aria-controls="config"
				   aria-selected="true">2. Write configuration file</a>
			</li>
			<li class="nav-item">
				<a class="nav-link disabled" id="done-tab" data-toggle="tab"
				   href="#done" role="tab" aria-controls="done"
				   aria-selected="true">3. Done</a>
			</li>
		</ul>
		<div class="tab-content">
			<!-- SQL tables tab -->
			<div id="tables" class="tab-pane fade show active"
			     role="tabpanel" aria-labelledby="table-tab">
				<form id="build_db" action="build_db.php" method="post">
					<div id="server-group" class="form-group">
						<label for="server">Server:</label>
						<input type="text" class="form-control"
						       name="server" value="localhost" placeholder="Server">
					</div>
					<div id="user-group" class="form-group">
						<label for="user">SQL User:</label>
						<input type="text" class="form-control"
						       name="user" placeholder="User">
					</div>
					<div id="pwd-group" class="form-group">
						<label for="pwd">SQL Password:</label>
						<input type="password" class="form-control"
						       name="pwd" placeholder="Password">
					</div>
					<div id="db-group" class="form-group">
						<label for="db">Database:</label>
						<input type="text" class="form-control"
						       name="db" placeholder="Database">
					</div>
					<button type="submit" class="btn btn-default">
						Create tables</button>
				</form>
			</div>
			<!-- Config file tab -->
			<div id="config" class="tab-pane fade"
			     role="tabpanel" aria-labelledby="config-tab">
				<br />
				<h4>Duo config:</h4>
				<div id="akey-group" class="form-group">
					<div class="alert alert-info">
						Your akey is a random string that you generate and keep secret from Duo. It
						should be at least 40 characters long and stored alongside your Web SDK
						application's integration key (ikey) and secret key (skey) in a configuration file.
					</div>
					<label for="akey">AKEY:</label>
					<input type="text" class="form-control"
							name="akey" placeholder="AKEY">
					<button id="generateAKEYBtn" type="button" class="btn">Generate random AKEY</button>
				</div>
				<div id="ikey-group" class="form-group">
					<div class="alert alert-info">
						IKEY, SKEY, and HOST should come from the Duo Security admin dashboard
						on the integrations page. For security reasons, these keys are best stored
						outside of the webroot in a production implementation.
 					</div>
					<label for="ikey">IKEY:</label>
					<input type="text" class="form-control"
							name="ikey" placeholder="IKEY">
				</div>
				<div id="skey-group" class="form-group">
					<label for="skey">SKEY:</label>
					<input type="text" class="form-control"
							name="skey" placeholder="SKEY">
				</div>
				<div id="host-group" class="form-group">
					<label for="host">HOST:</label>
					<input type="text" class="form-control"
							name="host" placeholder="HOST">
				</div>
				<br />
				<h4>Configuration file defines:</h4>
				<pre id="config-file" style="background-color: #eee;padding: 5px 10px;">
					<!-- TO BE FILLED -->
				</pre>
				<button id="create-config" type="button" class="btn btn-default">
					 Generate configuration file.</button>
			</div>
			<div id="done" class="tab-pane fade"
			     role="tabpanel" aria-labelledby="user-tab">
				 <br />
				 <div class="alert alert-success">
					 You are all set. You may now enjoy the application.
				 </div>
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

