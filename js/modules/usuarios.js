function fc_edit_usu(valor1,valor2){
	var userResourceBaseUrl = window.arsUserResourceBaseUrl || "***REMOVED***/usuarios";
	var userResourceUrl = function(id){
		return userResourceBaseUrl + "/" + id;
	};
	var tt = "";
	var successMessage = "";
	if(valor2=="I"){
		tt=arsTranslate("New User");
		successMessage=arsTranslate("User created successfully.");
		$(".validateTips").text(arsTranslate("Create a new user"));
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
		tt=arsTranslate("Edit User");
		successMessage=arsTranslate("Field updated successfully.");
		$(".validateTips").text(arsTranslate("Edit the user below"));
	}

	var abrirDialogUsuario = function(){
		$("#dialog-edit-usu").dialog({
			title: tt,
			modal: true,
			autoOpen: true,
			height: 470,
			width: 520,
			buttons: {
				[arsTranslate("Save")]: function() {
					var invalido = false;
					$('.cls_usu').each(function(){
						if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
							alert(arsFormat("The field :field is required.", {field: $(this).attr("title")}));
							$(this).focus();
							invalido = true;
							return false;
						}
					});
					if(invalido){
						return false;
					}
					if($('.cls_usuario_cliente_input').length==0){
						alert(arsTranslate("Select at least one client for the user."));
						$("#banco_usu_pool").focus();
						return false;
					}
					var clientes = [];
					var regioes = [];
					$('.usuario-clientes-item').each(function(){
						var clienteId = $(this).attr("data-cliente-id");
						if(clienteId){
							clientes.push(clienteId);
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
						return false;
					}
					if(dado_senha!=""){
						alert(dado_senha);
						return false;
					}

					var payload = {};
					$('.cls_usu').each(function(){
						payload[$(this).attr("name")] = $(this).val();
					});
					payload.banco_neo = clientes.join(",");
					payload.regiao_neo = regioes.join(",");

					arsJsonSubmit(
						valor2=="I" ? "POST" : "PUT",
						valor2=="I" ? userResourceBaseUrl : userResourceUrl($("#id_usu").val()),
						payload,
						arsTranslate("Error saving user."),
						function(){
							$("#dialog-edit-usu").dialog("close");
							msgbox("<br><table align='center'><tr><td>" + successMessage + "</td></tr></table><br>", {
								[arsTranslate("Close")]: function(){
									$( this ).dialog( "close" );
									AbrirModulo('usuarios');
								}
							});
						}
					);
				},
				[arsTranslate("Exit")]: function() {
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

	arsJsonGet(userResourceUrl(valor1), arsTranslate("Error loading user data."), function(ret){
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
	});
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
		alert(arsTranslate("Select a client to add."));
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
			alert(arsTranslate("This client is already linked to the user."));
		}
		return false;
	}
	$("#usuario-clientes-vinculados").append(
		"<div class='usuario-clientes-item' data-cliente-id=\"" + usuarioClientesEscape(id) + "\">"
		+ "<span class='usuario-clientes-nome'>" + usuarioClientesEscape(clienteNome) + "</span>"
		+ "<button type='button' class='usuario-clientes-remover' onclick='usuarioClientesRemover(this);'>" + arsTranslate("Remove") + "</button>"
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
		alert(arsTranslate("Select a region to add."));
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
			alert(arsTranslate("Standard user can have only one linked region."));
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
			alert(arsTranslate("This region is already linked to the user."));
		}
		return false;
	}
	$("#usuario-regioes-vinculadas").append(
		"<div class='usuario-regioes-item' data-regiao-id=\"" + usuarioRegioesEscape(id) + "\">"
		+ "<span class='usuario-regioes-nome'>" + usuarioRegioesEscape(regiaoNome) + "</span>"
		+ "<button type='button' class='usuario-regioes-remover' onclick='usuarioRegioesRemover(this);'>" + arsTranslate("Remove") + "</button>"
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
function fc_del_usu(valor1,valor2){
	var userResourceBaseUrl = window.arsUserResourceBaseUrl || "***REMOVED***/usuarios";
	msgbox("<br><table align='center'><tr><td style='font-size:8pt'>" + arsFormat("Do you really want to delete the user :name?", {name: "<b>" + valor2 + "</b>"}) + "</td></tr></table><br>",{
		[arsTranslate("Yes")]: function(){
			var dialog = $(this);
			arsJsonSubmit("DELETE", userResourceBaseUrl + "/" + valor1, {}, arsTranslate("Error deleting user."), function(){
				dialog.dialog("close");
				msgbox("<br><table align='center'><tr><td>" + arsTranslate("User deleted successfully.") + "</td></tr></table><br>",{
					[arsTranslate("Close")]: function(){
						$( this ).dialog( "close" );
						AbrirModulo('usuarios');
					}
				});
			});
		},
		[arsTranslate("No")]: function(){
			$( this ).dialog( "close" );
		}
	});
}
