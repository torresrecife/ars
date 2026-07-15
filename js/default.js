
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
function fc_edit_usu(valor1,valor2){
var userAjaxUrl = window.arsUserAjaxUrl || "ajax/usuarios";
		var tt = "";
		var tu = "";
		if(valor2=="I"){
			tt="Novo UsuÃ¡rio";
			tu="criado";
			$(".validateTips").text("Crie Um " + tt);
			usuarioClientesReset();
			usuarioRegioesReset();
			$('.cls_usu').each(function() {
				$(this).val("");
			});
			$("#setor_usu").val("0");
			$("#regiao_modo").val("N");
			usuarioRegioesAtualizarModo();
			sel_tipo(1, $("#setor_usu").val());
		}else if(valor2=="U"){
			tt="Editar UsuÃ¡rio";
			tu="editado";
			$(".validateTips").text("Edite o UsuÃ¡rio Abaixo");
		}

		var abrirDialogUsuario = function(){
			$("#dialog-edit-usu").dialog({
				title: tt,
				modal: true,
				autoOpen: true,
				height: 470,
				width: 520,
				buttons: {
					Salvar: function() {
						var mdados="";
						var invalido = false;
						$('.cls_usu').each(function(){
							if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
								alert("O campo " + $(this).attr("title") + " ï¿½ obrigatï¿½rio ");
								$(this).focus();
								invalido = true;
								return false;
							}
							mdados += $(this).attr("name")+"="+escape($(this).val())+"&";
						});
						if(invalido){
							return false;
						}
						if($('.cls_usuario_cliente_input').length==0){
							alert("Selecione ao menos um cliente para o usuÃ¡rio.");
							$("#banco_usu_pool").focus();
							return false;
						}
						var usus = [];
						var regioes = [];
						$('.usuario-clientes-item').each(function(){
							var clienteId = $(this).attr("data-cliente-id");
							if(clienteId){
								usus.push(clienteId);
							}
						});
						$('.usuario-regioes-item').each(function(){
							var regiaoId = $(this).attr("data-regiao-id");
							if(regiaoId){
								regioes.push(regiaoId);
							}
						});
						var dado_email = validaEmail($("#email_usu").val());
						var dado_senha = fc_teste_senha($("#senha_usu1").val(),$("#senha_usu2").val(),valor2);
						if(dado_email!=""){
							alert(dado_email);
						}else if(dado_senha!=""){
							alert(dado_senha);
						}else{
						$.ajax({
							   type: "POST",
							   url:  userAjaxUrl,
							   dataType: "json",
							   data: "flag=" + valor2 + "&response_format=json&" + mdados + "&banco_neo=" + usus.join(",") + "&regiao_neo=" + regioes.join(","),
							   success: function(response){
									if(response && response.ok===true){
										$( "#dialog-edit-usu" ).dialog( "close" );
										msgbox(valor2=="I"?"<br><table align='center'><tr><td>UsuÃ¡rio " + tu + " com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Campo editado com sucesso !</td></tr></table><br>", {
											Fechar: function(){
												$( this ).dialog( "close" );
												AbrirModulo('usuarios');
											}
										});
									}else if(response && response.code=="duplicate"){
										alert("UsuÃ¡rio jÃ¡ cadastrado!");
									}else{
										alert((response && response.message) ? response.message : "Erro ao salvar o usuÃ¡rio.");
									}
								},
								error: function(xhr){
									alert(LerMensagemAjaxErro(xhr, "Erro ao salvar o usuÃ¡rio."));
								}
							});
						}
					},
					Sair: function() {
						$( this ).dialog( "close" );
					}
				},
				close: function(){
					$('.cls_usu').each(function() {
						$(this).val("");
					});
					usuarioClientesReset();
					usuarioRegioesReset();
					$("#banco_usu_pool").html("");
				}
			});
			$("#nivel_usu").off("change.usuarioRegioes").on("change.usuarioRegioes", usuarioRegioesAtualizarModo);
			$("#regiao_modo").off("change.usuarioRegioes").on("change.usuarioRegioes", usuarioRegioesAtualizarModo);
			usuarioRegioesAtualizarModo();
		};

		if(valor2=="I"){
			abrirDialogUsuario();
			return;
		}

		$.ajax({
			type: "POST",
			url:  userAjaxUrl,
			dataType: "json",
			data: { flag: "E", id_usu: valor1, response_format: "json" },
			success: function(response){
				if(!response || response.ok!==true || !response.data){
					alert((response && response.message) ? response.message : "Erro ao carregar os dados do usuÃ¡rio.");
					return;
				}
				var ret = response.data || {};
				usuarioClientesReset();
				usuarioRegioesReset();
				$("#id_usu").val(ret.id_usu || "");
				$("#nome_usu").val(ret.nome_usu || "");
				$("#login_usu").val(ret.login_usu || "");
				$("#email_usu").val(ret.email_usu || "");
				$("#nivel_usu").val(ret.nivel_usu || "");
				$("#setor_usu").val(ret.id_setor || "0");
				$("#regiao_modo").val(ret.regiao_modo || "N");
				$("#status_usu").val(ret.status_usu || "");
				var regions = ret.regions || [];
				for(var j=0;j<regions.length;j++){
					usuarioRegioesAdicionarValor(String(regions[j].id), regions[j].name, true);
				}
				usuarioRegioesAtualizarModo();
				sel_tipo(1, ret.id_setor || 0, function(){
					var clients = ret.clients || [];
					for(var i=0;i<clients.length;i++){
						usuarioClientesAdicionarValor(String(clients[i].id), clients[i].name, true);
					}
				});
				abrirDialogUsuario();
			},
			error: function(xhr){
				alert(LerMensagemAjaxErro(xhr, "Erro ao carregar os dados do usuÃ¡rio."));
			}
		});
	}
	//funÃ§Ã£o editar semana
	function fc_edit_sem(valor1,valor2){
var weekAjaxUrl = window.arsWeekAjaxUrl || "ajax/semanas";

		var tt = "";
		var tu = "";
		if(valor2=="I"){
			tt="Nova Semana";
			tu="criada";
			$(".validateTips").text("Crie Um " + tt);
		}else if(valor2=="U"){
			tt="Editar Semana";
			tu="editada";
			$(".validateTips").text("Edite o UsuÃ¡rio Abaixo");
		}

		var abrirDialogSemana = function(){
			$("#dialog-edit-sem").dialog({
				title: tt,
				modal: true,
				autoOpen: true,
				height: 420,
				width: 450,
				buttons: {
					Salvar: function() {
						var mdados = "";
						$('.cls_sem').each(function(){
							mdados += $(this).attr("name")+"="+escape($(this).val())+"&";
						});
						$.ajax({
						   type: "POST",
						   url:  weekAjaxUrl,
						   dataType: "json",
						   data: "flag=" + valor2 + "&response_format=json&" + mdados,
						   success: function(response){
								if(response && response.ok===true){
									$( "#dialog-edit-sem" ).dialog( "close" );
									msgbox(valor2=="I"?"<br><table align='center'><tr><td>Semana " + tu + " com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Campo editado com sucesso !</td></tr></table><br>", {
										Fechar: function(){
											$( this ).dialog( "close" );
											AbrirModulo('semanas');
										}
									});
								}else if(response && response.code=="duplicate"){
									alert("Semana jÃ¡ cadastrada!");
								}else{
									alert((response && response.message) ? response.message : "Erro ao salvar a semana.");
								}
							},
							error: function(xhr){
								alert(LerMensagemAjaxErro(xhr, "Erro ao salvar a semana."));
							}
						});
					},
					Sair: function() {
						$( this ).dialog( "close" );
					}
				},
				close: function(){
					$('.cls_sem').each(function() {
						$(this).val("");
					});
				}
			});
		};

		if(valor2=="I"){
			$('.cls_sem').each(function() {
				$(this).val("");
			});
			abrirDialogSemana();
			return;
		}

		$.ajax({
			type: "POST",
			url:  weekAjaxUrl,
			dataType: "json",
			data: { flag: "E", id_sem: valor1, response_format: "json" },
			success: function(response){
				if(!response || response.ok!==true || !response.data){
					alert((response && response.message) ? response.message : "Erro ao carregar os dados da semana.");
					return;
				}
				var ret = response.data || {};

				$("#id_sem").val(ret.semanas_id || ret.id_sem || "");
				$("#mes_sem").val(ret.mes || "");
				$("#ano_sem").val(ret.ano || "");
				$("#ini1_sem").val(ret.ini_1 || "");
				$("#fim1_sem").val(ret.fim_1 || "");
				$("#ini2_sem").val(ret.ini_2 || "");
				$("#fim2_sem").val(ret.fim_2 || "");
				$("#ini3_sem").val(ret.ini_3 || "");
				$("#fim3_sem").val(ret.fim_3 || "");
				$("#ini4_sem").val(ret.ini_4 || "");
				$("#fim4_sem").val(ret.fim_4 || "");
				$("#ini5_sem").val(ret.ini_5 || "");
				$("#fim5_sem").val(ret.fim_5 || "");

				abrirDialogSemana();
			},
			error: function(xhr){
				alert(LerMensagemAjaxErro(xhr, "Erro ao carregar os dados da semana."));
			}
		});
	}

	//funÃ§Ã£o editar setores
	function fc_edit_setor(valor1,valor2){
var sectorAjaxUrl = window.arsSectorAjaxUrl || "ajax/setores";

		var tt = "";
		var tu = "";
		if(valor2=="I"){
			tt="Novo Setor";
			tu="criado";
			$(".validateTips").text("Crie Um " + tt);
		}else if(valor2=="U"){
			tt="Editar Setor";
			tu="editado";
			$(".validateTips").text("Edite o Setor Abaixo");
		}

		$.ajax({
			type: "POST",
			url:  sectorAjaxUrl,
			data: "flag=E&id_setor=" + valor1,
			success: function(retorno_ajax){
				var ret = retorno_ajax.split("-|-");
				//alert(ret[1]);
				$("#id_setor").val(ret[0]);
				$("#nome_setor").val(ret[1]);

				$( "#dialog-edit-setor" ).dialog({
					title: tt,
					modal: true,
					autoOpen: true,
					height: 440,
					width: 450,
					buttons: {
						Salvar: function() {
							var mdados="";
							$('.cls_setor').each(function(){
								if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
									alert("O campo " + $(this).attr("title") + " ï¿½ obrigatï¿½rio ");
									$(this).focus();
									return false;
								}
								mdados += $(this).attr("name")+"="+escape($(this).val())+"&";
							});

							$.ajax({
							   type: "POST",
							   url:  sectorAjaxUrl,
							   data: "flag=" + valor2 + "&" + mdados,
							   success: function(retorno_ajax){
									if(retorno_ajax==1){
										$( "#dialog-edit-setor" ).dialog( "close" );
										msgbox(valor2=="I"?"<br><table align='center'><tr><td>Setor " + tu + " com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Campo editado com sucesso !</td></tr></table><br>", {
											Fechar: function(){
												$( this ).dialog( "close" );
												AbrirModulo('setores');
											}
										});
									}else if(retorno_ajax==2){
										alert("Setor jÃ¡ cadastrado!");
									}else{
										alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao ***REMOVED***istrador)");
									}
								}
							});

						},
						Sair: function() {
							$( this ).dialog( "close" );
						}
					},
					close: function(){
						$('.cls_setor').each(function() {
							$(this).val("");
						});
					}
				});
			}
		});
	}
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
function usuarioClientesReset(){
	$("#usuario-clientes-vinculados").html("");
	$("#usuario-clientes-inputs").html("");
	$("#usuario-clientes-vazio").show();
	usuarioClientesAtualizarPool();
}
function usuarioClientesEscape(valor){
	return String(valor)
		.replace(/&/g, "&amp;")
		.replace(/"/g, "&quot;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;");
}
function usuarioClientesAtualizarInputs(){
	var html = "";
	$(".usuario-clientes-item").each(function(index){
		var clienteId = $(this).attr("data-cliente-id");
		var numero = index + 1;
		html += "<input type='hidden' class='cls_usuario_cliente_input' name='banco_usu_" + numero + "' value=\"" + usuarioClientesEscape(clienteId) + "\" />";
	});
	$("#usuario-clientes-inputs").html(html);
	if($(".usuario-clientes-item").length>0){
		$("#usuario-clientes-vazio").hide();
	}else{
		$("#usuario-clientes-vazio").show();
	}
	usuarioClientesAtualizarPool();
}
function usuarioClientesAtualizarPool(){
	var select = $("#banco_usu_pool");
	if(select.length===0){
		return;
	}
	var htmlBase = select.data("optionsHtml");
	if(typeof htmlBase !== "string"){
		htmlBase = select.html();
		select.data("optionsHtml", htmlBase);
	}
	select.html(htmlBase);
	$(".usuario-clientes-item").each(function(){
		var clienteId = $(this).attr("data-cliente-id");
		select.find("option[value='" + clienteId + "']").remove();
	});
	if(select.find("option").length>0){
		select.prop("selectedIndex", 0);
	}
}
function usuarioClientesAdicionar(){
	var clienteId = $("#banco_usu_pool").val();
	if(!clienteId){
		alert("Selecione um cliente para adicionar.");
		return false;
	}
	var clienteNome = $.trim($("#banco_usu_pool option:selected").text());
	return usuarioClientesAdicionarValor(clienteId, clienteNome, false);
}
function usuarioClientesAdicionarValor(clienteId, clienteNome, silencioso){
	var id = $.trim(String(clienteId));
	if(id===""){
		return false;
	}
	var existe = false;
	$(".usuario-clientes-item").each(function(){
		if($(this).attr("data-cliente-id")===id){
			existe = true;
		}
	});
	if(existe){
		if(!silencioso){
			alert("Esse cliente jÃ¡ estÃ¡ vinculado ao usuÃ¡rio.");
		}
		return false;
	}
	$("#usuario-clientes-vinculados").append(
		"<div class='usuario-clientes-item' data-cliente-id=\"" + usuarioClientesEscape(id) + "\">"
		+ "<span class='usuario-clientes-nome'>" + usuarioClientesEscape(clienteNome) + "</span>"
		+ "<button type='button' class='usuario-clientes-remover' onclick='usuarioClientesRemover(this);'>Remover</button>"
		+ "</div>"
	);
	usuarioClientesAtualizarInputs();
	if(!silencioso){
		$("#banco_usu_pool").val("");
	}
	return false;
}
function usuarioClientesRemover(botao){
	$(botao).closest(".usuario-clientes-item").remove();
	usuarioClientesAtualizarInputs();
	return false;
}
function usuarioRegioesReset(){
	$("#usuario-regioes-vinculadas").html("");
	$("#usuario-regioes-inputs").html("");
	$("#usuario-regioes-vazio").show();
	usuarioRegioesAtualizarPool();
}
function usuarioRegioesEscape(valor){
	return String(valor)
		.replace(/&/g, "&amp;")
		.replace(/"/g, "&quot;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;");
}
function usuarioRegioesAtualizarInputs(){
	var html = "";
	$(".usuario-regioes-item").each(function(index){
		var regiaoId = $(this).attr("data-regiao-id");
		var numero = index + 1;
		html += "<input type='hidden' class='cls_usuario_regiao_input' name='regiao_usu_" + numero + "' value=\"" + usuarioRegioesEscape(regiaoId) + "\" />";
	});
	$("#usuario-regioes-inputs").html(html);
	if($(".usuario-regioes-item").length>0){
		$("#usuario-regioes-vazio").hide();
	}else{
		$("#usuario-regioes-vazio").show();
	}
	usuarioRegioesAtualizarPool();
}
function usuarioRegioesAtualizarPool(){
	var select = $("#regiao_usu_pool");
	if(select.length===0){
		return;
	}
	var htmlBase = select.data("optionsHtml");
	if(typeof htmlBase !== "string"){
		htmlBase = select.html();
		select.data("optionsHtml", htmlBase);
	}
	select.html(htmlBase);
	$(".usuario-regioes-item").each(function(){
		var regiaoId = $(this).attr("data-regiao-id");
		select.find("option[value='" + regiaoId + "']").remove();
	});
	if(select.find("option").length>0){
		select.prop("selectedIndex", 0);
	}
}
function usuarioRegioesAtualizarModo(){
	var nivel = $("#nivel_usu").val();
	var modo = $("#regiao_modo").val();
	$("#regiao_modo option[value='T']").show();
	if(nivel==="USU"){
		$("#regiao_modo option[value='T']").hide();
		if(modo==="T"){
			modo = $(".usuario-regioes-item").length>0 ? "R" : "N";
			$("#regiao_modo").val(modo);
		}
		if($(".usuario-regioes-item").length>1){
			$(".usuario-regioes-item:gt(0)").remove();
			usuarioRegioesAtualizarInputs();
		}
	}
	if(modo==="R" || nivel==="USU"){
		$("#usuario-regioes-row").show();
	}else{
		$("#usuario-regioes-row").hide();
	}
}
function usuarioRegioesAdicionar(){
	var regiaoId = $("#regiao_usu_pool").val();
	if(!regiaoId){
		alert("Selecione uma regiao para adicionar.");
		return false;
	}
	var regiaoNome = $.trim($("#regiao_usu_pool option:selected").text());
	return usuarioRegioesAdicionarValor(regiaoId, regiaoNome, false);
}
function usuarioRegioesAdicionarValor(regiaoId, regiaoNome, silencioso){
	var id = $.trim(String(regiaoId));
	if(id===""){
		return false;
	}
	var nivel = $("#nivel_usu").val();
	if(nivel==="USU" && $(".usuario-regioes-item").length>0){
		if(!silencioso){
			alert("Usuario comum pode ter apenas uma regiao vinculada.");
		}
		return false;
	}
	var existe = false;
	$(".usuario-regioes-item").each(function(){
		if($(this).attr("data-regiao-id")===id){
			existe = true;
		}
	});
	if(existe){
		if(!silencioso){
			alert("Essa regiao ja esta vinculada ao usuario.");
		}
		return false;
	}
	$("#usuario-regioes-vinculadas").append(
		"<div class='usuario-regioes-item' data-regiao-id=\"" + usuarioRegioesEscape(id) + "\">"
		+ "<span class='usuario-regioes-nome'>" + usuarioRegioesEscape(regiaoNome) + "</span>"
		+ "<button type='button' class='usuario-regioes-remover' onclick='usuarioRegioesRemover(this);'>Remover</button>"
		+ "</div>"
	);
	usuarioRegioesAtualizarInputs();
	usuarioRegioesAtualizarModo();
	if(!silencioso){
		$("#regiao_usu_pool").val("");
	}
	return false;
}
function usuarioRegioesRemover(botao){
	$(botao).closest(".usuario-regioes-item").remove();
	usuarioRegioesAtualizarInputs();
	usuarioRegioesAtualizarModo();
	return false;
}
function regiaoUfsReset(){
	$("#regiao-ufs-vinculadas").html("");
	$("#regiao-ufs-vazio").show();
	$("#regiao_ufs").val("");
	regiaoUfsAtualizarPool();
}
function regiaoUfsEscape(valor){
	return String(valor)
		.replace(/&/g, "&amp;")
		.replace(/"/g, "&quot;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;");
}
function regiaoUfsAtualizarValor(){
	var ufs = [];
	$(".regiao-ufs-item").each(function(){
		var uf = $(this).attr("data-uf");
		if(uf){
			ufs.push(uf);
		}
	});
	$("#regiao_ufs").val(ufs.join(","));
	if(ufs.length>0){
		$("#regiao-ufs-vazio").hide();
	}else{
		$("#regiao-ufs-vazio").show();
	}
	regiaoUfsAtualizarPool();
}
function regiaoUfsAtualizarPool(){
	var select = $("#regiao_uf_pool");
	if(select.length===0){
		return;
	}
	var htmlBase = select.data("optionsHtml");
	if(typeof htmlBase !== "string"){
		htmlBase = select.html();
		select.data("optionsHtml", htmlBase);
	}
	select.html(htmlBase);
	$(".regiao-ufs-item").each(function(){
		var uf = $(this).attr("data-uf");
		select.find("option[value='" + uf + "']").remove();
	});
	if(select.find("option").length>0){
		select.prop("selectedIndex", 0);
	}
}
function regiaoUfsAdicionar(){
	var uf = $("#regiao_uf_pool").val();
	if(!uf){
		alert("Selecione uma UF para adicionar.");
		return false;
	}
	return regiaoUfsAdicionarValor(uf, false);
}
function regiaoUfsAdicionarValor(uf, silencioso){
	var valor = $.trim(String(uf)).toUpperCase();
	if(valor===""){
		return false;
	}
	var existe = false;
	$(".regiao-ufs-item").each(function(){
		if($(this).attr("data-uf")===valor){
			existe = true;
		}
	});
	if(existe){
		if(!silencioso){
			alert("Essa UF ja esta vinculada a regiao.");
		}
		return false;
	}
	$("#regiao-ufs-vinculadas").append(
		"<div class='regiao-ufs-item' data-uf=\"" + regiaoUfsEscape(valor) + "\">"
		+ "<span class='regiao-ufs-nome'>" + regiaoUfsEscape(valor) + "</span>"
		+ "<button type='button' class='regiao-ufs-remover' onclick='regiaoUfsRemover(this);'>Remover</button>"
		+ "</div>"
	);
	regiaoUfsAtualizarValor();
	if(!silencioso){
		$("#regiao_uf_pool").val("");
	}
	return false;
}
function regiaoUfsRemover(botao){
	$(botao).closest(".regiao-ufs-item").remove();
	regiaoUfsAtualizarValor();
	return false;
}
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
function fc_edit_metas(valor1,valor2){
var metaAjaxUrl = window.arsMetaAjaxUrl || "ajax/metas";

		var tt = "";
		var tu = "";
		var resetMetaDialog = function(){
			$("#meta_id").val("");
			$("#meta_name_1").val("");
			$("#regiao_id_1").val("");
			$("#meta_valor_1").val("");
			$("#meta_valor_1").attr("readonly",false);
			$("#def_sem_1").attr("checked",false);
			$("#sem1_valor_1").val("").hide();
			$("#sem2_valor_1").val("").hide();
			$("#sem3_valor_1").val("").hide();
			$("#sem4_valor_1").val("").hide();
			$("#sem5_valor_1").val("").hide();
			$("#metas_1").html("");
			$("#metas_num").val("1");
			$("#inp1_1").show();
		};
		var abrirDialogMeta = function(){
			$( "#dialog-edit-metas" ).dialog({
				title: tt,
				modal: true,
				autoOpen: true,
				height: 400,
				width: 1080,
				buttons: {
					Salvar: function() {
						var mdados="";
						var invalido = false;
						$('.cls_meta').each(function(){
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
						var metam="";
						var seman="";
						var defse="";
						var numes=0;
						$('.cls_metas2').each(function(){
							numes++;
							metam += $(this).attr("name")+"="+escape($(this).val())+"&"+"regiao_id_"+numes+"="+escape($("#regiao_id_"+numes).val())+"&"+"meta_valor_"+numes+"="+escape($("#meta_valor_"+numes).val())+"&";
							if($("#def_sem_"+numes).prop("checked")==true){
								defse += "def_sem_"+numes+"=Y&";
							}else{
								defse += "def_sem_"+numes+"=N&";
							}
							seman += "sem1_valor_"+numes+"="+escape($("#sem1_valor_"+numes).val())+"&";
							seman += "sem2_valor_"+numes+"="+escape($("#sem2_valor_"+numes).val())+"&";
							seman += "sem3_valor_"+numes+"="+escape($("#sem3_valor_"+numes).val())+"&";
							seman += "sem4_valor_"+numes+"="+escape($("#sem4_valor_"+numes).val())+"&";
							seman += "sem5_valor_"+numes+"="+escape($("#sem5_valor_"+numes).val())+"&";
						});
						$.ajax({
						   type: "POST",
						   url:  metaAjaxUrl,
						   dataType: "json",
						   data: "flag=" + valor2 + "&response_format=json&" + mdados + metam + seman + defse + "&numes=" + numes,
						   success: function(response){
						 		if(response && response.ok===true){
									$( "#dialog-edit-metas" ).dialog( "close" );
									msgbox(valor2=="I"?"<br><table align='center'><tr><td>Meta(s) " + tu + " com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Meta editada com sucesso !</td></tr></table><br>", {
										Fechar: function(){
											$( this ).dialog( "close" );
											AbrirModulo('metas');
										}
									});
								}else if(response && response.code=="duplicate"){
									alert("Meta já cadastrada!");
								}else{
									alert((response && response.message) ? response.message : "Erro ao salvar a meta.");
								}
							},
							error: function(xhr){
								alert(LerMensagemAjaxErro(xhr, "Erro ao salvar a meta."));
							}
						});

					},
					Sair: function() {
						$( this ).dialog( "close" );
					}
				},
				close: function( event, ui ) {
					resetMetaDialog();
				}
			});
		};
		if(valor2=="I"){
			tt="Nova Meta";
			tu="criada(s)";
			$(".validateMetas").text("Crie Um " + tt);
			resetMetaDialog();
			$("#regiao_id_1").val("");
			abrirDialogMeta();
			return;
		}else if(valor2=="U"){
			tt="Editar Meta";
			tu="editada(s)";
			$(".validateMetas").text("Edite a Meta Abaixo");
		}
		$.ajax({
			type: "POST",
			url:  metaAjaxUrl,
			dataType: "json",
			data: { flag: "E", meta_id: valor1, response_format: "json" },
			success: function(response){
				if(!response || response.ok!==true || !response.data){
					alert((response && response.message) ? response.message : "Erro ao carregar os dados da meta.");
					return;
				}
				var ret = response.data || {};

				if((ret.def_sem || "N")=="Y"){
					$("#def_sem_1").attr("checked",true);
					$(".sem_1").show();
					$("#meta_valor_1").attr("readonly",true);
				}else{
					$("#def_sem_1").attr("checked",false);
					$(".sem_1").hide();
					$("#meta_valor_1").attr("readonly",false);
				}

				$("#meta_id").val(ret.meta_id || "");
				$("#banco_id").val(ret.banco_id || "");
				$("#meta_mes").val(ret.meta_mes || "");
				$("#meta_ano").val(ret.meta_ano || "");
				$("#meta_name_1").val(ret.anda_id || "");
				$("#regiao_id_1").val(ret.regiao_id || "");
				$("#meta_valor_1").val(ret.meta_valor || "");
				$("#sem1_valor_1").val(ret.sem_1 || "");
				$("#sem2_valor_1").val(ret.sem_2 || "");
				$("#sem3_valor_1").val(ret.sem_3 || "");
				$("#sem4_valor_1").val(ret.sem_4 || "");
				$("#sem5_valor_1").val(ret.sem_5 || "");

				var espe = $("#meta_name_1 option:selected").attr("especie");
				if(espe==2){
					$("#meta_valor_1").setMask("decimal");
					$(".sem_1").setMask("decimal");
					if((ret.def_sem || "N")=="Y"){
						somarMeta(1);
					}
				}else{
					$("#meta_valor_1").val(parseInt(ret.meta_valor,10) || 0);
					$("#sem1_valor_1").val(parseInt(ret.sem_1,10) || 0);
					$("#sem2_valor_1").val(parseInt(ret.sem_2,10) || 0);
					$("#sem3_valor_1").val(parseInt(ret.sem_3,10) || 0);
					$("#sem4_valor_1").val(parseInt(ret.sem_4,10) || 0);
					$("#sem5_valor_1").val(parseInt(ret.sem_5,10) || 0);
					$("#meta_valor_1").setMask("integer");
					$(".sem_1").setMask("integer");
					if((ret.def_sem || "N")=="Y"){
						somarMeta(1);
					}
				}

				abrirDialogMeta();
			},
			error: function(xhr){
				alert(LerMensagemAjaxErro(xhr, "Erro ao carregar os dados da meta."));
			}
		});
	}
	function fc_del_metas(valor1,valor2){
var metaAjaxUrl = window.arsMetaAjaxUrl || "ajax/metas";
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar a meta <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  metaAjaxUrl,
					dataType: "json",
					data: { flag: "D", meta_id: valor1, response_format: "json" },
					success: function(response){
						$( this ).dialog( "close" );
						if(response && response.ok===true){
							msgbox("<br><table align='center'><tr><td>Meta deletada com sucesso !</td></tr></table><br>",{
								Fechar: function(){
									$( this ).dialog( "close" );
									AbrirModulo('metas');
								}
							});
						}else{
							alert((response && response.message) ? response.message : "Erro ao excluir a meta.");
						}
					},
					error: function(xhr){
						alert(LerMensagemAjaxErro(xhr, "Erro ao excluir a meta."));
					}
				});
			},
			"Não": function(){
				$( this ).dialog( "close" );
			}
		});
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
			"NÃ£o": function(){ $( this ).dialog( "close" ); }
		});
	}
	function fc_edit_regiao(valor1,valor2){
var regiaoAjaxUrl = window.arsRegionAjaxUrl || "ajax/regioes";
		var regiaoJsonData = function(extra){
			return $.extend({ response_format: "json" }, extra || {});
		};
		var tt = "";
		var tu = "";
		if(valor2=="I"){
			tt="Nova Regiao";
			tu="criada";
			$(".validateRegiao").text("Crie uma nova regiao");
			regiaoUfsReset();
			$('.cls_regiao').each(function() { $(this).val(""); });
			$("#regiao_status").val("Y");
			$("#regiao_slug").data("manual", false);
		}else if(valor2=="U"){
			tt="Editar Regiao";
			tu="editada";
			$(".validateRegiao").text("Edite a regiao abaixo");
		}
		var abrirDialogRegiao = function(){
			$("#dialog-edit-regiao").dialog({
				title: tt,
				modal: true,
				autoOpen: true,
				height: 420,
				width: 580,
				buttons: {
					Salvar: function() {
						var mdados = "";
						var invalido = false;
						$('.cls_regiao').each(function(){
							if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
								alert("O campo " + $(this).attr("title") + " e obrigatorio ");
								$(this).focus();
								invalido = true;
								return false;
							}
							mdados += $(this).attr("name")+"="+escape($(this).val())+"&";
						});
						if(invalido){ return false; }
						if($(".regiao-ufs-item").length===0){
							alert("Selecione ao menos uma UF para a regiao.");
							$("#regiao_uf_pool").focus();
							return false;
						}
						$.ajax({
							type: "POST",
							url:  regiaoAjaxUrl,
							dataType: "json",
							data: "flag=" + valor2 + "&response_format=json&" + mdados,
							success: function(response){
								if(response && response.ok===true){
									$( "#dialog-edit-regiao" ).dialog( "close" );
									msgbox("<br><table align='center'><tr><td>Regiao " + tu + " com sucesso !</td></tr></table><br>", {
										Fechar: function(){ $( this ).dialog( "close" ); AbrirModulo('regioes'); }
									});
								}else if(response && response.code=="duplicate"){
									alert("Slug de regiao ja cadastrado!");
								}else{
									alert((response && response.message) ? response.message : "Erro ao salvar a regiao.");
								}
							}
						});
					},
					Sair: function() { $( this ).dialog( "close" ); }
				},
				close: function(){
					$('.cls_regiao').each(function() { $(this).val(""); });
					$("#regiao_slug").data("manual", false);
					regiaoUfsReset();
				}
			});
		};
		if(valor2=="I"){
			abrirDialogRegiao();
			return;
		}
		$.ajax({
			type: "POST",
			url:  regiaoAjaxUrl,
			dataType: "json",
			data: regiaoJsonData({ flag: "E", regiao_id: valor1 }),
			success: function(response){
				if(!response || response.ok!==true || !response.data){
					alert((response && response.message) ? response.message : "Erro ao carregar os dados da regiao.");
					return;
				}
				var ret = response.data || {};
				regiaoUfsReset();
				$("#regiao_id_edit").val(ret.regiao_id || "");
				$("#regiao_nome").val(ret.regiao_nome || "");
				$("#regiao_slug").val(ret.regiao_slug || "");
				$("#regiao_slug").data("manual", true);
				$("#regiao_status").val(ret.regiao_status || "Y");
				var ufs = ret.ufs || [];
				for(var i=0;i<ufs.length;i++){
					regiaoUfsAdicionarValor(ufs[i], true);
				}
				abrirDialogRegiao();
			}
		});
	}
	function fc_del_regiao(valor1,valor2){
var regiaoAjaxUrl = window.arsRegionAjaxUrl || "ajax/regioes";
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar a regiao <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  regiaoAjaxUrl,
					dataType: "json",
					data: { flag: "D", regiao_id: valor1, response_format: "json" },
					success: function(response){
						$( this ).dialog( "close" );
						if(response && response.ok===true){
							msgbox("<br><table align='center'><tr><td>Regiao deletada com sucesso !</td></tr></table><br>",{
								Fechar: function(){ $( this ).dialog( "close" ); AbrirModulo('regioes'); }
							});
						}else if(response && response.code=="linked_users"){
							alert("Nao e possivel excluir a regiao porque existem usuarios vinculados a ela.");
						}else{
							alert((response && response.message) ? response.message : "Erro ao excluir a regiao.");
						}
					}
				});
			},
			"Nao": function(){ $( this ).dialog( "close" ); }
		});
	}
	function fc_del_usu(valor1,valor2){
var userAjaxUrl = window.arsUserAjaxUrl || "ajax/usuarios";
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar o usuÃ¡rio <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  userAjaxUrl,
					dataType: "json",
					data: { flag: "D", id_usu: valor1, response_format: "json" },
					success: function(response){
						$( this ).dialog( "close" );
						if(response && response.ok===true){
							msgbox("<br><table align='center'><tr><td>UsuÃ¡rio deletado com sucesso !</td></tr></table><br>",{
								Fechar: function(){
									$( this ).dialog( "close" );
									AbrirModulo('usuarios');
								}
							});
						}else{
							alert((response && response.message) ? response.message : "Erro ao excluir o usuÃ¡rio.");
						}
					},
					error: function(xhr){
						alert(LerMensagemAjaxErro(xhr, "Erro ao excluir o usuÃ¡rio."));
					}
				});
				//AbrirModulo('usuarios');
			},
			"NÃ£o": function(){
				$( this ).dialog( "close" );
			}
		});
	}
	function fc_del_sem(valor1,valor2){
var weekAjaxUrl = window.arsWeekAjaxUrl || "ajax/semanas";
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar a semana do mÃªs: <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  weekAjaxUrl,
					dataType: "json",
					data: { flag: "D", id_sem: valor1, response_format: "json" },
					success: function(response){
						$( this ).dialog( "close" );
						if(response && response.ok===true){
							msgbox("<br><table align='center'><tr><td>Semana deletada com sucesso !</td></tr></table><br>",{
								Fechar: function(){
									$( this ).dialog( "close" );
									AbrirModulo('semanas');
								}
							});
						}else{
							alert((response && response.message) ? response.message : "Erro ao excluir a semana.");
						}
					},
					error: function(xhr){
						alert(LerMensagemAjaxErro(xhr, "Erro ao excluir a semana."));
					}
				});
				//AbrirModulo('usuarios');
			},
			"NÃ£o": function(){
				$( this ).dialog( "close" );
			}
		});
	}

	function fc_del_setor(valor1,valor2){
var sectorAjaxUrl = window.arsSectorAjaxUrl || "ajax/setores";
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar o setor <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  sectorAjaxUrl,
					data: "flag=D&id_setor=" + valor1,
					success: function(retorno_ajax){
						$( this ).dialog( "close" );
						if(retorno_ajax==1){
							msgbox("<br><table align='center'><tr><td>Setor deletado com sucesso !</td></tr></table><br>",{
								Fechar: function(){
									$( this ).dialog( "close" );
									AbrirModulo('setores');
								}
							});
						}else{
							alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao ***REMOVED***istrador)");
						}
					}
				});
				//AbrirModulo('usuarios');
			},
			"NÃ£o": function(){
				$( this ).dialog( "close" );
			}
		});
	}

	function cpfcnpj(valor){
		$("#"+valor).attr("alt",$("input[@TIPOPES=radioGroup]:checked").val());
		$('input:text').setMask();
	}

	function validaCaractaer(pEvent){
		if(navigator.appName.indexOf('Internet Explorer')>0){
			if ((pEvent.keyCode<97 || pEvent.keyCode>122)&&(pEvent.keyCode<48 || pEvent.keyCode>57)){
				alert("Caractere nÃ£o aceito para esse campo");
				pEvent.keyCode = 0;
			}
		}else{
			if ((pEvent.which<97 || pEvent.which>122)&&(pEvent.which<48 || pEvent.which>57)) {
				alert("Caractere nÃ£o aceito para esse campo");
				pEvent.which = 0;
			}
		}
	}

