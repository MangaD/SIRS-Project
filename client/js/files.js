"use strict";

function loadFiles(ciphertext) {
	if (window.use_custom_secure_channel) {
		Smartphone.sendRequest({
			action: "encrypt",
			do: "files",
			message: generateFileListRequestString()
		});
	} else {
		postLoadFiles();
	}
}

function postLoadFiles(ciphertext) {

	loaderStart();

	// send as plaintext if no cipher provided
	if (!ciphertext) {
		ciphertext = generateFileListRequestObject();
	}

	postJSONData("files.php", ciphertext)
	.then(data => {

		if (data.hasOwnProperty('ciphertext')) {
			Smartphone.sendRequest({
				action: "decrypt",
				do: "filesList",
				message: data.ciphertext
			});
		} else {
			serverResponseLoadFiles(data);
		}

	})
	.catch((error2) => {
		console.log(error2);
		alert(error2);
		loaderEnd();
	});
}

function serverResponseLoadFiles(data) {
	if (!data.success) {
		for (let k in data.errors) {
			alert(data.errors[k]);
		}
		loaderEnd();
	} else {
		generateFilesTable(data.list);
	}

	loaderEnd();
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

function generateFilesTable(files) {
	let table_html;

	for (let index = 0, len = files.length; index < len; ++index) {

		let fileSizeInt = parseInt(files[index].size);
		let fileSizeString;
		if (fileSizeInt < 1024) {
			fileSizeString = fileSizeInt + " B";
		} else if (fileSizeInt >= 1024) {
			fileSizeString = Math.round(fileSizeInt / 1024* 100) / 100 + " KiB";
		} else if (fileSizeInt >= 1024*1024) {
			fileSizeString = Math.round(fileSizeInt / 1024 / 1024* 100) / 100 + " MiB";
		} else if (fileSizeInt >= 1024*1024*1024) {
			fileSizeString = Math.round(fileSizeInt / 1024 / 1024 / 1024* 100) / 100 + " GiB";
		}

		table_html += '<tr>';
		table_html += '<td title="' + files[index].name + '">' + files[index].name + '</td>';
		table_html += '<td title="' + files[index].username + '">' + files[index].username + '</td>';
		table_html += '<td title="' + files[index].hash + '">' + files[index].hash + '</td>';
		table_html += '<td title="' + files[index].fileSizeString + '">' + fileSizeString + '</td>';
		table_html += '<td title="' + files[index].created_at + '">' + files[index].created_at + '</td>';
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

function generateFileListRequestObject() {
	return {};
}

function generateFileListRequestString() {
	return JSON.stringify(generateFileListRequestObject());
}