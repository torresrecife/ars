function painelAbrirDetalhe(andaId, bankId, bankName, month, year, weekKey, detailType){
	$('#detail_bank_id').val(bankId);
	$('#detail_anda_id').val(andaId);
	$('#detail_month').val(month);
	$('#detail_year').val(year);
	$('#detail_week').val(weekKey);
	$('#detail_region_id').val($('#regiao_id').val() || (window.arsPanelConfig ? window.arsPanelConfig.regionId : 0));
	$('#banco_and').val(bankName);
	$('#banco_lnc').val(bankName);
	if(detailType=='and'){
		$('#form_ars').attr('action', window.arsDetailAndamentoUrl || 'detalhes/andamentos');
	}else if(detailType=='fat'){
		$('#form_ars').attr('action', window.arsDetailFaturamentoUrl || 'detalhes/faturamento');
	}
	$('#form_ars').attr('target','_blank');
	$('#form_ars').submit();
}

function painelNavegarMes(bankId,direcao){
	var m_mes = $('#mes').val();
	painelSomarMes(m_mes,direcao);
	NavegarModulo('painel', {
		bank_id: bankId,
		area_id: $('#panel_area_id').val() || '',
		regiao_id: $('#regiao_id').val() || (window.arsPanelConfig ? window.arsPanelConfig.regionId : 0),
		mes: $('#mes').val(),
		ano: $('#ano').val()
	});
}

function painelNavegarRegiao(bankId,regiaoId){
	NavegarModulo('painel', {
		bank_id: bankId,
		area_id: $('#panel_area_id').val() || '',
		regiao_id: regiaoId,
		mes: $('#mes').val(),
		ano: $('#ano').val()
	});
}

function painelSomarMes(meses,direcao){
	var n_mes = 0;
	var n_ano = parseFloat($('#ano').val());
	if(meses==12 && direcao=='n'){
		n_mes=1;
		$('#ano').val(n_ano+1);
	}else if(meses==1 && direcao=='p'){
		n_mes=12;
		$('#ano').val(n_ano-1);
	}else{
		if(direcao=='n'){
			n_mes = parseFloat(meses) + 1;
		}else if(direcao=='p'){
			n_mes = parseFloat(meses) - 1;
		}
	}
	$('#mes').val(n_mes);
}

$(function(){
	if(typeof window.arsPanelContentHeight === 'undefined'){
		return;
	}
	var alturaConteudo = parseInt(window.arsPanelContentHeight, 10) || 0;
	var alturaTela = $(window).height() - $("#header-box").outerHeight(true);
	var alturaMinima = Math.max(290, alturaTela);

	$("#content-box").css({
		height: "auto",
		"min-height": Math.max(alturaMinima, alturaConteudo)
	});
	$("#element-box").css("height", alturaTela - 45);
	$("#content-box .adminform").css("height", "auto");
});
