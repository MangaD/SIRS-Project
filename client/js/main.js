// Load page's html into variables
var login_html;
var register_html;

$(document).ready( function() {

	// Set server url
	window.app_server = "../server";
	// Set app title
	window.app_title = "Smartphone as a security token";

	// Load login html
	$.get("html/login.html", function(data) {
		login_html = data;

		// Load register html
		$.get("html/register.html", function(data) {
			register_html = data;

			// Load login page if user not logged in
			if (isLoggedIn()) {
				// TODO switch to downloads page
			} else {
				showLoginPage();
			}

			// Enable tooltips
			$('[data-toggle="tooltip"]').tooltip();
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



