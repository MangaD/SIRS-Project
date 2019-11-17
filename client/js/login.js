function isLoggedIn() {
	postData("login.php", {})
	.then((data) => {
		if (!data.success) {
			if (data.errors.already_logged === true) {
				return true;
			} else { return false; }
		} else { return false; }
	})
	.catch((error2) => {
		return false;
	});
}

function login() {

	loaderStart();

	postData("login.php", {
		username: document.getElementById("log_username").value,
		password: document.getElementById("log_password").value,
	})
	.then((data) => {
		if (!data.success) {
			if (data.errors.already_logged === true) {
				window.username = data.username;
				window.uid = data.uid;

				alert("Login successful!");

				// TODO Switch to main window
			} else {

				for(var k in data.errors) {
					$("#login_alerts").append(
						'<div id="error_alert" class="alert alert-danger alert-dismissible" role="alert">' +
						'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
						data.errors[k] +
						'</div>'
					);
				}
			}
		}

		//console.log(data);
		loaderEnd();
	})
	.catch((error2) => {
		$("#login_alerts").append(
			'<div id="error_alert" class="alert alert-danger alert-dismissible" role="alert">' +
			'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
			'Failed to connect to the server.' +
			'</div>'
		);
		//console.log(error2);
		loaderEnd();
	});
}


