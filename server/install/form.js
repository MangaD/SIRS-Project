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

	$('input').keypress(function() {
		$(this).removeClass('is-invalid');
		let el = $(this).next();
		if (el !== undefined && el.attr('class') !== undefined &&
		           el.attr('class').indexOf('form-text') >= 0) {
			el.remove();
		}
	});

	// Create tables
	$('#build_db').submit(function(event) {

		let formData = {
			'server' : $('input[name=server]').val(),
			'user' : $('input[name=user]').val(),
			'pwd' : $('input[name=pwd]').val(),
			'db' : $('input[name=db]').val()
		};
		$.ajax({
			type: 'POST',
			url: 'build_db.php',
			data: formData,
			dataType: 'json',
			encode: true
		}).done(function(data) {
			$('.form-group input').removeClass('is-invalid');
			$('#build_db + .alert').remove();
			$('.form-text').remove();

			if (!data.success) {
				if (data.errors.server) {
					$('#server-group input').addClass('is-invalid');
					$('#server-group').append('<div class="form-text text-muted">' +
						data.errors.server + '</div>');
				}
				if (data.errors.user) {
					$('#user-group input').addClass('is-invalid');
					$('#user-group').append('<div class="form-text text-muted">' +
						data.errors.user + '</div>');
				}
				if (data.errors.pwd) {
					$('#pwd-group input').addClass('is-invalid');
					$('#pwd-group').append('<div class="form-text text-muted">' +
						data.errors.pwd + '</div>');
				}
				if (data.errors.db) {
					$('#db-group input').addClass('is-invalid');
					$('#db-group').append('<div class="form-text text-muted">' +
						data.errors.db + '</div>');
				}
				if (data.errors.exception) {
					$('#build_db').append('<div class="alert alert-danger">' +
						data.errors.exception + '</div>');
				}
			} else {
				$('#build_db').append('<div class="alert alert-success">' +
					data.message + '</div>');

				$('#config-file').html('define(\'DB_SERVER\', \'' +
					$('input[name=server]').val() + '\');<br />' +
					'define(\'DB_USERNAME\', \'' +
					$('input[name=user]').val() + '\');<br />' +
					'define(\'DB_PASSWORD\', \'' +
					$('input[name=pwd]').val() + '\');<br />' +
					'define(\'DB_NAME\', \'' +
					$('input[name=db]').val() + '\');<br />');
				activateTab('config');
			}
		});/*.fail(function(data) { // Use for debug
			console.log(data);
		});*/
		event.preventDefault();
	});

	// Create configuration
	$('#create-config').click(function(event) {
		let formData = {
			'server' : $('input[name=server]').val(),
			'user' : $('input[name=user]').val(),
			'pwd' : $('input[name=pwd]').val(),
			'db' : $('input[name=db]').val()
		};
		$.ajax({
			type: 'POST',
			url: 'create_config.php',
			data: formData,
			dataType: 'json',
			encode: true
		}).done(function(data) {
			$('#create-config + .alert').remove();
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