function diasemana(valor){
	if(valor.value.length==10){
		var semana = ["domingo", "segunda-feira", "terÃ§a-feira","quarta-feira","quinta-feira","sexta-feira","sÃ¡bado"];
		var data = $(valor).val();
		var arr = data.split("/").reverse();
		var teste = new Date(arr[0], arr[1] - 1, arr[2]);
		var dia = teste.getDay();
		$(valor).val(data + " (" + semana[dia] +")");
	}
}
function fc_teste_senha(valor1,valor2,valor3){

	if(valor1!=valor2){
		$("#senha_usu1").css("border","1px solid red");
		$("#senha_usu2").css("border","1px solid red");
		return "Senhas nÃ£o sÃ£o iguais";
	}else if(valor1=="" && valor3=="I"){
		$("#senha_usu1").css("border","1px solid red");
		$("#senha_usu2").css("border","1px solid red");
		return "Informe sua senha!";
	}else{
		if((valor1!="" && valor3=="U" && valor1.length<4) || (valor1.length<4 && valor3=="I")){
			$("#senha_usu1").css("border","1px solid red");
			$("#senha_usu2").css("border","1px solid red");
			return "Sua senha deve conter no mÃ­nimo 4 caracteres!";
		}else{
			var er = /[A-Za-z0-9_\-\.]{4,}/;
			if((er.test(valor1)==false && valor1!="" && valor3=="U") || (er.test(valor1)==false && valor3=="I")){
				$("#senha_usu1").css("border","1px solid red");
				$("#senha_usu2").css("border","1px solid red");
				return "Senha contÃ©m caractere invÃ¡lido!";
			}else{
				return "";
			}
		}
	}
}

