"use strict";

// Load page's html into variables
let login_html;
let register_html;
let main_html;
let twofa_html;

$(document).ready( function() {

	loadSettings();

	// Set app title
	window.app_title = "Smartphone as a security token";

	// Load login html
	$.get("html/login.html", function(data) {
		login_html = data;

		// Load register html
		$.get("html/register.html", function(data2) {
			register_html = data2;

			//Load 2fa page html
			$.get("html/2fa.html", function(data4) {
				twofa_html = data4;

				// Load main page html
				$.get("html/main.html", function(data3) {
					main_html = data3;

					// Load login page if user not logged in
					// else load main page
					postData("login.php", {})
					.then((data) => {
						if (!data.success) {
							if (data.errors.already_logged === true) {
								window.username = data.username;
								window.uid = data.uid;
								
								
								/*
         						* Perform secondary auth, generate sig request, then load up Duo
         						* javascript and iframe.
         						*/
			 					window.sig_request = data.sig_request;
				
								//alert(data.sig_request);

			 					//show2faPage(window.sig_request);
								
								
								showMainPage();


							} else { showLoginPage(); }
						} else { showLoginPage(); }
					})
					.catch((error2) => {
						showLoginPage();
					});

					// Enable tooltips
					$('[data-toggle="tooltip"]').tooltip();
				});
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

function show2faPage($sig_request) {
	document.title = "2FA | " + window.app_title;

	//document.getElementById("duo_iframe").setAttribute("data-host", "api-0cbbf77f.duosecurity.com");
	//document.getElementById("duo_iframe").setAttribute("data-sig-request", window.sig_request);
	$("#main_container").html(twofa_html);
	

	//alert($sig_request);
	
	
}