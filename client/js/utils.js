"use strict";

/**
 * Server POST request
 */
function postData(url, data) {
	return fetch(`${window.serverAddress}/${url}`, {
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
	let d = new Date();
	d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	let expires = "expires="+d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
function getCookie(cname) {
	let name = cname + "=";
	let ca = document.cookie.split(';');
	for(let i = 0; i < ca.length; i++) {
		let c = ca[i];
		while (c.charAt(0) === ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) === 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
}
/**
 *
 * Note that this code has two limitations:
 *
 * - It will not delete cookies with HttpOnly flag set, as the HttpOnly
 * flag disables Javascript's access to the cookie.
 * - It will not delete cookies that have been set with a Path value. (This is despite
 * the fact that those cookies will appear in document.cookie,
 * but you can't delete it without specifying the same Path value with which it was set.)
 *
 * https://stackoverflow.com/questions/179355/clearing-all-cookies-with-javascript
 */
function deleteAllCookies() {

	if (document.cookie === "") return;

	let cookies = document.cookie.split(";");

	for (let i = 0; i < cookies.length; i++) {
		let cookie = cookies[i];
		let eqPos = cookie.indexOf("=");
		let name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
		// specify path
		// https://www.w3schools.com/js/js_cookies.asp
		document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
	}
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

