function openConnection() {
	websocket = new WebSocket("ws://127.0.0.1:4444");
	websocket.onmessage = function (event) {
		alert(event.data);
	};
}

function closeConnection() {
	websocket.close();
}

function sendMessage(request) {
	websocket.send(request);
}

