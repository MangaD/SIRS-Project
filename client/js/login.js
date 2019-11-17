$("#login_btn").click( function(e) {
	login();
	e.preventDefault();
});

function isLoggedIn() {
	postData("login.php", {})
	.then((data) => {
		if (!data.success) {
			if (data.errors.already_logged === true) {
				return true;
			} else { return false; }
		} else { return false; }
	})
	.catch((error2) => {
		return false;
	});
}

function login() {

	loaderStart();

	postData("login.php", {
		username: document.getElementById("username").value,
		password: document.getElementById("password").value,
	})
	.then((data) => {
		if (!data.success) {
			if (data.errors.already_logged === true) {
				window.username = data.username;
				window.uid = data.uid;

				alert("Login successful!");

				// TODO Switch to main window
			} else {

				for(var k in data.errors) {
					$("#login_alerts").append(
						'<div id="error_alert" class="alert alert-danger alert-dismissible" role="alert">' +
						'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
						data.errors[k] +
						'</div>'
					);
				}
			}
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

function validateUser() {

	var username = document.getElementById("username").value;

	var filter = /^([a-zA-Z0-9-_]{6,12})$/;

	if ((username === "") || !filter.test(username)) {
		alert('Please provide a valid user');
		return false;
	}
	return true;
}

function validatePhone(theForm) {
	return true;
}


function validatePassword(theForm) {

	var password = document.getElementById("password").value;

	var filter = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{5,}$/;

	if ((theForm.password.value === "" && theForm.password1.value === "") || theForm.password.value !== theForm.password1.value || (!filter.test(theForm.password.value) && !filter.test(theForm.password1.value))) {
		alert('Please provide a valid password');
		theForm.password.focus();
		return false;
	}
}

