function fc_edit_setor(valor1, valor2){
	var sectorResourceBaseUrl = window.arsSectorResourceBaseUrl || "***REMOVED***/setores";
	var sectorResourceUrl = function(id){
		return sectorResourceBaseUrl + "/" + id;
	};
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

	var abrirDialogSetor = function(){
		$("#dialog-edit-setor").dialog({
			title: tt,
			modal: true,
			autoOpen: true,
			height: 440,
			width: 450,
			buttons: {
				Salvar: function() {
					var payload = {};
					var invalido = false;
					$('.cls_setor').each(function(){
						if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
							alert("O campo " + $(this).attr("title") + " e obrigatorio ");
							$(this).focus();
							invalido = true;
							return false;
						}
						payload[$(this).attr("name")] = $(this).val();
					});
					if(invalido){
						return false;
					}

					arsJsonSubmit(
						valor2=="I" ? "POST" : "PUT",
						valor2=="I" ? sectorResourceBaseUrl : sectorResourceUrl($("#id_setor").val()),
						payload,
						"Erro ao salvar o setor.",
						function(){
							$("#dialog-edit-setor").dialog("close");
							msgbox(valor2=="I" ? "<br><table align='center'><tr><td>Setor " + tu + " com sucesso !</td></tr></table><br>" : "<br><table align='center'><tr><td>Campo editado com sucesso !</td></tr></table><br>", {
								Fechar: function(){
									$(this).dialog("close");
									AbrirModulo('setores');
								}
							});
						}
					);
				},
				Sair: function() {
					$(this).dialog("close");
				}
			},
			close: function(){
				$('.cls_setor').each(function() {
					$(this).val("");
				});
			}
		});
	};

	if(valor2=="I"){
		$('.cls_setor').each(function() {
			$(this).val("");
		});
		abrirDialogSetor();
		return;
	}

	arsJsonGet(sectorResourceUrl(valor1), "Erro ao carregar os dados do setor.", function(ret){
		$("#id_setor").val(ret.area_id || "");
		$("#nome_setor").val(ret.area_nome || "");
		abrirDialogSetor();
	});
}

function fc_del_setor(valor1, valor2){
	var sectorResourceBaseUrl = window.arsSectorResourceBaseUrl || "***REMOVED***/setores";
	msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar o setor <b>" + valor2 + "</b> ?</td></tr></table><br>",{
		"Sim": function(){
			var dialog = $(this);
			arsJsonSubmit("DELETE", sectorResourceBaseUrl + "/" + valor1, {}, "Erro ao excluir o setor.", function(){
				dialog.dialog("close");
				msgbox("<br><table align='center'><tr><td>Setor deletado com sucesso !</td></tr></table><br>",{
					Fechar: function(){
						$(this).dialog("close");
						AbrirModulo('setores');
					}
				});
			});
		},
		"Não": function(){
			$(this).dialog("close");
		}
	});
}
