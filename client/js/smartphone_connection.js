"use strict";

var ws;

class Smartphone {

	static connectToSmartphone() {
		ws = new WebSocket('ws://' + window.smartphoneAddress + ':' + window.smartphonePort);

		ws.onopen = function() {
			console.log('Connected to smartphone.');
		};

		ws.onmessage = function(evt) {

			loaderEnd();
			
			let response = JSON.parse(evt.data);

			if (response.action === "login") {

				cleanLoginErrors();

				if (response.success) {

					// DH exchange
					if (window.use_custom_secure_channel) {

						loaderStart();

						postJSONData("DHExchange.php", {
							request: ''
						})
						.then((data) => {
							if (!data.success) {
								showLoginErrors(data.errors);
							} else {
								Smartphone.sendRequest({
									action: "dh",
									p: data.pBase64,
									g: data.gBase64,
									l: data.l,
									key: data.key,
									do: "login"
								});
							}

							loaderEnd();
						})
						.catch((error2) => {
							showLoginErrors([error2]);
							console.log(error2);
							loaderEnd();
						});
					} else {
						// Server login
						login();
					}

				} else {
					showLoginErrors(['Smartphone: ' + response.message]);
				}
			} else if (response.action === "dh") {
				if (response.success) {

					loaderStart();

					postJSONData("DHExchange.php", {
						request: response.pubKeyPEM
					})
					.then((data) => {
						if (!data.success) {
							showLoginErrors(data.errors);
						} else {
							if (response.do === "login") {
								Smartphone.sendRequest({
									action: "encrypt",
									do: "login",
									message: '{"username": "' + document.getElementById("log_username").value +
									'", "password": "' + document.getElementById("log_password").value +
									'", "twoFAresponse": "' + (window.twoFAresponse ? window.twoFAresponse : "") + '"}'
								});
							}
						}

						loaderEnd();
					})
					.catch((error2) => {
						showLoginErrors([error2]);
						loaderEnd();
					});
				} else {
					showLoginErrors(['Smartphone DH: ' + response.message]);
				}
			} else if (response.action === "encrypt") {
				if (response.success) {
					if (response.do) {
						if (response.do === "login") {
							login({ciphertext: response.ciphertext});
						}
					}
				} else {
					if (response.do) {
						if (response.do === "login") {
							showLoginErrors(['Smartphone AES: ' + response.message]);
						} else {
							alert('Smartphone AES: ' + response.message);
						}
					} else {
						alert('Smartphone AES: ' + response.message);
					}
				}
			}
		};

		ws.onclose = function() {
			ws = null;
			logout();
			console.log('Connection to smartphone is closed...');
			loaderEnd();
		};
		ws.onerror = function(e) {
			alert("Smartphone error: " + (e.msg ? e.msg : "Couldn't connect to smartphone."));
			logout();
			loaderEnd();
		};
	}

	static closeConnection() {
		ws.close();
		ws = null;
	}

	static sendRequest(jsonData) {
		loaderStart();
		if (!ws) {
			this.connectToSmartphone();
		}
		Smartphone.waitForSocketConnection(ws, function() {
			ws.send(JSON.stringify(jsonData));
		});
	}

	// Make the function wait until the connection is made...
	// https://stackoverflow.com/questions/13546424/how-to-wait-for-a-websockets-readystate-to-change
	static waitForSocketConnection(socket, callback) {
		setTimeout(
			function () {
				if (socket.readyState === 1) {
					if (callback != null) {
						callback();
					}
				} else {
					Smartphone.waitForSocketConnection(socket, callback);
				}
			}, 50); // wait 50 milliseconds for the connection...
	}
}