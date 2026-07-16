
$(function() {
	var csrfToken = $('meta[name="csrf-token"]').attr('content');
	if(csrfToken){
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': csrfToken
			}
		});
	}

	$('.date-picker').datepicker( {
		dayNames: ['Domingo','Segunda','TerÃƒÂ§a','Quarta','Quinta','Sexta','SÃƒÂ¡bado'],
		dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
		dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','SÃƒÂ¡b','Dom'],
		monthNames: ['Janeiro','Fevereiro','MarÃƒÂ§o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
		monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
		nextText: 'PrÃƒÂ³ximo',
		prevText: 'Anterior',
		closeText: 'OK',
		currentText: 'MÃƒÂªs atual',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'MM / yy',
        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, month, 1));
            $("#mes").val(parseInt(month)+1);
            $("#ano").val(parseInt(year));
        }
    });

	$('input[type="text"]').setMask();
});
function msgbox(msg, bts){
	var $dialog = $('<div></div>')
		.html(msg)
		.dialog({
			modal: true,
			autoOpen: true,
			buttons: bts,
			title: 'Alerta'
		});
}
function mostrarErroData(){
	$("#startDate").css("border","1px solid red");
	$("#obg_date").fadeIn();
	$("#obg_date").html("Inseir o mÃƒÆ’Ã‚Âªs / ano!");
	setTimeout(function(){
		$("#startDate").css("border","1px solid #ccc");
		$("#obg_date").fadeOut();
	}, 3000);
}
function NormalizarModuloPath(scriptFile){
	var map = {
		"***REMOVED***.php": "***REMOVED***",
		"usu.php": "usuarios",
		"usuarios.php": "usuarios",
		"setor.php": "setores",
		"setores.php": "setores",
		"clientes.php": "clientes",
		"andamentos.php": "andamentos",
		"metas.php": "metas",
		"semanas.php": "semanas",
		"regioes.php": "regioes",
		"carteiras.php": "carteiras",
		"painel.php": "painel",
		"producao.php": "producao",
		"relatorio.php": "relatorio"
	};

	return map[scriptFile] || scriptFile;
}
function ColetarParametrosNavegacao(){
	var params = {};
	if($("#startDate").length && $("#startDate").val()!="") { params.startDate = $("#startDate").val(); }
	if($("#startSetor").length && $("#startSetor").val()!="") { params.startSetor = $("#startSetor").val(); }
	if($("#startBanco").length && $("#startBanco").val()!="") { params.startBanco = $("#startBanco").val(); }
	if($("#mes").length && $("#mes").val()!="") { params.mes = $("#mes").val(); }
	if($("#ano").length && $("#ano").val()!="") { params.ano = $("#ano").val(); }
	if($("#regiao_id").length && $("#regiao_id").val()!="") { params.regiao_id = $("#regiao_id").val(); }
	if($("#area_id").length && $("#area_id").val()!="") { params.area_id = $("#area_id").val(); }
	if($("#bank_id").length && $("#bank_id").val()!="") { params.bank_id = $("#bank_id").val(); }
	return params;
}
function ConstruirUrlModulo(path, params){
	var target = NormalizarModuloPath(path);
	var query = [];
	params = params || {};
	for(var key in params){
		if(!params.hasOwnProperty(key)){
			continue;
		}
		var value = params[key];
		if(value === null || typeof value === "undefined" || value === ""){
			continue;
		}
		query.push(encodeURIComponent(key) + "=" + encodeURIComponent(value));
	}
	return query.length ? (target + "?" + query.join("&")) : target;
}
function NavegarModulo(path, params){
	window.location = ConstruirUrlModulo(path, params);
}
function LerMensagemAjaxErro(xhr, fallback){
	if(xhr && xhr.responseJSON && xhr.responseJSON.message){
		return xhr.responseJSON.message;
	}
	if(xhr && xhr.responseText){
		try{
			var parsed = JSON.parse(xhr.responseText);
			if(parsed && parsed.message){
				return parsed.message;
			}
		}catch(e){}
	}
	return fallback || "Erro na operacao.";
}
function EnviarPagina(frm, precisaData, are, fla){
	if(precisaData && $("#startDate").val()==""){
		mostrarErroData();
		return;
	}
	var params = ColetarParametrosNavegacao();
	if(typeof are !== "undefined" && are !== null && are !== "") { params.area_id = are; }
	if(typeof fla !== "undefined" && fla !== null && fla !== "") { params.bank_id = fla; }
	NavegarModulo(frm, params);
}
function AbrirCarteiras(areaId){
	var params = ColetarParametrosNavegacao();
	params.area_id = areaId || "";
	delete params.bank_id;
	NavegarModulo("carteiras", params);
}
function AbrirPainel(areaId, bankId){
	if($("#startDate").val()==""){
		mostrarErroData();
		return;
	}
	var params = ColetarParametrosNavegacao();
	params.area_id = areaId || "";
	params.bank_id = bankId || "";
	NavegarModulo("painel", params);
}
function AbrirRelatorio(geral){
	if($("#startDate").length && $("#startDate").val()==""){
		mostrarErroData();
		return;
	}
	var params = ColetarParametrosNavegacao();
	params.geral = geral;
	NavegarModulo("relatorio", params);
}
function AbrirModulo(scriptFile){
	NavegarModulo(scriptFile, {});
}
function AbrirMetasAdmin(){
	var params = ColetarParametrosNavegacao();
	if($("#banco_id").length && $("#banco_id").val()!="") { params.startBanco = $("#banco_id").val(); }
	if($("#meta_mes").length && $("#meta_mes").val()!="") { params.mes = $("#meta_mes").val(); }
	if($("#meta_ano").length && $("#meta_ano").val()!="") { params.ano = $("#meta_ano").val(); }
	NavegarModulo("metas", params);
}
function AbrirMetasSelecao(){
	if($("#startDate").length && $("#startDate").val()==""){
		mostrarErroData();
		return;
	}
	if(!$("#startBanco").length || $("#startBanco").val()==""){
		alert("Selecione o cliente.");
		$("#startBanco").focus();
		return;
	}

	var params = ColetarParametrosNavegacao();
	NavegarModulo("metas", params);
}
// usuarios.js e semanas.js carregam os fluxos desses modulos

	// clientes.js, andamentos.js e metas.js carregam os handlers desses modulos
