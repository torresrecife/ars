function fc_edit_cliente(valor1,valor2){
var clientAjaxUrl = window.arsClientAjaxUrl || "ajax/clientes";
		var tt = "";
		var tu = "";
		var salvarCliente = function(){
			var mdados="";
			var invalido = false;
			$('.cls_cliente').each(function(){
				if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
					alert("O campo " + $(this).attr("title") + " é obrigatório ");
					$(this).focus();
					invalido = true;
					return false;
				}
				mdados += $(this).attr("name")+"="+escape($(this).val())+"&";
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
			var cartei="";
			var totalCarteiras = 0;
			var carteirasLista = [];
			$('.cliente-carteiras-item').each(function(index){
				var numero = index + 1;
				var valorCarteira = $(this).attr("data-carteira");
				totalCarteiras = numero;
				carteirasLista.push(valorCarteira);
				cartei += "dados_name_" + numero + "=" + escape(valorCarteira) + "&";
			});
			$.ajax({
			   type: "POST",
			   url:  clientAjaxUrl,
			   dataType: "json",
			   data: "flag=" + valor2 + "&response_format=json&" + mdados + "&cartei_num=" + totalCarteiras + "&dados_json=" + encodeURIComponent(JSON.stringify(carteirasLista)) + "&" + cartei,
			   success: function(response){
					if(response && response.ok===true){
						$( "#dialog-edit-cliente" ).dialog( "close" );
						msgbox("<br><table align='center'><tr><td>Cliente " + tu + " com sucesso !</td></tr></table><br>", {
							Fechar: function(){
								$( this ).dialog( "close" );
								AbrirModulo('clientes');
							}
						});
					}else if(response && response.code=="duplicate"){
						alert("Cliente já cadastrado!");
					}else{
						alert((response && response.message) ? response.message : "Erro ao salvar o cliente.");
					}
				},
				error: function(xhr){
					alert(LerMensagemAjaxErro(xhr, "Erro ao salvar o cliente."));
				}
			});
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

		$.ajax({
			type: "POST",
			url:  clientAjaxUrl,
			dataType: "json",
			data: { flag: "E", banco_id: valor1, response_format: "json" },
			success: function(response){
				if(!response || response.ok!==true || !response.data){
					alert((response && response.message) ? response.message : "Erro ao carregar os dados do cliente.");
					return;
				}
				var ret = response.data || {};
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
			},
			error: function(xhr){
				alert(LerMensagemAjaxErro(xhr, "Erro ao carregar os dados do cliente."));
			}
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
var clientAjaxUrl = window.arsClientAjaxUrl || "ajax/clientes";
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar o servidor <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  clientAjaxUrl,
					dataType: "json",
					data: { flag: "D", banco_id: valor1, response_format: "json" },
					success: function(response){
						$( this ).dialog( "close" );
						if(response && response.ok===true){
							msgbox("<br><table align='center'><tr><td>Cliente deletado com sucesso !</td></tr></table><br>",{
								Fechar: function(){
									$( this ).dialog( "close" );
									AbrirModulo('clientes');
								}
							});
						}else{
							alert((response && response.message) ? response.message : "Erro ao excluir o cliente.");
						}
					},
					error: function(xhr){
						alert(LerMensagemAjaxErro(xhr, "Erro ao excluir o cliente."));
					}
				});
			},
			"Não": function(){
				$( this ).dialog( "close" );
			}
		});
}
