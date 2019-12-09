"use strict";

function login(ciphertext) {

	loaderStart();

	cleanLoginErrors()
	
	// send as plaintext
	if (!ciphertext) {
		ciphertext = {
			username: document.getElementById("log_username").value,
			password: document.getElementById("log_password").value,
			twoFAresponse: (window.twoFAresponse ? window.twoFAresponse : ""),
		}
	}

	// Login to server
	postJSONData("login.php", ciphertext)
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
				showLoginErrors(data.errors);
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
		showLoginErrors([error2]);

		console.log(error2);

		loaderEnd();
	});
}

function showLoginErrors(errors) {
	for(let k in errors) {
		$("#login_alerts").append(
			'<div id="error_alert" class="alert alert-danger alert-dismissible" role="alert">' +
			'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
			errors[k] +
			'</div>'
		);
	}
}

function cleanLoginErrors() {
	$("#login_alerts").html('');
}