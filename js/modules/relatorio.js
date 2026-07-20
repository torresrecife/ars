function relatorioAbrirDetalhe(valor1,valor2){
	$("#codig_lnc").val(valor1);
	$("#banco_lnc").val(valor2);
	$("#form_ars").attr("action", window.arsDetailFaturamentoUrl || "detalhes/faturamento");
	$("#form_ars").attr("target","_blank");
	$("#form_ars").submit();
}

$(function () {
	if (window.arsReportContentHeight) {
		$("#content-box").css("height", parseInt(window.arsReportContentHeight, 10) + "px");
	}
});
