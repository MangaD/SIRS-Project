"use strict";

var ws;
var smartphonePingID;

class Smartphone {

	static connectToSmartphone() {
		ws = new WebSocket(`ws://${window.smartphoneAddress}:${window.smartphonePort}`);

		ws.onopen = function() {
			console.log('Connected to smartphone.');
			smartphonePingID = window.setInterval(function() {
				Smartphone.sendRequest({
					action: "ping"
				}, false);
			}, 5000);
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
						postDHRequest(response);
					} else {
						// Plaintext server request
						if (response.do === "login") {
							postLogin();
						} else if (response.do === "register") {
							postRegister(generateRegisterRequestObject(response.pubKeyRSA_PEM));
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
					postDHResponse(response);
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
			 * success - boolean, if encryption was successful
			 * message - string, for eventual errors or success message
			 * ciphertext - base64 string with encrypted message
			 * do - what to do after encrypt  (eg. server login, server register...)
			 */
			} else if (response.action === "encrypt") {
				if (response.success) {
					if (response.do) {
						if (response.do === "login") {
							postLogin({ciphertext: response.ciphertext});
						} else if (response.do === "register") {
							postRegister({ciphertext: response.ciphertext});
						} else if (response.do === "files") {
							postLoadFiles({ciphertext: response.ciphertext});
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

			/**
			 * Decrypt server response.
			 * 
			 * JSON parameters:
			 * success - boolean, if decryption was successful
			 * message - string, for eventual errors or success message
			 * plaintext - decrypted message
			 * do - what to do after decryption  (eg. list files)
			 */
			} else if (response.action === "decrypt") {
				if (response.success) {
					let data = JSON.parse(response.plaintext);
					if (response.do) {
						if (response.do === "login") {
							serverResponseLogin(data);
						} else if (response.do === "register") {
							serverResponseRegister(data);
						} else if (response.do === "filesList") {
							serverResponseLoadFiles(data);
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
			window.clearInterval(smartphonePingID);
			logout();
			console.log('Connection to smartphone is closed...');
			loaderEnd();
		};
		ws.onerror = function(e) {
			window.clearInterval(smartphonePingID);
			logout();
			alert("Smartphone error: " + (e.msg ? e.msg : "Couldn't connect to smartphone."));
			loaderEnd();
		};
	}

	static closeConnection() {
		ws.close();
		ws = null;
	}

	static sendRequest(jsonData, startLoader=true) {
		if (startLoader === true) loaderStart();
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