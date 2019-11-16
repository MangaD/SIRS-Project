<?php

require_once 'config.php';

function isInstalled() {

	if(!defined('DB_SERVER') || !defined('DB_NAME') || !defined('DB_USERNAME')
	   || !defined('DB_PASSWORD')) {
		return false;
	}
	else return true;
}

// https://stackoverflow.com/questions/8719276/cors-with-php-headers
// https://www.moxio.com/blog/12/how-to-make-a-cross-domain-request-in-javascript-using-cors
function cors() {
	if (isset($_SERVER['HTTP_ORIGIN'])) {
		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Max-Age: 3600");
	}
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
			header("Access-Control-Allow-Methods: POST");
		}
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
			header("Access-Control-Allow-Headers: Accept, Content-Type, User-Agent");
		}
	}
	header("Content-Type: application/json; charset=UTF-8");
}

// https://stackoverflow.com/questions/15699101/get-the-client-ip-address-using-php
function getUserIP() {

	$ip = getenv('HTTP_CLIENT_IP')?:
		getenv('HTTP_X_FORWARDED_FOR')?:
		getenv('HTTP_X_FORWARDED')?:
		getenv('HTTP_FORWARDED_FOR')?:
		getenv('HTTP_FORWARDED')?:
		getenv('REMOTE_ADDR');

	return $ip;
}

function isIPv4($ip) {

	if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
		return true;
	} else {
		return false;
	}
}

function isIPv6($ip) {

	if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
		return true;
	} else {
		return false;
	}
}

// http://blog.teamtreehouse.com/how-to-create-bulletproof-sessions
class SessionManager
{

	static function sessionStart($domain = null, $secure = true) {

		ini_set('session.use_strict_mode', 1);

		// Set the cookie name before we start.
		session_name('sirs-session');

		// Set the domain to default to the current domain.
		if (!isset($domain) && isset($_SERVER['SERVER_NAME'])) {
			$domain = $_SERVER['SERVER_NAME'];
		}

		// Set the default secure value to whether the site is being accessed with SSL
		// https://stackoverflow.com/questions/7304182/detecting-ssl-with-php/7304239
		if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
			$secure = false;
		}

		// Set the cookie settings and start the session
		session_set_cookie_params(0, '/', $domain, $secure, true);
		session_start();

		// Make sure the session hasn't expired, and destroy it if it has
		if(self::validateSession()) {
			// Check to see if the session is new or a hijacking attempt
			if(!self::preventHijacking()) {
				// Reset session data and regenerate id
				$_SESSION = array();
				$_SESSION['IPaddress'] = $_SERVER['REMOTE_ADDR'];
				$_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
				$_SESSION['HTTP_ORIGIN'] = $_SERVER['HTTP_ORIGIN'];
				self::regenerateSession();
			}
			/**
			 * "The solution to this is to change the session ID. How often you
			 * do this is tends to be a matter of great debate, but at the bare
			 * minimum the ID should be changed when new sessions are created
			 * and when the user changes privileges (logs in or out)."
			 */
			// Give a 5% chance of the session id changing on any request
			elseif(rand(1, 100) <= 5) {
				self::regenerateSession();
			}
		}
		else {
			$_SESSION = array();
			session_destroy();
			session_start();
		}

	}

	/**
	 * Check if user is logged in.
	 *
	 * Note: It also checks if the origin that logged in is the same as the one
	 *       performing the request in order to avoid hackers hijacking the user
	 *       session.
	 */
	static function isLoggedIn() {

		self::sessionStart();

		if(!isset($_SESSION['username']) || empty($_SESSION['username']) ||
			!isset($_SESSION['uid']) || empty($_SESSION['uid'])) {
			return false;
		}

		return true;
	}

	static function logout() {
		self::regenerateSession();
		$_SESSION = array();
		session_destroy();
	}

	static protected function preventHijacking() {

		if(!isset($_SESSION['IPaddress']) || !isset($_SESSION['userAgent'])
			|| !isset($_SESSION['HTTP_ORIGIN']))
			return false;

		if ($_SESSION['IPaddress'] != $_SERVER['REMOTE_ADDR'])
			return false;

		if( $_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT'])
			return false;

		if( $_SESSION['HTTP_ORIGIN'] != $_SERVER['HTTP_ORIGIN'])
			return false;

		return true;
	}

	static function regenerateSession() {

		// If this session is obsolete it means there already is a new id
		if(isset($_SESSION['OBSOLETE']) && $_SESSION['OBSOLETE'] == true)
			return;

		// Set current session to expire in 10 seconds
		$_SESSION['OBSOLETE'] = true;
		$_SESSION['EXPIRES'] = time() + 10;

		// Create new session without destroying the old one
		session_regenerate_id(false);

		// Grab current session ID and close both sessions to allow other scripts to use them
		$newSession = session_id();
		session_write_close();

		// Set session ID to the new one, and start it back up again
		session_id($newSession);
		session_start();

		// Now we unset the obsolete and expiration values for the session we want to keep
		unset($_SESSION['OBSOLETE']);
		unset($_SESSION['EXPIRES']);
	}

	static protected function validateSession() {

		if( isset($_SESSION['OBSOLETE']) && !isset($_SESSION['EXPIRES']) )
			return false;

		if(isset($_SESSION['EXPIRES']) && $_SESSION['EXPIRES'] < time())
			return false;

		return true;
	}
}

?>

