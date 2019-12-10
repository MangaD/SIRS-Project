"use strict";

// Load page's html into variables
let login_html;
let register_html;
let main_html;

$(document).ready( function() {

	loadSettings();

	// Set app title
	window.app_title = "Smartphone as a security token";

	//https://stackoverflow.com/questions/33342595/preloader-wont-ignore-websocket-pace-js
	Pace.options.ajax.trackWebSockets = false;

	// Empty main container
	$("#main_container").html('');

	// Load login html
	$.get("html/login.html", function(data) {
		login_html = data;

		// Load register html
		$.get("html/register.html", function(data2) {
			register_html = data2;
		
			// Load main page html
			$.get("html/main.html", function(data3) {
				main_html = data3;

				// Load login page if user not logged in
				// else load main page
				Smartphone.sendRequest({
					action: "login",
					password: window.smartphonePassword,
					do: "login"
				});

				// Enable tooltips
				$('[data-toggle="tooltip"]').tooltip();
			});
		});
	});


});

function showRegisterPage() {
	document.title = "Register | " + window.app_title;
	$("#main_container").html(register_html);
}

function showLoginPage() {
	document.title = "Login | " + window.app_title;
	$("#main_container").html(login_html);
}

function showMainPage() {
	document.title = window.app_title;
	$("#main_container").html(main_html);
	loadFiles();
}

function show2FAModal() {

	$('#duo_iframe').src = "";

	// https://stackoverflow.com/questions/48109090/unable-to-integrate-duo-web-sdk-with-angular-application
	Duo.init({
		iframe: "duo_iframe",
		host: window.host,
		sig_request: window.sig_request,
		submit_callback: twoFactorVerify.bind(this),
	});

	$('#duoModal').modal('show');
}

function twoFactorVerify(response) {
	$('#duoModal').modal('hide');
	window.twoFAresponse = response.elements.sig_response.value
	if (window.use_custom_secure_channel) {
		Smartphone.sendRequest({
			action: "encrypt",
			do: "login",
			message: generateLoginRequestString()
		});
	} else {
		login();
	}
}

function isMainContainerEmpty() {
	return !$.trim($("#main_container").html());
}