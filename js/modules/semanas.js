function fc_edit_sem(valor1,valor2){
	var weekResourceBaseUrl = window.arsWeekResourceBaseUrl || "admin/semanas";
	var weekResourceUrl = function(id){
		return weekResourceBaseUrl + "/" + id;
	};
	var tt = "";
	var tu = "";
	if(valor2=="I"){
		tt="Nova Semana";
		tu="criada";
		$(".validateTips").text("Crie Um " + tt);
	}else if(valor2=="U"){
		tt="Editar Semana";
		tu="editada";
		$(".validateTips").text("Edite a semana abaixo");
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
					var payload = {};
					$('.cls_sem').each(function(){
						payload[$(this).attr("name")] = $(this).val();
					});
					arsJsonSubmit(
						valor2=="I" ? "POST" : "PUT",
						valor2=="I" ? weekResourceBaseUrl : weekResourceUrl($("#id_sem").val()),
						payload,
						"Erro ao salvar a semana.",
						function(){
							$("#dialog-edit-sem").dialog("close");
							msgbox(valor2=="I"?"<br><table align='center'><tr><td>Semana " + tu + " com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Campo editado com sucesso !</td></tr></table><br>", {
								Fechar: function(){
									$(this).dialog("close");
									AbrirModulo('semanas');
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

	arsJsonGet(weekResourceUrl(valor1), "Erro ao carregar os dados da semana.", function(ret){
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
	});
}

function fc_del_sem(valor1,valor2){
	var weekResourceBaseUrl = window.arsWeekResourceBaseUrl || "admin/semanas";
	msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar a semana do mês: <b>" + valor2 + "</b> ?</td></tr></table><br>",{
		"Sim": function(){
			var dialog = $(this);
			arsJsonSubmit("DELETE", weekResourceBaseUrl + "/" + valor1, {}, "Erro ao excluir a semana.", function(){
				dialog.dialog("close");
				msgbox("<br><table align='center'><tr><td>Semana deletada com sucesso !</td></tr></table><br>",{
					Fechar: function(){
						$(this).dialog("close");
						AbrirModulo('semanas');
					}
				});
			});
		},
		"Não": function(){
			$(this).dialog("close");
		}
	});
}
