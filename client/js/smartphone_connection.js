"use strict";

var ws;

function connectToSmartphone() {
	ws = new WebSocket('ws://' + window.smartphoneAddress + ':' + window.smartphonePort);

	ws.onopen = function() {
		alert('Connected.');
	};

	ws.onmessage = function(evt) {
		alert(evt.data);
	};

	ws.onclose = function() {
		alert('Connection is closed...');
	};
	ws.onerror = function(e) {
		alert(e.msg);
	};
}

function closeConnection() {
	ws.close();
}

function sendRequest(jsonData) {
	/*var  req = {
		arg1: req_1st_arg,
		arg2: req_2nd_arg
	};*/

	ws.send(JSON.stringify(jsonData));
}