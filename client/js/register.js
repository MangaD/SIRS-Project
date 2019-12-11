"use strict";

function register() {
	Smartphone.sendRequest({
		action: "login",
		password: window.smartphonePassword,
		do: "register"
	});
}

function postRegister(ciphertext) {

	cleanRegisterErrors();

	// send as plaintext if no cipher provided
	if (!ciphertext) {
		alert("'postRegister' requires either ciphertext or object (with RSA key).");
		return;
	}

	loaderStart();

	postJSONData("register.php", ciphertext)
	.then((data) => {
		if (data.hasOwnProperty('ciphertext')) {
			Smartphone.sendRequest({
				action: "decrypt",
				do: "register",
				message: data.ciphertext
			});
		} else {
			serverResponseRegister(data);
		}
	})
	.catch((error2) => {
		showRegisterErrors([error2]);
		console.log(error2);
		loaderEnd();
	});
}

function serverResponseRegister(data) {
	if (!data.success) {
		if (data.errors.already_logged === true) {
			window.username = data.username;
			window.uid = data.uid;

			//alert("Already logged in!");
			showMainPage();
		} else {
			showRegisterErrors(data.errors);
		}
	} else {
		alert("Registration successful!");
		showLoginPage();
	}

	//console.log(data);
	loaderEnd();
}

function showRegisterErrors(errors) {
	for(let k in errors) {
		$("#register_alerts").append(
			'<div id="error_alert" class="alert alert-danger alert-dismissible" role="alert">' +
			'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
			errors[k] +
			'</div>'
		);
	}
}

function cleanRegisterErrors() {
	$("#register_alerts").html('');
}

function generateRegisterRequestObject(pubKeyRSA_PEM) {
	let usernameVal = (document.getElementById("reg_username") ? document.getElementById("reg_username").value : "");
	let passwordVal = (document.getElementById("reg_password") ? document.getElementById("reg_password").value : "");
	let confirm_passwordVal = (document.getElementById("reg_confirm_password") ? document.getElementById("reg_confirm_password").value : "");
	return {
		username: usernameVal,
		password: passwordVal,
		confirm_password: confirm_passwordVal,
		pubKeyRSA_PEM: pubKeyRSA_PEM
	};
}

function generateRegisterRequestString(pubKeyRSA_PEM) {
	return JSON.stringify(generateRegisterRequestObject(pubKeyRSA_PEM));
}