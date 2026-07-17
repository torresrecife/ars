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
			$(".validateTips").text("Edite o usuário Abaixo");
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
			},
			"Não": function(){
				$( this ).dialog( "close" );
			}
		});
}
