$("#saveSettingsBtn").click( function(e) {

	saveSettings();

	e.preventDefault();
});

function saveSettings() {
	window.app_server = $("#serverAddress").val();
	//console.log(window.app_server);

	$('#settingsModal').modal('hide');
}
