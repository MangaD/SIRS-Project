'use strict';

function activateTab(tab) {
	$('.nav-link').addClass('disabled');
	$('.nav-tabs a[href="#' + tab + '"]').removeClass('disabled');
	$('.nav-tabs a[href="#' + tab + '"]').tab('show');
}

function generateConfigPre() {
	$('#config-file').html('/* Database configuration */<br />' +
	'define(\'DB_SERVER\', \'' +	$('input[name=server]').val() + '\');<br />' +
	'define(\'DB_USERNAME\', \'' + $('input[name=user]').val() + '\');<br />' +
	'define(\'DB_PASSWORD\', \'' + $('input[name=pwd]').val() + '\');<br />' +
	'define(\'DB_NAME\', \'' + $('input[name=db]').val() + '\');<br />' +

	'<br />/* Duo configuration */<br />' +
	'/*<br />' +
	' * Your akey is a random string that you generate and keep secret from Duo. It<br />' +
	' * should be at least 40 characters long and stored alongside your Web SDK<br />' +
	' * application\'s integration key (ikey) and secret key (skey) in a configuration file.<br />' +
	' */<br />' +
	'define(\'AKEY\', \'' + $('#akey-group input').val() + '\');<br />' +
	'/*<br />' +
	' * IKEY, SKEY, and HOST should come from the Duo Security admin dashboard<br />' +
	' * on the integrations page. For security reasons, these keys are best stored<br />' +
	' * outside of the webroot in a production implementation.<br />' +
	' */<br />' +
	'define(\'IKEY\', \'' + $('#ikey-group input').val() + '\');<br />' +
	'define(\'SKEY\', \'' + $('#skey-group input').val() + '\');<br />' +
	'define(\'HOST\', \'' + $('#host-group input').val() + '\');<br />');
}

// https://stackoverflow.com/questions/1349404/generate-random-string-characters-in-javascript
function generateRandomString(length) {
	var result           = '';
	var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	var charactersLength = characters.length;
	for ( var i = 0; i < length; i++ ) {
		result += characters.charAt(Math.floor(Math.random() * charactersLength));
	}
	return result;
}

$("#generateAKEYBtn").click(() => {
	$('input[name=akey]').val(generateRandomString(64));
	generateConfigPre();
});

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

				const typeHandler = function(e) {
					$result.innerHTML = e.target.value;
				}

				// https://stackoverflow.com/questions/574941/best-way-to-track-onchange-as-you-type-in-input-type-text/26202266#26202266
				$('input[name=akey]').on('input', generateConfigPre);
				$('input[name=ikey]').on('input', generateConfigPre);
				$('input[name=skey]').on('input', generateConfigPre);
				$('input[name=host]').on('input', generateConfigPre);

				generateConfigPre('','','','');

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
			'user'   : $('input[name=user]').val(),
			'pwd'    : $('input[name=pwd]').val(),
			'db'     : $('input[name=db]').val(),
			'akey'   : $('input[name=akey]').val(),
			'ikey'   : $('input[name=ikey]').val(),
			'skey'   : $('input[name=skey]').val(),
			'host'   : $('input[name=host]').val(),
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

