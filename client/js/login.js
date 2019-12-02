"use strict";

function login() {

	loaderStart();

	$("#login_alerts").html('');
	
	postJSONData("login.php", {
		username: document.getElementById("log_username").value,
		password: document.getElementById("log_password").value,
		twoFAresponse: (window.twoFAresponse ? window.twoFAresponse : ""),
	})
	.then((data) => {
		
		if (!data.success) {
			if (data.errors.already_logged === true) {
				window.username = data.username;
				window.uid = data.uid;

				showMainPage();
			} else if (data.errors.missing2FA === true) {
				/*
				* Perform secondary auth, generate sig request, then load up Duo
				* javascript and iframe.
				*/
				window.sig_request = data.sig_request;
				window.host = data.host;

				try {
					show2FAModal();
				} catch(dfa_ex) {
					console.log(dfa_ex);
				}
			} else {

				for(let k in data.errors) {
					$("#login_alerts").append(
						'<div id="error_alert" class="alert alert-danger alert-dismissible" role="alert">' +
						'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
						data.errors[k] +
						'</div>'
					);
				}
			}
		} else {
			window.username = data.username;
			window.uid = data.uid;
			window.twoFAresponse = null;

			showMainPage();
		}

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
