"use strict";

var ws;

class Smartphone {

	static connectToSmartphone() {
		ws = new WebSocket(`ws://${window.smartphoneAddress}:${window.smartphonePort}`);

		ws.onopen = function() {
			console.log('Connected to smartphone.');
		};

		ws.onmessage = function(evt) {

			loaderEnd();
			
			let response = JSON.parse(evt.data);

			/**
			 * Login on smartphone response.
			 * 
			 * JSON parameters:
			 * success - boolean, if login was successful
			 * message - string, for eventual errors or success message
			 * do - what to do after login  (eg. server login, server register...)
			 */
			if (response.action === "login") {

				cleanLoginErrors();
				cleanRegisterErrors();

				if (response.success) {

					// DH exchange
					if (window.use_custom_secure_channel) {

						loaderStart();

						postJSONData("DHExchange.php", {
							request: ''
						})
						.then((data) => {
							if (!data.success) {
								if (response.do === "login") {
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
					} else {
						// Plaintext server request
						if (response.do === "login") {
							login();
						} else if (response.do === "register") {
							register();
						}
					}

				} else {
					showLoginErrors(['Smartphone: ' + response.message]);
				}

			/**
			 * DH smartphone response.
			 * 
			 * JSON parameters:
			 * success - boolean, if DH was successful
			 * message - string, for eventual errors or success message
			 * do - what to do after DH (eg. server login, server register...)
			 */
			} else if (response.action === "dh") {
				if (response.success) {

					loaderStart();

					postJSONData("DHExchange.php", {
						request: response.pubKeyPEM
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
									message: generateRegisterRequestString()
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
				} else {
					if (response.do === "login") {
						showLoginErrors(['Smartphone DH: ' + response.message]);
					} else if (response.do === "register") {
						showRegisterErrors(['Smartphone DH: ' + response.message]);
					}
				}

			/**
			 * Encrypt smartphone response.
			 * 
			 * JSON parameters:
			 * success - boolean, if login was successful
			 * message - string, for eventual errors or success message
			 * ciphertext - base64 string with encrypted message
			 * do - what to do after encrypt  (eg. server login, server register...)
			 */
			} else if (response.action === "encrypt") {
				if (response.success) {
					if (response.do) {
						if (response.do === "login") {
							login({ciphertext: response.ciphertext});
						} else if (response.do === "register") {
							register({ciphertext: response.ciphertext});
						}
					}
				} else {
					if (response.do) {
						if (response.do === "login") {
							showLoginErrors(['Smartphone AES: ' + response.message]);
						} else if (response.do === "register") {
							showRegisterErrors(['Smartphone AES: ' + response.message]);
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