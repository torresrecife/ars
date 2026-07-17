function fc_edit_cliente(valor1,valor2){
	var clientResourceBaseUrl = window.arsClientResourceBaseUrl || "***REMOVED***/clientes";
	var clientResourceUrl = function(id){
		return clientResourceBaseUrl + "/" + id;
	};
	var tt = "";
	var tu = "";
	var salvarCliente = function(){
		var invalido = false;
		$('.cls_cliente').each(function(){
			if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
				alert("O campo " + $(this).attr("title") + " é obrigatório ");
				$(this).focus();
				invalido = true;
				return false;
			}
		});
		if(invalido){
			return false;
		}
		var carteiraPendente = $("#dados_name_pool").val();
		if(carteiraPendente){
			clienteCarteirasAdicionarValor(carteiraPendente, true);
			$("#dados_name_pool").val("");
		}
		if($('.cls_cliente_carteira_input').length==0){
			alert("Selecione ao menos uma carteira para o cliente.");
			$("#dados_name_pool").focus();
			return false;
		}
		var totalCarteiras = 0;
		var carteirasLista = [];
		$('.cliente-carteiras-item').each(function(index){
			var numero = index + 1;
			var valorCarteira = $(this).attr("data-carteira");
			totalCarteiras = numero;
			carteirasLista.push(valorCarteira);
		});
		var payload = {};
		$('.cls_cliente').each(function(){
			payload[$(this).attr("name")] = $(this).val();
		});
		payload.cartei_num = totalCarteiras;
		payload.dados_json = JSON.stringify(carteirasLista);
		for(var i=0;i<carteirasLista.length;i++){
			payload["dados_name_" + (i + 1)] = carteirasLista[i];
		}
		arsJsonSubmit(
			valor2=="I" ? "POST" : "PUT",
			valor2=="I" ? clientResourceBaseUrl : clientResourceUrl($("#banco_id").val()),
			payload,
			"Erro ao salvar o cliente.",
			function(){
				$("#dialog-edit-cliente").dialog("close");
				msgbox("<br><table align='center'><tr><td>Cliente " + tu + " com sucesso !</td></tr></table><br>", {
					Fechar: function(){
						$( this ).dialog( "close" );
						AbrirModulo('clientes');
					}
				});
			}
		);
	};
	if(valor2=="I"){
		tt="Novo Cliente";
		tu="criado";
		$(".validateTips").text("Crie Um " + tt);
		clienteCarteirasReset();
		$('.cls_cliente').each(function() {
			$(this).val("");
		});
		$("#dados_name_pool").val("");
		$("#dialog-edit-cliente" ).dialog({
			title: tt,
			modal: true,
			autoOpen: true,
			height: 400,
			width: 600,
			buttons: {
				Salvar: salvarCliente,
				Sair: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function(){
				$('.cls_cliente').each(function() {
					$(this).val("");
				});
				clienteCarteirasReset();
				$("#dados_name_pool").val("");
			}
		});
		return;
	}else if(valor2=="U"){
		tt="Editar Cliente";
		tu="editado";
		$(".validateTips").text("Edite o Cliente Abaixo");
	}

	arsJsonGet(clientResourceUrl(valor1), "Erro ao carregar os dados do cliente.", function(ret){
		clienteCarteirasReset();
		$("#banco_id").val(ret.banco_id || "");
		$("#banco_name").val(ret.banco_name || "");
		$("#banco_cod").val(ret.banco_cod || "");
		$("#banco_area").val(ret.banco_area || "");
		$("#banco_status").val(ret.banco_status || "");
		$("#banco_class").val(ret.banco_class || "");
		$("#simulador").val(ret.simulador || "");
		$("#banco_curto").val(ret.banco_curto || "");
		var dadosSalvos = ret.dados_codes || [];
		if(dadosSalvos.length>0){
			for(var i=0;i<dadosSalvos.length;i++){
				if(dadosSalvos[i]!=""){
					clienteCarteirasAdicionarValor(dadosSalvos[i], true);
				}
			}
		}

		$("#dialog-edit-cliente" ).dialog({
			title: tt,
			modal: true,
			autoOpen: true,
			height: 400,
			width: 600,
			buttons: {
				Salvar: salvarCliente,
				Sair: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function(){
				$('.cls_cliente').each(function() {
					$(this).val("");
				});
				clienteCarteirasReset();
				$("#dados_name_pool").val("");
			}
		});
	});
}
function clienteCarteirasReset(){
	$("#cliente-carteiras-vinculadas").html("");
	$("#cliente-carteiras-inputs").html("");
	$("#cliente-carteiras-vazio").show();
	$("#cartei_num").val(0);
}
function clienteCarteirasEscape(valor){
	return String(valor)
		.replace(/&/g, "&amp;")
		.replace(/"/g, "&quot;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;");
}
function clienteCarteirasAtualizarInputs(){
	var html = "";
	$(".cliente-carteiras-item").each(function(index){
		var valor = $(this).attr("data-carteira");
		var numero = index + 1;
		html += "<input type='hidden' class='cls_cliente_carteira_input' name='dados_name_" + numero + "' value=\"" + clienteCarteirasEscape(valor) + "\" />";
	});
	$("#cliente-carteiras-inputs").html(html);
	$("#cartei_num").val($(".cliente-carteiras-item").length);
	if($(".cliente-carteiras-item").length>0){
		$("#cliente-carteiras-vazio").hide();
	}else{
		$("#cliente-carteiras-vazio").show();
	}
}
function clienteCarteirasAdicionar(){
	var valor = $("#dados_name_pool").val();
	if(!valor){
		alert("Selecione uma carteira para adicionar.");
		return false;
	}
	return clienteCarteirasAdicionarValor(valor, false);
}
function clienteCarteirasAdicionarValor(valor, silencioso){
	var carteira = $.trim(valor);
	var existe = false;
	if(carteira==""){
		return false;
	}
	$(".cliente-carteiras-item").each(function(){
		if($(this).attr("data-carteira")===carteira){
			existe = true;
		}
	});
	if(existe){
		if(!silencioso){
			alert("Essa carteira ja esta vinculada ao cliente.");
		}
		return false;
	}
	$("#cliente-carteiras-vinculadas").append(
		"<div class='cliente-carteiras-item' data-carteira=\"" + clienteCarteirasEscape(carteira) + "\">"
		+ "<span class='cliente-carteiras-nome'>" + clienteCarteirasEscape(carteira) + "</span>"
		+ "<button type='button' class='cliente-carteiras-remover' onclick='clienteCarteirasRemover(this);'>Remover</button>"
		+ "</div>"
	);
	clienteCarteirasAtualizarInputs();
	if(!silencioso){
		$("#dados_name_pool").val("");
	}
	return false;
}
function clienteCarteirasRemover(botao){
	$(botao).closest(".cliente-carteiras-item").remove();
	clienteCarteirasAtualizarInputs();
	return false;
}
function fc_del_cliente(valor1,valor2){
	var clientResourceBaseUrl = window.arsClientResourceBaseUrl || "***REMOVED***/clientes";
	msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar o servidor <b>" + valor2 + "</b> ?</td></tr></table><br>",{
		"Sim": function(){
			var dialog = $(this);
			arsJsonSubmit("DELETE", clientResourceBaseUrl + "/" + valor1, {}, "Erro ao excluir o cliente.", function(){
				dialog.dialog("close");
				msgbox("<br><table align='center'><tr><td>Cliente deletado com sucesso !</td></tr></table><br>",{
					Fechar: function(){
						$( this ).dialog( "close" );
						AbrirModulo('clientes');
					}
				});
			});
		},
		"Não": function(){
			$( this ).dialog( "close" );
		}
	});
}
