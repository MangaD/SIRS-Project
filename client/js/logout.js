"use strict";

function logout() {

	loaderStart();

	$("#login_alerts").html('');

	postJSONData("logout.php", {})
	.then((data) => {
		if (!data.success) {
			Object.keys(data.errors).forEach((k) => {
				console.log("Error: " + data.errors[k]);
				if (data.errors[k].includes("You are not logged in!")) {
					showLoginPage();
				}
			});
		} else {
			//console.log("Success: " + data.message);
			showLoginPage();
		}
		loaderEnd();
	})
	.catch((error) => {
		console.log("Error: " + error);
		if (error.includes("You are not logged in!")) {
			showLoginPage();
		}
		loaderEnd();
	});

}

