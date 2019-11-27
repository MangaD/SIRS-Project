"use strict";

function loadFiles() {
	// TODO calls to server

	// Demo
	let files = [
		{
			name: "hello2.txt",
			hash: "ce342bef5df56c933352d71edbbc35b36a9160d8",
			owner: "leo",
		},
		{
			name: "hello.txt",
			hash: "ce342bef5df56c933352d71edbbc35b36a9160d8",
			owner: "leo",
		},
		{
			name: "hello5.txt",
			hash: "ce342bef5df56c933352d71edbbc35b36a9160d8",
			owner: "123456",
		}
	];

	let table_html;

	for (let index = 0, len = files.length; index < len; ++index) {
		table_html += '<tr>';
		table_html += '<td>' + files[index].name + '</td>';
		table_html += '<td>' + files[index].owner + '</td>';
		table_html += '<td>' + files[index].hash + '</td>';
		table_html += '<td ' +
			' style="color: #cfc;font-size: x-large;text-shadow: 0px 4px 0px #000;">' +
			'<i data-hash="' + files[index].hash + '" class="fas fa-download"></i></td>';
		if (window.username === files[index].owner) {
			table_html += '<td style="color: #f77;font-size: x-large;text-shadow: 0px 4px 0px #000;">' +
				'<i data-hash="' + files[index].hash + '" class="fas fa-trash-alt"></i></td>';
		} else {
			table_html += '<td>&nbsp;</td>';
		}
		table_html += '</tr>';
	}

	$('#filesTable > tbody').html(table_html);
}
