
function btnLogin() {

    let socket = new WebSocket('ws://127.0.0.1:4444');

    
    socket.onopen = function(e) {
        alert("[open] Connection established");
        
        socket.send("My name is John");

        alert("Sending to server");
    };

}


