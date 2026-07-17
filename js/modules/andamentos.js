function andamentoTiposReset(mensagemVazio){
	$("#andamento-tipos-vinculados").html("");
	$("#andamento-tipos-inputs").html("");
	$("#andamento-tipos-vazio").show();
	$("#andamento-tipos-vazio").text(mensagemVazio || "Nenhum andamento vinculado.");
	andamentoTiposAtualizarPool();
}
function andamentoTiposEscape(valor){
	return String(valor)
		.replace(/&/g, "&amp;")
		.replace(/"/g, "&quot;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;");
}
function andamentoTiposAtualizarInputs(){
	var html = "";
	$(".andamento-tipos-item").each(function(index){
		var tipo = $(this).attr("data-tipo");
		var numero = index + 1;
		html += "<input type='hidden' class='cls_andam_input' name='andam_name_" + numero + "' value=\"" + andamentoTiposEscape(tipo) + "\" />";
	});
	$("#andamento-tipos-inputs").html(html);
	if($(".andamento-tipos-item").length>0){
		$("#andamento-tipos-vazio").hide();
	}else{
		$("#andamento-tipos-vazio").text("Nenhum andamento vinculado.");
		$("#andamento-tipos-vazio").show();
	}
	andamentoTiposAtualizarPool();
}
function andamentoTiposAtualizarPool(){
	var select = $("#andam_name_pool");
	if(select.length===0){
		return;
	}
	var htmlBase = select.data("optionsHtml");
	if(typeof htmlBase !== "string"){
		htmlBase = select.html();
		select.data("optionsHtml", htmlBase);
	}
	select.html(htmlBase);
	$(".andamento-tipos-item").each(function(){
		var tipo = $(this).attr("data-tipo");
		select.find("option").filter(function(){
			return $.trim($(this).val()) === tipo;
		}).remove();
	});
	if(select.find("option").length>0){
		select.prop("selectedIndex", 0);
	}
}
function andamentoTiposAdicionar(){
	var tipo = $("#andam_name_pool").val();
	if(!tipo){
		alert("Selecione um andamento para adicionar.");
		return false;
	}
	return andamentoTiposAdicionarValor(tipo, false);
}
function andamentoTiposAdicionarValor(tipo, silencioso){
	var valor = $.trim(String(tipo));
	if(valor===""){
		return false;
	}
	var existe = false;
	$(".andamento-tipos-item").each(function(){
		if($(this).attr("data-tipo")===valor){
			existe = true;
		}
	});
	if(existe){
		if(!silencioso){
			alert("Esse andamento jÃ¡ estÃ¡ vinculado.");
		}
		return false;
	}
	$("#andamento-tipos-vinculados").append(
		"<div class='andamento-tipos-item' data-tipo=\"" + andamentoTiposEscape(valor) + "\">"
		+ "<span class='andamento-tipos-nome'>" + andamentoTiposEscape(valor) + "</span>"
		+ "<button type='button' class='andamento-tipos-remover' onclick='andamentoTiposRemover(this);'>Remover</button>"
		+ "</div>"
	);
	andamentoTiposAtualizarInputs();
	if(!silencioso){
		$("#andam_name_pool").val("");
	}
	return false;
}
function andamentoTiposRemover(botao){
	$(botao).closest(".andamento-tipos-item").remove();
	andamentoTiposAtualizarInputs();
	return false;
}
function fc_edit_andamento(valor1,valor2){
var andamentoAjaxUrl = window.arsAndamentoAjaxUrl || "ajax/andamentos";
	var andamentoJsonData = function(extra){
		return $.extend({ response_format: "json" }, extra || {});
	};
	var tt = "";
	var tu = "";
	if(valor2=="I"){
		tt="Novo Andamento";
		tu="criado";
		$(".validateTips").text("Crie Um " + tt);
	}else if(valor2=="U"){
		tt="Editar Andamento";
		tu="editado";
		$(".validateTips").text("Edite o Andamento Abaixo");
	}
	var abrirDialogAndamento = function(ret){
		ret = ret || {};
		$("#anda_id").val(ret.anda_id || "");
		$("#nome").val(ret.nome || "");
		$("#chave").val(ret.chave || "");
		$("#especie").val(ret.especie || "");
		$("#painel").val(ret.painel || "");
		$("#titulo").val(ret.titulo || "");
		andamentoTiposReset(valor2=="I" ? "Nenhum andamento vinculado." : "Carregando andamentos vinculados...");
		if($.isArray(ret.tipos) && ret.tipos.length>0){
			$.each(ret.tipos, function(_, tipo){
				andamentoTiposAdicionarValor(tipo, true);
			});
		}else if(valor2=="U"){
			andamentoTiposReset("Nenhum andamento vinculado.");
		}
		var especieAtual = String(ret.especie || $("#especie").val() || "");
		sel_tipo(0, especieAtual, function(){
			if(!$.isArray(ret.tipos) || ret.tipos.length===0){
				andamentoTiposAtualizarInputs();
			}
		});
		$( "#dialog-edit-andamento" ).dialog({
			title: tt,
			modal: true,
			autoOpen: true,
			height: 400,
			width: 600,
			buttons:{
				Salvar: function(){
					var mdados = {};
					var invalido = false;
					$(".cls_andamento").each(function(){
						if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
							alert("O campo " + $(this).attr("title") + " ï¿½ obrigatï¿½rio ");
							$(this).focus();
							invalido = true;
							return false;
						}
						mdados[$(this).attr("name")] = $(this).val();
					});
					if(invalido){ return false; }
					var mandam = [];
					$(".andamento-tipos-item").each(function(){
						var tipo = $.trim($(this).attr("data-tipo"));
						if(tipo!==""){
							mandam.push(tipo);
						}
					});
					if(mandam.length===0){
						alert("Selecione ao menos um andamento vinculado.");
						$("#andam_name_pool").focus();
						return false;
					}
					$.ajax({
						type: "POST",
						url: andamentoAjaxUrl,
						dataType: "json",
						data: $.extend({}, andamentoJsonData(), mdados, { flag: valor2, anda_neo: mandam.join(",") }),
						success: function(response){
							if(response && response.ok===true){
								$( "#dialog-edit-andamento" ).dialog( "close" );
								msgbox(valor2=="I"?"<br><table align='center'><tr><td>Andamento " + tu + " com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Campo editado com sucesso !</td></tr></table><br>", {
									Fechar: function(){ $( this ).dialog( "close" ); AbrirModulo('andamentos'); }
								});
							}else if(response && response.code=="duplicate"){
								alert("Andamento jï¿½ cadastrado!");
							}else{
								alert((response && response.message) ? response.message : "Erro ao salvar o andamento.");
							}
						}
					});
				},
				Sair: function() { $( this ).dialog( "close" ); }
			},
			close: function(){
				$(".cls_andamento").each(function(){ $(this).val(""); });
				andamentoTiposReset("Nenhum andamento vinculado.");
				$("#andam_name_pool").html("").data("optionsHtml", "");
			}
		});
	};
	if(valor2=="I"){
		abrirDialogAndamento({});
		return;
	}
	$.ajax({
		type: "POST",
		url: andamentoAjaxUrl,
		dataType: "json",
		data: andamentoJsonData({ flag: "E", anda_id: valor1 }),
		success: function(response){
			if(!response || response.ok!==true || !response.data){
				alert((response && response.message) ? response.message : "Erro ao carregar os dados do andamento.");
				return;
			}
			abrirDialogAndamento(response.data || {});
		}
	});
}
function fc_del_andamento(valor1,valor2){
var andamentoAjaxUrl = window.arsAndamentoAjaxUrl || "ajax/andamentos";
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar o andamento <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  andamentoAjaxUrl,
					dataType: "json",
					data: { flag: "D", anda_id: valor1, response_format: "json" },
					success: function(response){
						$( this ).dialog( "close" );
						if(response && response.ok===true){
							msgbox("<br><table align='center'><tr><td>Andamento deletado com sucesso !</td></tr></table><br>",{
								Fechar: function(){ $( this ).dialog( "close" ); AbrirModulo('andamentos'); }
							});
						}else{
							alert((response && response.message) ? response.message : "Erro ao excluir o andamento.");
						}
					}
				});
			},
			"Não": function(){ $( this ).dialog( "close" ); }
		});
}
