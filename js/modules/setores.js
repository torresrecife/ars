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
		dataType: "json",
		data: { flag: "E", id_setor: valor1 },
		success: function(response){
			if(!response || response.ok!==true || !response.data){
				alert((response && response.message) ? response.message : "Erro ao carregar os dados do setor.");
				return;
			}
			var ret = response.data || {};
			$("#id_setor").val(ret.area_id || "");
			$("#nome_setor").val(ret.area_nome || "");

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
						   dataType: "json",
						   data: "flag=" + valor2 + "&response_format=json&" + mdados,
						   success: function(response){
								if(response && response.ok===true){
									$( "#dialog-edit-setor" ).dialog( "close" );
									msgbox(valor2=="I"?"<br><table align='center'><tr><td>Setor " + tu + " com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Campo editado com sucesso !</td></tr></table><br>", {
										Fechar: function(){
											$( this ).dialog( "close" );
											AbrirModulo('setores');
										}
									});
								}else if(response && response.code=="duplicate"){
									alert("Setor jÃ¡ cadastrado!");
								}else{
									alert((response && response.message) ? response.message : "Erro ao salvar o setor.");
								}
							},
							error: function(xhr){
								alert(LerMensagemAjaxErro(xhr, "Erro ao salvar o setor."));
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

function fc_del_setor(valor1,valor2){
var sectorAjaxUrl = window.arsSectorAjaxUrl || "ajax/setores";
	msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar o setor <b>" + valor2 + "</b> ?</td></tr></table><br>",{
		"Sim": function(){
			$.ajax({
				type: "POST",
				url:  sectorAjaxUrl,
				dataType: "json",
				data: { flag: "D", id_setor: valor1, response_format: "json" },
				success: function(response){
					$( this ).dialog( "close" );
					if(response && response.ok===true){
						msgbox("<br><table align='center'><tr><td>Setor deletado com sucesso !</td></tr></table><br>",{
							Fechar: function(){
								$( this ).dialog( "close" );
								AbrirModulo('setores');
							}
						});
					}else{
						alert((response && response.message) ? response.message : "Erro ao excluir o setor.");
					}
				},
				error: function(xhr){
					alert(LerMensagemAjaxErro(xhr, "Erro ao excluir o setor."));
				}
			});
		},
		"Não": function(){
			$( this ).dialog( "close" );
		}
	});
}
