window.app_server = "../server";
window.app_title = "Smartphone as a security token";

if (isLoggedIn()) {
	// TODO switch to downloads page
} else {
	document.title = "Login | " + window.app_title;
	$.get("html/login.html", function(data){
		$("#main_container").html(data);
	});
}
