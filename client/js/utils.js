
/**
 * Server POST request
 */
function postData(url, data) {
	return fetch(`${window.app_server}/${url}`, {
		body: JSON.stringify(data),
		cache: 'no-cache',
		credentials: 'include',
		headers: {
			'User-Agent': window.app_title,
			'Accept': 'application/json',
			'Content-Type': 'application/json; charset=utf-8',
		},
		method: 'POST',
		mode: 'cors',
		referrer: 'no-referrer',
	})
	.then(response => response.json())
}


/**
 * Settings utilities start
 */
function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	var expires = "expires="+d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) === ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) === 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
}
function loaderStart() {
	try {
		document.getElementById("loader").style.display = "block";
	} catch(e) {}
}
function loaderEnd() {
	try {
		document.getElementById("loader").style.display = "none";
	} catch(e) {}
}

