function andamentoTiposReset(mensagemVazio){
	$("#andamento-tipos-vinculados").html("");
	$("#andamento-tipos-inputs").html("");
	$("#andamento-tipos-vazio").show();
	$("#andamento-tipos-vazio").text(mensagemVazio || arsTranslate("No linked progress items."));
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
		$("#andamento-tipos-vazio").text(arsTranslate("No linked progress items."));
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
		alert(arsTranslate("Select a progress item to add."));
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
			alert(arsTranslate("This progress item is already linked."));
		}
		return false;
	}
	$("#andamento-tipos-vinculados").append(
		"<div class='andamento-tipos-item' data-tipo=\"" + andamentoTiposEscape(valor) + "\">"
		+ "<span class='andamento-tipos-nome'>" + andamentoTiposEscape(valor) + "</span>"
		+ "<button type='button' class='andamento-tipos-remover' onclick='andamentoTiposRemover(this);'>" + arsTranslate("Remove") + "</button>"
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
	var andamentoResourceBaseUrl = window.arsAndamentoResourceBaseUrl || "admin/andamentos";
	var andamentoResourceUrl = function(id){
		return andamentoResourceBaseUrl + "/" + id;
	};
	var tt = "";
	var successMessage = "";
	if(valor2=="I"){
		tt=arsTranslate("New Progress");
		successMessage=arsTranslate("Progress created successfully.");
		$(".validateTips").text(arsTranslate("Create a new progress"));
	}else if(valor2=="U"){
		tt=arsTranslate("Edit Progress");
		successMessage=arsTranslate("Field updated successfully.");
		$(".validateTips").text(arsTranslate("Edit the progress below"));
	}
	var abrirDialogAndamento = function(ret){
		ret = ret || {};
		$("#anda_id").val(ret.anda_id || "");
		$("#nome").val(ret.nome || "");
		$("#chave").val(ret.chave || "");
		$("#especie").val(ret.especie || "");
		$("#painel").val(ret.painel || "");
		$("#titulo").val(ret.titulo || "");
		andamentoTiposReset(valor2=="I" ? arsTranslate("No linked progress items.") : arsTranslate("Loading linked progress items..."));
		if($.isArray(ret.tipos) && ret.tipos.length>0){
			$.each(ret.tipos, function(_, tipo){
				andamentoTiposAdicionarValor(tipo, true);
			});
		}else if(valor2=="U"){
			andamentoTiposReset(arsTranslate("No linked progress items."));
		}
		var especieAtual = String(ret.especie || $("#especie").val() || "");
		sel_tipo(0, especieAtual, function(){
			if(!$.isArray(ret.tipos) || ret.tipos.length===0){
				andamentoTiposAtualizarInputs();
			}
		});
		$("#dialog-edit-andamento").dialog({
			title: tt,
			modal: true,
			autoOpen: true,
			height: 400,
			width: 600,
			buttons:{
				[arsTranslate("Save")]: function(){
					var mdados = {};
					var invalido = false;
					$(".cls_andamento").each(function(){
						if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
							alert(arsFormat("The field :field is required.", {field: $(this).attr("title")}));
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
						alert(arsTranslate("Select at least one linked progress item."));
						$("#andam_name_pool").focus();
						return false;
					}
					arsJsonSubmit(
						valor2=="I" ? "POST" : "PUT",
						valor2=="I" ? andamentoResourceBaseUrl : andamentoResourceUrl($("#anda_id").val()),
						$.extend({}, mdados, { anda_neo: mandam.join(",") }),
						arsTranslate("Error saving progress."),
						function(){
							$("#dialog-edit-andamento").dialog("close");
							msgbox("<br><table align='center'><tr><td>" + successMessage + "</td></tr></table><br>", {
								[arsTranslate("Close")]: function(){ $(this).dialog("close"); AbrirModulo('andamentos'); }
							});
						}
					);
				},
				[arsTranslate("Exit")]: function() { $(this).dialog("close"); }
			},
			close: function(){
				$(".cls_andamento").each(function(){ $(this).val(""); });
				andamentoTiposReset(arsTranslate("No linked progress items."));
				$("#andam_name_pool").html("").data("optionsHtml", "");
			}
		});
	};
	if(valor2=="I"){
		abrirDialogAndamento({});
		return;
	}
	arsJsonGet(andamentoResourceUrl(valor1), arsTranslate("Error loading progress data."), function(ret){
		abrirDialogAndamento(ret || {});
	});
}
function fc_del_andamento(valor1,valor2){
	var andamentoResourceBaseUrl = window.arsAndamentoResourceBaseUrl || "admin/andamentos";
	msgbox("<br><table align='center'><tr><td style='font-size:8pt'>" + arsFormat("Do you really want to delete the progress :name?", {name: "<b>" + valor2 + "</b>"}) + "</td></tr></table><br>",{
		[arsTranslate("Yes")]: function(){
			var dialog = $(this);
			arsJsonSubmit("DELETE", andamentoResourceBaseUrl + "/" + valor1, {}, arsTranslate("Error deleting progress."), function(){
				dialog.dialog("close");
				msgbox("<br><table align='center'><tr><td>" + arsTranslate("Progress deleted successfully.") + "</td></tr></table><br>",{
					[arsTranslate("Close")]: function(){ $(this).dialog("close"); AbrirModulo('andamentos'); }
				});
			});
		},
		[arsTranslate("No")]: function(){ $(this).dialog("close"); }
	});
}
