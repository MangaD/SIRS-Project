"use strict";

function loadFiles() {

	loaderStart();

	postJSONData("files.php", {})
	.then(data => {
		if (!data.success) {
			for (let k in data.errors) {
				alert(data.errors[k]);
			}
		} else {
			let files = data.list;

			let table_html;

			for (let index = 0, len = files.length; index < len; ++index) {
				table_html += '<tr>';
				table_html += '<td>' + files[index].name + '</td>';
				table_html += '<td>' + files[index].username + '</td>';
				table_html += '<td>' + files[index].hash + '</td>';
				table_html += '<td>' + files[index].created_at + '</td>';
				table_html += '<td ' +
					' style="color: #cfc;font-size: x-large;text-shadow: 0px 4px 0px #000;">' +
					'<i data-hash="' + files[index].hash + '" class="fas fa-download" ' +
					'style="cursor:pointer;" onclick="fileDownload(this);"></i></td>';

				if (window.username === files[index].username) {
					table_html += '<td style="color: #f77;font-size: x-large;text-shadow: 0px 4px 0px #000;">' +
						'<i data-hash="' + files[index].hash + '" class="fas fa-trash-alt" ' +
						'style="cursor:pointer;" ></i></td>';
				} else {
					table_html += '<td>&nbsp;</td>';
				}
				table_html += '</tr>';
			}

			if (files.length === 0) {
				table_html += '<td colspan="6" style="text-align:center;">No files in the server.</td>';
			}

			$('#filesTable > tbody').html(table_html);

		}

		loaderEnd();
	});
}

function fileDownload(el){
	let hash = el.getAttribute('data-hash');
	console.log(hash);

	postJSONData("filesDownload.php", {
		hash: hash,
	})
	.then(data => {
		if (!data.success) {
			console.log("Erro");
		} else {
			console.log("Aqui");
			console.log(data);
		}
	})
}
