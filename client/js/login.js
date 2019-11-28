"use strict";

function login() {

	loaderStart();

	$("#login_alerts").html('');
	
	postData("login.php", {
		username: document.getElementById("log_username").value,
		password: document.getElementById("log_password").value,
	})
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
				
				//alert(window.sig_request);
				show2FAModal();

				alert("Already logged in!");
				//showMainPage();
			} else {

				for(let k in data.errors) {
					$("#login_alerts").append(
						'<div id="error_alert" class="alert alert-danger alert-dismissible" role="alert">' +
						'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
						data.errors[k] +
						'</div>'
					);
				}
			}
		} else {
			window.username = data.username;
			window.uid = data.uid;
			
			/*
         	* Perform secondary auth, generate sig request, then load up Duo
         	* javascript and iframe.
         	*/
			window.sig_request = data.sig_request;
				
			//alert(window.sig_request);
			show2FAModal();

			//showMainPage();
			//alert("Log in successful!");
			
		}

		//console.log(data);
		loaderEnd();
	})
	.catch((error2) => {
		$("#login_alerts").append(
			'<div id="error_alert" class="alert alert-danger alert-dismissible" role="alert">' +
			'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
			'Failed to connect to the server.' +
			'</div>'
		);
		//console.log(error2);
		loaderEnd();
	});
}