function validaEmail(mail){
	var er = /^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/;
	if(mail == ""){
		$("#email_usu").css("border","1px solid red");
		return "Informe seu e-mail!";
	}else if(er.test(mail) == false){
		$("#email_usu").css("border","1px solid red");
		return "E-mail invÃ¡lido!";
	}else{
		return "";
	}
}

function addMes(data,mes){
	var minhaData = moment(data,"D/M/YYYY").add('months', mes);
	return moment(minhaData).format('DD/MM/YYYY');
}

function regiaoSlugify(valor){
	return String(valor || "")
		.toLowerCase()
		.replace(/[Ã¡Ã Ã£Ã¢Ã¤]/g, "a")
		.replace(/[Ã©Ã¨ÃªÃ«]/g, "e")
		.replace(/[Ã­Ã¬Ã®Ã¯]/g, "i")
		.replace(/[Ã³Ã²ÃµÃ´Ã¶]/g, "o")
		.replace(/[ÃºÃ¹Ã»Ã¼]/g, "u")
		.replace(/Ã§/g, "c")
		.replace(/[^a-z0-9]+/g, "-")
		.replace(/^-+|-+$/g, "");
}

$(function(){
	$("#regiao_nome").on("input", function(){
		if($("#regiao_slug").data("manual")){
			return;
		}
		$("#regiao_slug").val(regiaoSlugify($(this).val()));
	});

	$("#regiao_slug").on("input", function(){
		var valor = $(this).val();
		$(this).data("manual", $.trim(valor) !== "");
	});
});

