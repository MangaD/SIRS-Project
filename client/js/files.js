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

				let fileSizeInt = parseInt(files[index].size);
				let fileSizeString;
				if (fileSizeInt >= 1024) {
					fileSizeString = Math.round(fileSizeInt / 1024* 100) / 100 + " KiB";
				} else if (fileSizeInt >= 1024*1024) {
					fileSizeString = Math.round(fileSizeInt / 1024 / 1024* 100) / 100 + " MiB";
				} else if (fileSizeInt >= 1024*1024*1024) {
					fileSizeString = Math.round(fileSizeInt / 1024 / 1024 / 1024* 100) / 100 + " GiB";
				}

				table_html += '<tr>';
				table_html += '<td>' + files[index].name + '</td>';
				table_html += '<td>' + files[index].username + '</td>';
				table_html += '<td>' + files[index].hash + '</td>';
				table_html += '<td>' + fileSizeString + '</td>';
				table_html += '<td>' + files[index].created_at + '</td>';
				table_html += '<td ' +
					' style="color: #cfc;font-size: x-large;text-shadow: 0px 4px 0px #000;">' +
					'<i data-name="' + files[index].name + '" data-hash="' + files[index].hash + '" class="fas fa-download" ' +
					'style="cursor:pointer;" onclick="fileDownload(this);"></i></td>';

				if (window.username === files[index].username) {
					table_html += '<td style="color: #f77;font-size: x-large;text-shadow: 0px 4px 0px #000;">' +
						'<i data-name="' + files[index].name + '" data-hash="' + files[index].hash + '" class="fas fa-trash-alt" ' +
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

function fileDownload(el) {

	const name = el.getAttribute('data-name');
	const hash = el.getAttribute('data-hash');

	postJSONData("filesDownload.php", {
		name: name,
		hash: hash,
	})
	.then(data => {
		if (!data.success) {
			for(let k in data.errors) {
				alert(data.errors[k]);
			}
		} else {
			
			var fileName = data['name'];
			
			postDownload(`${window.serverAddress}/filesDownload.php`, {
				name: fileName,
				hash: hash,
			})
			.then(blob => {
				const url = window.URL.createObjectURL(blob);
				const a = document.createElement('a');
				a.style.display = 'none';
				a.href = url;
				// the filename you want
				a.download = fileName;
				document.body.appendChild(a);
				a.click();
				window.URL.revokeObjectURL(url);
				alert(`File '${fileName}' has been downloaded.`);
			})
			.catch(() => alert('File not downloaded'));
			
		}
	})
}