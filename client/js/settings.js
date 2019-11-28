"use strict";

$("#saveSettingsBtn").click( function(e) {

	saveSettings();

	e.preventDefault();
});

$('#settingsModal').on('hidden.bs.modal', function () {
	loadSettings();
});

$("#clearCookiesBtn").click( function(e) {
	deleteAllCookies();
	loadSettings();
	alert("Cookies cleared!");
	e.preventDefault();
});

function saveSettings() {
	window.serverAddress = $("#serverAddress").val();
	setCookie("serverAddress", $("#serverAddress").val(), 365*5);

	let secureCheckbox = document.getElementById("toggleSecureChannel");
	window.use_custom_secure_channel = secureCheckbox.checked;
	setCookie("toggleSecureChannel", secureCheckbox.checked, 365*5);

	window.smartphoneAddress = $("#smartphoneAddress").val();
	setCookie("smartphoneAddress", $("#smartphoneAddress").val(), 365*5);
	
	window.smartphonePort = $("#smartphonePort").val();
	setCookie("smartphonePort", $("#smartphonePort").val(), 365*5);

	$('#settingsModal').modal('hide');
}

function loadSettings() {
	// Set server url
	window.serverAddress = (getCookie("serverAddress") ? getCookie("serverAddress") : "../server");
	$("#serverAddress").val(window.serverAddress);

	// User custom secure channel
	let secureCheckbox = document.getElementById("toggleSecureChannel");
	window.use_custom_secure_channel = (getCookie("toggleSecureChannel") == "true" ? 
		true : false);
	secureCheckbox.checked = window.use_custom_secure_channel;

	// Set smartphone url
	window.smartphoneAddress = (getCookie("smartphoneAddress") ? getCookie("smartphoneAddress") : "127.0.0.1");
	$("#smartphoneAddress").val(window.smartphoneAddress);

	// Set smartphone port
	window.smartphonePort = (getCookie("smartphonePort") ? getCookie("smartphonePort") : "4444");
	$("#smartphonePort").val(window.smartphonePort);
}