function inserir_cli(valor,stt){
	return clienteCarteirasAdicionar();
}
function inserir_anda(valor,stt){
	var crt = parseFloat($("#andam_num").val());
	var atr = (32 * crt) +260;
	if(stt==1){
		crt = crt+1;
		$("#andam_"+(crt-1)).html(
		"<select class='cls_andam input-default' name='andam_name_"+crt+"' style='width:360px;height:22px'>"+valor+"</select>" +
		"<button id='inp1_"+crt+"' class='bts' onclick='inserir_anda($(\"#andam_name_1\").html(),1);'>+</button>" +
		"<button id='inp0_"+crt+"' class='bts' onclick='inserir_anda($(\"#andam_name_1\").html(),0);'>-</button>" +
		"<div id='andam_"+crt+"'></div>");
		$("#inp1_"+(crt-1)).hide();
		$("#inp0_"+(crt-1)).hide();
	}else if(stt==0){
		crt = crt-1;
		$("#andam_"+crt).html(" ");
		$("#inp1_"+crt).show();
		$("#inp0_"+crt).show();
	}
	$("#tb_dialog").css("height",atr+"px");
	$("#andam_num").val(crt);
}

function inserir_metas(valor,stt){
	var crt = parseFloat($("#metas_num").val());
	var atr = (32 * crt) +70;
	if(stt==1){
		crt = crt+1;
		$("#metas_"+(crt-1)).html(
		"<div style='float:left'>" +
		"<select class='cls_metas2 input-default' name='meta_name_"+crt+"' onchange='my_especie("+crt+");' style='width:260px;height:22px;float:left'>"+valor+"</select>" +
		"<select class='cls_meta_regiao input-default' name='regiao_id_"+crt+"' id='regiao_id_"+crt+"' style='width:160px;height:22px;float:left'>"+$("#regiao_id_1").html()+"</select>" +
		"<input type='text' class='cls_meta' name='meta_valor_"+crt+"' id='meta_valor_"+crt+"' value='' obrigatorio='1' style='width:120px;float:left'/>" +
		"<input type='checkbox' class='cls_meta' name='def_sem_"+crt+"' id='def_sem_"+crt+"' onclick='definir_sem(this,"+crt+");' value='' title='Definir manualmente' style='width:20px;'>" +
		"<input type='text' class='cls_meta sem_"+crt+"' name='sem1_valor_"+crt+"' id='sem1_valor_"+crt+"' value='' onkeypress='somarMeta("+crt+")' onblur='somarMeta("+crt+")' style='display:none;width:70px;float:left'/>" +
		"<input type='text' class='cls_meta sem_"+crt+"' name='sem2_valor_"+crt+"' id='sem2_valor_"+crt+"' value='' onkeypress='somarMeta("+crt+")' onblur='somarMeta("+crt+")' style='display:none;width:70px;float:left'/>" +
		"<input type='text' class='cls_meta sem_"+crt+"' name='sem3_valor_"+crt+"' id='sem3_valor_"+crt+"' value='' onkeypress='somarMeta("+crt+")' onblur='somarMeta("+crt+")' style='display:none;width:70px;float:left'/>" +
		"<input type='text' class='cls_meta sem_"+crt+"' name='sem4_valor_"+crt+"' id='sem4_valor_"+crt+"' value='' onkeypress='somarMeta("+crt+")' onblur='somarMeta("+crt+")' style='display:none;width:70px;float:left'/>" +
		"<input type='text' class='cls_meta sem_"+crt+"' name='sem5_valor_"+crt+"' id='sem5_valor_"+crt+"' value='' onkeypress='somarMeta("+crt+")' onblur='somarMeta("+crt+")' style='display:none;width:70px;float:left'/>" +
		"<button id='inp1_"+crt+"' class='bts' onclick='inserir_metas($(\"#meta_name_1\").html(),1);' style='float:left'>+</button>" +
		"<button id='inp0_"+crt+"' class='bts' onclick='inserir_metas($(\"#meta_name_1\").html(),0);' style='float:left'>-</button>" +
		"</div>" +
		"<div id='metas_"+crt+"'></div>");
		$("#inp1_"+(crt-1)).hide();
		$("#inp0_"+(crt-1)).hide();
	}else if(stt==0){
		crt = crt-1;
		$("#metas_"+crt).html(" ");
		$("#inp1_"+crt).show();
		$("#inp0_"+crt).show();
	}
	$("#tb_dialog").css("height",atr+"px");
	$("#metas_num").val(crt);
}
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

