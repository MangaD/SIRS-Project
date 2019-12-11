"use strict";

function postDHRequest(response) {

	loaderStart();

	postJSONData("DHExchange.php", {
		request: '',
	})
	.then((data) => {

		if (!data.success) {
			if (response.do === "login") {
				showLoginPage();
				showLoginErrors(data.errors);
			} else if (response.do === "register") {
				showRegisterErrors(data.errors);
			}
		} else {
			Smartphone.sendRequest({
				action: "dh",
				p: data.pBase64,
				g: data.gBase64,
				l: data.l,
				key: data.key,
				signedKey: data.signedKey,
				pubKeyRSA: data.pubKeyRSA,
				do: response.do
			});
		}

		loaderEnd();
	})
	.catch((error2) => {
		if (response.do === "login") {
			showLoginErrors([error2]);
		} else if (response.do === "register") {
			showRegisterErrors([error2]);
		}
		console.log(error2);
		loaderEnd();
	});
}

function postDHResponse(response) {

	loaderStart();

	postJSONData("DHExchange.php", {
		request: response.pubKeyPEM,
		signedPubKeyPEM: response.signedPubKeyPEM,
		username: (document.getElementById("log_username") ? document.getElementById("log_username").value : "")
	})
	.then((data) => {
		if (!data.success) {
			if (response.do === "login") {
				showLoginErrors(data.errors);
			} else if (response.do === "register") {
				showRegisterErrors(data.errors);
			}
		} else {
			if (response.do === "login") {
				Smartphone.sendRequest({
					action: "encrypt",
					do: "login",
					message: generateLoginRequestString()
				});
			} else if (response.do === "register") {
				Smartphone.sendRequest({
					action: "encrypt",
					do: "register",
					message: generateRegisterRequestString(response.pubKeyRSA_PEM)
				});
			}
		}

		loaderEnd();
	})
	.catch((error2) => {
		if (response.do === "login") {
			showLoginErrors([error2]);
		} else if (response.do === "register") {
			showRegisterErrors([error2]);
		}
		loaderEnd();
	});
}