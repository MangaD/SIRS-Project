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

				/*
				Smartphone.sendRequest({
					action: "addFile",
					hash: hash
				});
				*/
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

		fileSizeString = htmlEntities(fileSizeString);
		const fileName = htmlEntities(files[index].name);
		const fileOwnerName = htmlEntities(files[index].username);
		const fileHash = htmlEntities(files[index].hash);
		const fileCreatedAt = htmlEntities(files[index].created_at);

		table_html += '<tr>';
		table_html += '<td title="' + fileName + '">' + fileName + '</td>';
		table_html += '<td title="' + fileOwnerName + '">' + fileOwnerName + '</td>';
		table_html += '<td title="' + fileHash + '">' + fileHash + '</td>';
		table_html += '<td title="' + fileSizeString + '">' + fileSizeString + '</td>';
		table_html += '<td title="' + fileCreatedAt + '">' + fileCreatedAt + '</td>';
		table_html += '<td ' +
			' style="color: #cfc;font-size: x-large;text-shadow: 0px 4px 0px #000;">' +
			'<i data-name="' + fileName + '" data-hash="' + fileHash + '" class="fas fa-download" ' +
			'style="cursor:pointer;" onclick="fileDownload(this);"></i></td>';

		if (window.username === files[index].username) {
			table_html += '<td style="color: #f77;font-size: x-large;text-shadow: 0px 4px 0px #000;">' +
				'<i data-name="' + fileName + '" data-hash="' + fileHash + '" class="fas fa-trash-alt" ' +
				'style="cursor:pointer;" ></i></td>';
		} else {
			table_html += '<td>&nbsp;</td>';
		}
		table_html += '</tr>';
	}

	if (files.length === 0) {
		table_html += '<td colspan="7" style="text-align:center;">No files in the server.</td>';
	}

	$('#filesTable > tbody').html(table_html);
}

function generateFileListRequestObject() {
	return {};
}

function generateFileListRequestString() {
	return JSON.stringify(generateFileListRequestObject());
}

function viewFile(f) {

	const supportedTypes = ['text/plain', 'text/x-log'];

	// Only process txt files.
	if (!supportedTypes.includes(f.type)) {
		alert(`File type '${f.type}' not supported.`);
		return;
	}

	const reader = new FileReader();
	reader.fileName = f.name;
	reader.lastModifiedDate = f.lastModifiedDate;
	reader.fileSize = f.size;
	reader.fileType = f.type;

	reader.onload = event => {

		/*
		Smartphone.sendRequest({
			action: "decryptFile",
			// The atob() function decodes a string of data which has been encoded
			// using base-64 encoding. Conversely, the btoa() function creates a base-64
			// encoded ASCII string from a "string" of binary data.
			content: btoa(event.target.result),
			hash: ,
			fileName: event.target.fileName,
			fileType: event.target.fileType,
			fileSize: event.target.fileSize,
			fileLastModified: event.target.lastModifiedDate
		});
		*/

		
		//console.log(event.target.result); // desired file content
		// https://stackoverflow.com/questions/24245105/how-to-get-the-filename-from-the-javascript-filereader
		$('#viewFileModal .modal-header').html(htmlEntities(event.target.fileName));
		$('#viewFileTextarea').html(htmlEntities(event.target.result));
		$('#viewFileModal').modal('show');
		let footer = "<strong>Type:</strong> " + htmlEntities(event.target.fileType);
		footer += " <strong>Size:</strong> " + htmlEntities(event.target.fileSize) + " B";
		footer += " <strong>Last modification:</strong> " + htmlEntities(event.target.lastModifiedDate);
		$('#viewFileModal .modal-footer').html(footer);
		
	}
	reader.onerror = error => reject(error);
	reader.onloadstart = () => loaderStart();
	reader.onloadend = () => loaderEnd();
	
	// For reading as binary string
	//reader.readAsBinaryString(f);

	// For reading plain text:
	reader.readAsText(f); // you could also read images and other binaries
}