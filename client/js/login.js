"use strict";

function login() {
	Smartphone.sendRequest({
		action: "login",
		password: window.smartphonePassword,
		do: "login"
	});
}

function postLogin(ciphertext) {

	loaderStart();

	cleanLoginErrors();
	
	// send as plaintext if no cipher provided
	if (!ciphertext) {
		ciphertext = generateLoginRequestObject();
	}

	// Login to server
	postJSONData("login.php", ciphertext)
	.then((data) => {

		if (data.hasOwnProperty('ciphertext')) {
			Smartphone.sendRequest({
				action: "decrypt",
				do: "login",
				message: data.ciphertext
			});
		} else {
			serverResponseLogin(data);
		}

	})
	.catch((error2) => {
		// If main container is empty then this login was
		// just to check if user is already logged in
		if (isMainContainerEmpty()) {
			showLoginPage();
		} else {
			showLoginErrors([error2]);
			console.log(error2);
			loaderEnd();
		}
	});
}

function serverResponseLogin(data) {

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
			// If main container is empty then this login was
			// just to check if user is already logged in
			if (isMainContainerEmpty()) {
				showLoginPage();
			} else {
				showLoginErrors(data.errors);
			}
		}
	} else {
		window.username = data.username;
		window.uid = data.uid;
		window.twoFAresponse = null;

		showMainPage();
	}

	loaderEnd();
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

function generateLoginRequestObject() {
	let usernameVal = (document.getElementById("log_username") ? document.getElementById("log_username").value : "");
	let passwordVal = (document.getElementById("log_password") ? document.getElementById("log_password").value : "");

	return {
		username: usernameVal,
		password: passwordVal,
		twoFAresponse: (window.twoFAresponse ? window.twoFAresponse : ""),
	};
}

function generateLoginRequestString() {
	return JSON.stringify(generateLoginRequestObject());
}