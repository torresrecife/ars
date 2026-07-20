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