function inserir_banco(valor,stt){
	var crt = parseFloat($("#banco_num").val());
	var atr = (32 * crt) +260;
	if(stt==1){
		crt = crt+1;
		$("#banco_"+(crt-1)).html(
		"<select class='cls_usu input-default cls_usu2' name='banco_usu_"+crt+"' style='height:22px'>"+valor+"</select>" +
		"<button id='inp1_"+crt+"' class='bts' onclick='inserir_banco($(\"#banco_usu_1\").html(),1);'>+</button>" +
		"<button id='inp0_"+crt+"' class='bts' onclick='inserir_banco($(\"#banco_usu_1\").html(),0);'>-</button>" +
		"<div id='banco_"+crt+"'></div>");
		$("#inp1_"+(crt-1)).hide();
		$("#inp0_"+(crt-1)).hide();
	}else if(stt==0){
		crt = crt-1;
		$("#banco_"+crt).html(" ");
		$("#inp1_"+crt).show();
		$("#inp0_"+crt).show();
	}
	$("#tb_dialog").css("height",atr+"px");
	$("#banco_num").val(crt);
}
function sel_tipo(valor1,valor2,callback){
var selectAjaxUrl = window.arsSelectAjaxUrl || "ajax/select";
	var montarOptions = function(selectId, html){
		var select = $(selectId);
		var markup = $.trim(String(html || ""));
		var items = [];
		if(markup !== ""){
			var container = $("<div>").html(markup);
			container.find("option").each(function(){
				items.push({
					value: $(this).attr("value") || "",
					text: $(this).text()
				});
			});
		}
		if(items.length === 0){
			items.push({ value: "", text: "  " });
		}
		select.empty();
		for(var i = 0; i < items.length; i++){
			select.append($("<option>").val(items[i].value).text(items[i].text));
		}
		select.data("optionsHtml", select.html());
	};

	$.ajax({
		type: "POST",
		url: selectAjaxUrl,
		dataType: "html",
		cache: false,
		data: {
			flag: valor2,
			dados: valor1
		},
		success: function(retorno_ajax){
			if(valor1 == 0){
				montarOptions("#andam_name_pool", retorno_ajax);
				if(valor2 == 1 || valor2 == "1"){
					$("#sel_anda").html("Selecionar Andamentos:");
				}else if(valor2 == 2 || valor2 == "2"){
					$("#sel_anda").html("Selecionar Lanï¿½amentos:");
				}
				andamentoTiposAtualizarPool();
			}else if(valor1 == 1){
				montarOptions("#banco_usu_pool", retorno_ajax);
				$("#sel_banco").html("Clientes:");
				usuarioClientesAtualizarPool();
			}
			if(typeof callback === "function"){
				callback(retorno_ajax);
			}
		},
		error: function(){
			if(valor1 == 0){
				$("#andam_name_pool").html("<option value=''>Erro ao carregar</option>");
				$("#andam_name_pool").data("optionsHtml", "<option value=''>Erro ao carregar</option>");
			}else if(valor1 == 1){
				$("#banco_usu_pool").html("<option value=''>Erro ao carregar</option>");
				$("#banco_usu_pool").data("optionsHtml", "<option value=''>Erro ao carregar</option>");
			}
		}
	});
}


