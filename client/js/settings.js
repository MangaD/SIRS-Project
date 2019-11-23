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
	window.app_server = $("#serverAddress").val();
	setCookie("serverAddress", $("#serverAddress").val(), 365*5);
	//console.log(window.app_server);

	let secureCheckbox = document.getElementById("toggleSecureChannel");
	window.use_custom_secure_channel = secureCheckbox.checked;
	setCookie("toggleSecureChannel", secureCheckbox.checked, 365*5);

	$('#settingsModal').modal('hide');
}

function loadSettings() {
	// Set server url
	window.app_server = (getCookie("serverAddress") ? getCookie("serverAddress") : "../server");
	$("#serverAddress").val(window.app_server);

	// User custom secure channel
	let secureCheckbox = document.getElementById("toggleSecureChannel");
	window.use_custom_secure_channel = (getCookie("toggleSecureChannel") == "true" ? 
		true : false);
	secureCheckbox.checked = window.use_custom_secure_channel;

}