$(document).ready(function () {
	if (typeof carregarFiltros === 'function') {
		carregarFiltros('tbf1');
	}

	$('tr').dblclick(function () {
		$(this).css('background', '#ffffff');
	});

	$('tr').click(function () {
		$(this).css('background', 'yellow');
	});
});

function enviar_neo(valor) {
	window.open('http://192.168.81.200/Modulos/ElementosProcessuais/ProcessoFichaGeral.aspx?idProcesso=' + valor);
}

function exportDetailTable(tableId, fileName) {
	var sourceTable = document.getElementById(tableId);
	if (!sourceTable) {
		return;
	}

	var exportTable = sourceTable.cloneNode(true);
	var sourceRows = sourceTable.getElementsByTagName('tr');
	var exportRows = exportTable.getElementsByTagName('tr');
	var visibleRows = [];
	var i;

	for (i = 0; i < sourceRows.length; i++) {
		if ($(sourceRows[i]).is(':visible')) {
			visibleRows.push(exportRows[i]);
		}
	}

	while (exportTable.firstChild) {
		exportTable.removeChild(exportTable.firstChild);
	}

	for (i = 0; i < visibleRows.length; i++) {
		exportTable.appendChild(visibleRows[i]);
	}

	$(exportTable).find('select').each(function () {
		var selected = $(this).find('option:selected').text() || $(this).find('option').first().text() || '';
		$(this).replaceWith(document.createTextNode(selected));
	});

	$(exportTable).find('[data-excel-type="text"]').each(function () {
		var currentStyle = this.getAttribute('style') || '';
		if (currentStyle !== '' && currentStyle.slice(-1) !== ';') {
			currentStyle += ';';
		}
		this.setAttribute('style', currentStyle + "mso-number-format:'\\@';");
	});

	$(exportTable).find('[onclick]').removeAttr('onclick');

	var html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" ' +
		'xmlns:x="urn:schemas-microsoft-com:office:excel" ' +
		'xmlns="http://www.w3.org/TR/REC-html40"><head>' +
		'<meta charset="utf-8"></head><body>' +
		exportTable.outerHTML +
		'</body></html>';

	var blob = new Blob(['\ufeff', html], {
		type: 'application/vnd.ms-excel;charset=utf-8;'
	});

	var safeFileName = (fileName || 'detail-report')
		.replace(/[\\/:*?"<>|]+/g, '-')
		.replace(/\s+/g, '-');

	if (window.navigator && window.navigator.msSaveOrOpenBlob) {
		window.navigator.msSaveOrOpenBlob(blob, safeFileName + '.xls');
		return;
	}

	var url = window.URL.createObjectURL(blob);
	var link = document.createElement('a');
	link.href = url;
	link.download = safeFileName + '.xls';
	document.body.appendChild(link);
	link.click();
	document.body.removeChild(link);
	window.setTimeout(function () {
		window.URL.revokeObjectURL(url);
	}, 1000);
}
