'use strict';

function activateTab(tab) {
	$('.nav-link').addClass('disabled');
	$('.nav-tabs a[href="#' + tab + '"]').removeClass('disabled');
	$('.nav-tabs a[href="#' + tab + '"]').tab('show');
}

$(document).ready(function() {

	// Initialize page stuff
	$(function() {
		$('[data-toggle="tooltip"]').tooltip();
	});

	// Create tables
	$('#remove-db').click(function(event) {

		$.ajax({
			type: 'POST',
			url: 'remove_db.php',
			data: '',
			dataType: 'json',
			encode: true
		}).done(function(data) {
			$('#remove-db + .alert-danger').remove();
			$('#remove-db + .alert-success').remove();
			if (!data.success) {
				let message = '';
				Object.keys(data.errors).forEach(function(k) {
					message += data.errors[k] + '<br />';
				});
				$('#database').append('<div class="alert alert-danger">' +
					message + '</div>');
			} else {
				$('#database').append('<div class="alert alert-success">' +
					data.message + '</div>');
				activateTab('config');
			}
		});
	});

	// Create configuration
	$('#remove-config').click(function(event) {
		$.ajax({
			type: 'POST',
			url: 'remove_config.php',
			data: '',
			dataType: 'json',
			encode: true
		}).done(function(data) {
			$('#remove-config + .alert').remove();
			if (!data.success) {
				let message = '';
				Object.keys(data.errors).forEach(function(k) {
					message += data.errors[k] + '<br />';
				});
				$('#config').append('<div class="alert alert-danger">' +
					message + '</div>');
			} else {
				$('#config').append('<div class="alert alert-success">' +
					data.message + '</div>');
				activateTab('done');
			}
		});
	});

});
