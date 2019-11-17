function register() {

	loaderStart();

	$("#register_alerts").html('');

	postData("register.php", {
		username: document.getElementById("reg_username").value,
		password: document.getElementById("reg_password").value,
		confirm_password: document.getElementById("reg_confirm_password").value,
	})
	.then((data) => {
		if (!data.success) {
			if (data.errors.already_logged === true) {
				window.username = data.username;
				window.uid = data.uid;

				alert("Alreade logged in!");

				// TODO Switch to main window
			} else {

				for(var k in data.errors) {
					$("#register_alerts").append(
						'<div id="error_alert" class="alert alert-danger alert-dismissible" role="alert">' +
						'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
						data.errors[k] +
						'</div>'
					);
				}
			}
		} else {
			showLoginPage();
		}

		//console.log(data);
		loaderEnd();
	})
	.catch((error2) => {
		$("#register_alerts").append(
			'<div id="error_alert" class="alert alert-danger alert-dismissible" role="alert">' +
			'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
			'Failed to connect to the server.' +
			'</div>'
		);
		//console.log(error2);
		loaderEnd();
	});
}
