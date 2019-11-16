document.getElementById("login_btn").addEventListener("click", function(event) {
	login();
	event.preventDefault();
});

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
				// TODO Switch to main window
			} else {
				// TODO Set error alert
			}
		}

		loaderEnd();
	})
	.catch((error2) => {
		console.log(error2);
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