function definir_sem(valor1,valor2){
	if($(valor1).prop("checked")==true){
		$(".sem_"+valor2).show();
		$("#meta_valor_"+valor2).attr("readonly",true);
		somarMeta(valor2);
	}else if($(valor1).prop("checked")==false){
		$(".sem_"+valor2).hide();
		$("#meta_valor_"+valor2).attr("readonly",false);
	}
}

function somarMeta(valor2){
	var espe = $("#meta_name_"+valor2+" option:selected").attr("especie");

	var sem1 = $("#sem1_valor_"+valor2).val();
	var sem2 = $("#sem2_valor_"+valor2).val();
	var sem3 = $("#sem3_valor_"+valor2).val();
	var sem4 = $("#sem4_valor_"+valor2).val();
	var sem5 = $("#sem5_valor_"+valor2).val();

	if(espe==2){
		var mval = parseFloat((sem1?sem1.replace(".","").replace(",","."):0));
		var mva2 = parseFloat((sem2?sem2.replace(".","").replace(",","."):0));
		var mva3 = parseFloat((sem3?sem3.replace(".","").replace(",","."):0));
		var mva4 = parseFloat((sem4?sem4.replace(".","").replace(",","."):0));
		var mva5 = parseFloat((sem5?sem5.replace(".","").replace(",","."):0));
		var mvat = mval+mva2+mva3+mva4+mva5;
		var formatter = new Intl.NumberFormat("pt-BR", {style: "currency",currency: "BRL"});
		$("#meta_valor_"+valor2).val(formatter.format(mvat).replace("R$",""));
	}else{
		var mval = parseFloat((sem1?sem1:0));
		var mva2 = parseFloat((sem2?sem2:0));
		var mva3 = parseFloat((sem3?sem3:0));
		var mva4 = parseFloat((sem4?sem4:0));
		var mva5 = parseFloat((sem5?sem5:0));
		var mvat = mval+mva2+mva3+mva4+mva5;
		$("#meta_valor_"+valor2).val(mvat);
	}
}


















