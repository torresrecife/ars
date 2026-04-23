
$(function() {
	$('.date-picker').datepicker( {
		dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
		dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
		dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
		monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
		monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
		nextText: 'Próximo',
		prevText: 'Anterior',
		closeText: 'OK',
		currentText: 'Mês atual',
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
function EnviarDados(frm,hid,are,fla){
	if($("#startDate").val()=="" && (hid==2 || hid==4 || hid==14)){
		$("#startDate").css("border","1px solid red");
		$("#obg_date").fadeIn();
		$("#obg_date").html("Inseir o mês / ano!");
		setTimeout(function(){
			$("#startDate").css("border","1px solid #ccc");
			$("#obg_date").fadeOut();
		}, 3000);		
	}else{
		$("#hid_send").val(hid);
		$("#hid_area").val(are);		
		$("#hid_flag").val(fla);		
		$("#form_ars").attr("action",frm);
		$("#form_ars").attr("target","");
		$("#form_ars").submit();
	}
}
	
	//função editar usuário
	function fc_edit_usu(valor1,valor2){
		//alert(1);
		
		var tt = "";
		var tu = "";
		if(valor2=="I"){
			tt="Novo Usuário";
			tu="criado";
			$(".validateTips").text("Crie Um " + tt);
		}else if(valor2=="U"){
			tt="Editar Usuário";
			tu="editado";
			$(".validateTips").text("Edite o Usuário Abaixo");
		}
	
		$.ajax({
			type: "POST",
			url:  "inc/ajax_usu.php",
			data: "flag=E&id_usu=" + valor1,
			success: function(retorno_ajax){
				var ret = retorno_ajax.split("-|-");
				
				$("#id_usu").val(ret[0]);
				$("#nome_usu").val(ret[2]);
				$("#login_usu").val(ret[3]);
				$("#email_usu").val(ret[5]);
				$("#nivel_usu").val(ret[6]);
				$("#nivel_usu").val(ret[6]);
				$("#setor_usu option[value="+ret[9]+"]").attr("selected","selected");
				if(valor2=="U"){
					sel_tipo(1,ret[9]);
				}
				$("#status_usu").val(ret[11]);
				$("#dialog-edit-usu").dialog({
					title: tt,
					modal: true,
					autoOpen: true,
					height: 470,
					width: 450,
					buttons: {
						Salvar: function() {
							var mdados="";
							$('.cls_usu').each(function(){
								if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
									alert("O campo " + $(this).attr("title") + " é obrigatório ");
									$(this).focus();
									return false;
								}
								mdados += $(this).attr("name")+"="+escape($(this).val())+"&";
							});
							///pega os clientes///////////
							var usus="";
							var numes=0;
							$('.cls_usu2').each(function(){
								if((numes++)>0){
									usus += ",";
								}
								usus += escape($(this).val());
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
								   url:  "inc/ajax_usu.php",
								   data: "flag=" + valor2 + "&" + mdados + "&banco_neo=" + usus,
								   success: function(retorno_ajax){
										if(retorno_ajax==1){
											$( "#dialog-edit-usu" ).dialog( "close" );
											msgbox(valor2=="I"?"<br><table align='center'><tr><td>Usuário " + tu + " com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Campo editado com sucesso !</td></tr></table><br>", {
												Fechar: function(){
													$( this ).dialog( "close" );
													EnviarDados('index.php','8','');
												}
											});
										}else if(retorno_ajax==2){
											alert("Usuário já cadastrado!");
										}else{
											alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
										}
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
					}
				});
			}
		});
	}
	//função editar semana
	function fc_edit_sem(valor1,valor2){
		
		var tt = "";
		var tu = "";
		if(valor2=="I"){
			tt="Nova Semana";
			tu="criada";
			$(".validateTips").text("Crie Um " + tt);
		}else if(valor2=="U"){
			tt="Editar Semana";
			tu="editada";
			$(".validateTips").text("Edite o Usuário Abaixo");
		}
	
		$.ajax({
			type: "POST",
			url:  "inc/ajax_sem.php",
			data: "flag="+(valor2=="U"?"E":"")+"&id_sem=" + valor1,
			success: function(retorno_ajax){
				var ret = retorno_ajax.split("-|-");
				
				$("#id_sem").val(ret[0]);
				
				$("#mes_sem").val(ret[1]);
				$("#ano_sem").val(ret[2]);
				
				$("#ini1_sem").val(ret[3]);
				$("#fim1_sem").val(ret[4]);
				
				$("#ini2_sem").val(ret[5]);
				$("#fim2_sem").val(ret[6]);
				
				$("#ini3_sem").val(ret[7]);
				$("#fim3_sem").val(ret[8]);
				
				$("#ini4_sem").val(ret[9]);
				$("#fim4_sem").val(ret[10]);
				
				$("#ini5_sem").val(ret[11]);
				$("#fim5_sem").val(ret[12]);
				
				$("#dialog-edit-sem").dialog({
					title: tt,
					modal: true,
					autoOpen: true,
					height: 420,
					width: 450,
					buttons: {
						Salvar: function() {
							var mdados="";
							$('.cls_sem').each(function(){
								mdados += $(this).attr("name")+"="+escape($(this).val())+"&";
							});
							//alert(mdados);
							$.ajax({
							   type: "POST",
							   url:  "inc/ajax_sem.php",
							   data: "flag=" + valor2 + "&" + mdados,
							   success: function(retorno_ajax){
									if(retorno_ajax==1){
										$( "#dialog-edit-sem" ).dialog( "close" );
										msgbox(valor2=="I"?"<br><table align='center'><tr><td>Semana " + tu + " com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Campo editado com sucesso !</td></tr></table><br>", {
											Fechar: function(){
												$( this ).dialog( "close" );
												EnviarDados('index.php','15','');
											}
										});
									}else if(retorno_ajax==2){
										alert("Semana já cadastrada!");
									}else{
										alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
									}
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
			}
		});
	}
	
	//função editar setores
	function fc_edit_setor(valor1,valor2){
		
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
			url:  "inc/ajax_setor.php",
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
									alert("O campo " + $(this).attr("title") + " é obrigatório ");
									$(this).focus();
									return false;
								}
								mdados += $(this).attr("name")+"="+escape($(this).val())+"&";
							});
							
							$.ajax({
							   type: "POST",
							   url:  "inc/ajax_setor.php",
							   data: "flag=" + valor2 + "&" + mdados,
							   success: function(retorno_ajax){
									if(retorno_ajax==1){
										$( "#dialog-edit-setor" ).dialog( "close" );
										msgbox(valor2=="I"?"<br><table align='center'><tr><td>Setor " + tu + " com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Campo editado com sucesso !</td></tr></table><br>", {
											Fechar: function(){
												$( this ).dialog( "close" );
												EnviarDados('index.php','9','');
											}
										});
									}else if(retorno_ajax==2){
										alert("Setor já cadastrado!");
									}else{
										alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
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
		var tt = "";
		var tu = "";
		if(valor2=="I"){
			tt="Novo Cliente";
			tu="criado";
			$(".validateTips").text("Crie Um " + tt);
		}else if(valor2=="U"){
			tt="Editar Cliente";
			tu="editado";
			$(".validateTips").text("Edite o Cliente Abaixo");
		}
	
		$.ajax({
			type: "POST",
			url:  "inc/ajax_cliente.php",
			data: "flag=E&banco_id=" + valor1,
			success: function(retorno_ajax){
				var ret = retorno_ajax.split("-|-");
				$("#banco_id").val(ret[0]);
				$("#banco_name").val(ret[1]);
				$("#banco_cod").val(ret[2]);
				$("#banco_area option[value="+ret[4]+"]").attr("selected","selected");
				$("#banco_status option[value="+ret[5]+"]").attr("selected","selected");
				$("#banco_class").val(ret[6]);
				$("#simulador").val(ret[7]);
				$("#banco_curto").val(ret[8]);
				
				$("#dialog-edit-cliente" ).dialog({
					title: tt,
					modal: true,
					autoOpen: true,
					height: 400,
					width: 600,
					buttons: {
						Salvar: function() {
							var mdados="";
							$('.cls_cliente').each(function(){
								if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
									alert("O campo " + $(this).attr("title") + " é obrigatório ");
									$(this).focus();
									return false;
								}
								mdados += $(this).attr("name")+"="+escape($(this).val())+"&";
							});
							/////pega as carteiras///////////
							var cartei="";
							var numes=0;
							$('.cls_cliente2').each(function(){
								numes++;
								cartei += $(this).attr("name")+"="+escape($(this).val())+"&";
							});
							$.ajax({
							   type: "POST",
							   url:  "inc/ajax_cliente.php",
							   data: "flag=" + valor2 + "&" + mdados + "&cartei_num="+$('#cartei_num').val()+"&"+cartei,
							   success: function(retorno_ajax){
									if(retorno_ajax==1){
										$( "#dialog-edit-cliente" ).dialog( "close" );
										msgbox(valor2=="I"?"<br><table align='center'><tr><td>Cliente " + tu + " com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Cliente editado com sucesso !</td></tr></table><br>", {
											Fechar: function(){
												$( this ).dialog( "close" );
												EnviarDados('index.php','11','');
											}
										});
									}else if(retorno_ajax==2){
										alert("Servidor já cadastrado!");
									}else{
										alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
									}
								}
							});
							
						},
						Sair: function() {
							$( this ).dialog( "close" );
						}
					},
					close: function(){ 
						$('.cls_cliente').each(function() {
							$(this).val("");
						});
					}
				});
			}
		});
	}
	function fc_edit_andamento(valor1,valor2){
		
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
	
		$.ajax({
			type: "POST",
			url:  "inc/ajax_andamento.php",
			data: "flag=E&anda_id=" + valor1,
			success: function(retorno_ajax){
				var ret = retorno_ajax.split("-|-");
				//alert(ret[1]);
				$("#anda_id").val(ret[0]);
				$("#nome").val(ret[1]);
				$("#chave").val(ret[2]);
				$("#anda_neo").val(ret[3]);
				$("#especie").val(ret[4]);
				$("#painel option[value="+ret[5]+"]").attr("selected","selected");
				$("#titulo").val(ret[6]);
				
				$( "#dialog-edit-andamento" ).dialog({
					title: tt,
					modal: true,
					autoOpen: true,
					height: 400,
					width: 600,
					buttons:{
						Salvar: function(){
							var mdados="";
							$('.cls_andamento').each(function(){
								if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
									alert("O campo " + $(this).attr("title") + " é obrigatório ");
									$(this).focus();
									return false;
								}
								mdados += $(this).attr("name")+"="+escape($(this).val())+"&";
							});
							///pega os andamentos///////////
							var mandam="";
							var numes=0;
							$('.cls_andam').each(function(){
								if((numes++)>0){
									mandam += ",";
								}
								mandam += escape($(this).val());
							});
							$.ajax({
							   type: "POST",
							   url:  "inc/ajax_andamento.php",
							   data: "flag=" + valor2 + "&" + mdados + "&anda_neo=" + mandam,
							   success: function(retorno_ajax){
									if(retorno_ajax==1){
										$( "#dialog-edit-andamento" ).dialog( "close" );
										msgbox(valor2=="I"?"<br><table align='center'><tr><td>Andamento " + tu + " com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Campo editado com sucesso !</td></tr></table><br>", {
											Fechar: function(){
												$( this ).dialog( "close" );
												EnviarDados('index.php','12','');
											}
										});
									}else if(retorno_ajax==2){
										alert("Andamento já cadastrado!");
									}else{
										alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
									}
								}
							});
							
						},
						Sair: function() {
							$( this ).dialog( "close" );
						}
					},
					close: function(){
						$('.cls_andamento').each(function(){
							$(this).val("");
						});
					}
				});
			}
		});
	}
	function fc_edit_metas(valor1,valor2){
		
		var tt = "";
		var tu = "";
		if(valor2=="I"){
			tt="Nova Meta";
			tu="criada(s)";
			$(".validateMetas").text("Crie Um " + tt);
		}else if(valor2=="U"){
			tt="Editar Meta";
			tu="editada(s)";
			$(".validateMetas").text("Edite a Meta Abaixo");
		}
		$.ajax({
			type: "POST",
			url:  "inc/ajax_metas.php",
			data: "flag=E&meta_id=" + valor1,
			success: function(retorno_ajax){
				//alert(retorno_ajax);
				var ret = retorno_ajax.split("-|-");
				if(valor2!="I"){
					
					///verifica se as semanas foram definidas individualmente
					if(ret[6]=="Y"){
						$("#def_sem_1").attr("checked",true);
						$(".sem_1").show();
						$("#meta_valor_1").attr("readonly",true);
					}else{
						$("#def_sem_1").attr("checked",false);
						$(".sem_1").hide();
						$("#meta_valor_1").attr("readonly",false);
					}
					
					$("#meta_id").val(ret[0]);
					$("#banco_id").val(ret[1]);
					$("#meta_mes").val(ret[2]);
					$("#meta_ano").val(ret[3]);
					$("#meta_name_1").val(ret[4]);					
					$("#meta_valor_1").val(ret[5]);
					$("#sem1_valor_1").val(ret[7]);
					$("#sem2_valor_1").val(ret[8]);
					$("#sem3_valor_1").val(ret[9]);
					$("#sem4_valor_1").val(ret[10]);
					$("#sem5_valor_1").val(ret[11]);
					
					var espe = $("#meta_name_1 option:selected").attr("especie");
					if(espe==2){
						$("#meta_valor_1").setMask("decimal");
						$(".sem_1").setMask("decimal");
					}else{
						//defini os valores se estes não forem dinheiro
						$("#meta_valor_1").val(parseInt(ret[5]));
						$("#sem1_valor_1").val(parseInt(ret[7]));
						$("#sem2_valor_1").val(parseInt(ret[8]));
						$("#sem3_valor_1").val(parseInt(ret[9]));
						$("#sem4_valor_1").val(parseInt(ret[10]));
						$("#sem5_valor_1").val(parseInt(ret[11]));
						
						$("#meta_valor_1").setMask("integer");
						$(".sem_1").setMask("integer");						
					}
				}
				$( "#dialog-edit-metas" ).dialog({
					title: tt,
					modal: true,
					autoOpen: true,
					height: 400,
					width: 920,
					buttons: {
						Salvar: function() {
							var mdados="";
							$('.cls_meta').each(function(){
								if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
									alert("O campo " + $(this).attr("title") + " é obrigatório ");
									$(this).focus();
									return false;
								}
								mdados += $(this).attr("name")+"="+escape($(this).val())+"&";
							});
							/////pega as metas///////////
							var metam="";
							var seman="";
							var defse="";
							var numes=0;
							$('.cls_metas2').each(function(){
								numes++;
								metam += $(this).attr("name")+"="+escape($(this).val())+"&"+"meta_valor_"+numes+"="+escape($("#meta_valor_"+numes).val())+"&";
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
							   url:  "inc/ajax_metas.php",
							   data: "flag=" + valor2 + "&" + mdados + metam + seman + defse + "&numes=" + numes,
							   success: function(retorno_ajax){
								 	if(retorno_ajax==1){
										$( "#dialog-edit-metas" ).dialog( "close" );
										msgbox(valor2=="I"?"<br><table align='center'><tr><td>Meta(s) " + tu + " com sucesso !</td></tr></table><br>":"<br><table align='center'><tr><td>Meta editada com sucesso !</td></tr></table><br>", {
											Fechar: function(){
												$( this ).dialog( "close" );
												EnviarDados('index.php','14','');
											}
										});
									}else if(retorno_ajax==2){
										alert("Servidor já cadastrado!");
									}else{
										alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
									}
								}
							});
							
						},
						Sair: function() {
							$( this ).dialog( "close" );
						}
					},
					close: function( event, ui ) {
						$(".sem_1").hide();
						$("#def_sem_1").attr("checked",false);
						//$('.cls_meta').each(function() {
						//	$(this).val("");
						//});
						$('.cls_metas2').each(function(){
							$(this).val("");
						});
					}
				});
				//alert($("#nivel_usu").find("option[value='USU']").attr("selected","selected"));
			}
		});
	}
	function fc_del_cliente(valor1,valor2){
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar o servidor <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  "inc/ajax_cliente.php",
					data: "flag=D&banco_id=" + valor1,
					success: function(retorno_ajax){
						$( this ).dialog( "close" );
						if(retorno_ajax==1){
							msgbox("<br><table align='center'><tr><td>Cliente deletado com sucesso !</td></tr></table><br>",{
								Fechar: function(){
									$( this ).dialog( "close" );
									EnviarDados('index.php','11','');
								}
							});
						}else{
							alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
						}
					}
				});
			},
			"Não": function(){
				$( this ).dialog( "close" );
			}
		});
	}
	function fc_del_andamento(valor1,valor2){
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar o andamento <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  "inc/ajax_andamento.php",
					data: "flag=D&anda_id=" + valor1,
					success: function(retorno_ajax){
						$( this ).dialog( "close" );
						if(retorno_ajax==1){
							msgbox("<br><table align='center'><tr><td>Andamento deletado com sucesso !</td></tr></table><br>",{
								Fechar: function(){
									$( this ).dialog( "close" );
									EnviarDados('index.php','12','');
								}
							});
						}else{
							alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
						}
					}
				});
			},
			"Não": function(){
				$( this ).dialog( "close" );
			}
		});
	}
	function fc_del_metas(valor1,valor2){
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar a meta <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  "inc/ajax_metas.php",
					data: "flag=D&meta_id=" + valor1,
					success: function(retorno_ajax){
						$( this ).dialog( "close" );
						if(retorno_ajax==1){
							msgbox("<br><table align='center'><tr><td>Meta deletada com sucesso !</td></tr></table><br>",{
								Fechar: function(){
									$( this ).dialog( "close" );
									EnviarDados('index.php','14','');
								}
							});
						}else{
							alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
						}
					}
				});
			},
			"Não": function(){
				$( this ).dialog( "close" );
			}
		});
	}
	
	function fc_del_usu(valor1,valor2){
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar o usuário <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  "inc/ajax_usu.php",
					data: "flag=D&id_usu=" + valor1,
					success: function(retorno_ajax){
						$( this ).dialog( "close" );
						if(retorno_ajax==1){
							msgbox("<br><table align='center'><tr><td>Usuário deletado com sucesso !</td></tr></table><br>",{
								Fechar: function(){
									$( this ).dialog( "close" );
									EnviarDados('index.php','8','');
								}
							});
						}else{
							alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
						}
					}
				});
				//EnviarDados('index.php','8','');
			},
			"Não": function(){
				$( this ).dialog( "close" );
			}
		});
	}
	function fc_del_sem(valor1,valor2){
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar a semana do mês: <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  "inc/ajax_sem.php",
					data: "flag=D&id_sem=" + valor1,
					success: function(retorno_ajax){
						$( this ).dialog( "close" );
						if(retorno_ajax==1){
							msgbox("<br><table align='center'><tr><td>Semana deletada com sucesso !</td></tr></table><br>",{
								Fechar: function(){
									$( this ).dialog( "close" );
									EnviarDados('index.php','15','');
								}
							});
						}else{
							alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
						}
					}
				});
				//EnviarDados('index.php','8','');
			},
			"Não": function(){
				$( this ).dialog( "close" );
			}
		});
	}
	
	function fc_del_setor(valor1,valor2){
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar o setor <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  "inc/ajax_setor.php",
					data: "flag=D&id_setor=" + valor1,
					success: function(retorno_ajax){
						$( this ).dialog( "close" );
						if(retorno_ajax==1){
							msgbox("<br><table align='center'><tr><td>Setor deletado com sucesso !</td></tr></table><br>",{
								Fechar: function(){
									$( this ).dialog( "close" );
									EnviarDados('index.php','9','');
								}
							});
						}else{
							alert("Erro: " + retorno_ajax + ". (Copie esse erro e informe ao administrador)");
						}
					}
				});
				//EnviarDados('index.php','8','');
			},
			"Não": function(){
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
				alert("Caractere não aceito para esse campo");
				pEvent.keyCode = 0;
			}
		}else{
			if ((pEvent.which<97 || pEvent.which>122)&&(pEvent.which<48 || pEvent.which>57)) {	
				alert("Caractere não aceito para esse campo");
				pEvent.which = 0;
			}
		}
	}

function diasemana(valor){ 
	if(valor.value.length==10){
		var semana = ["domingo", "segunda-feira", "terça-feira","quarta-feira","quinta-feira","sexta-feira","sábado"];
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
		return "Senhas não são iguais";
	}else if(valor1=="" && valor3=="I"){
		$("#senha_usu1").css("border","1px solid red");
		$("#senha_usu2").css("border","1px solid red");
		return "Informe sua senha!";
	}else{
		if((valor1!="" && valor3=="U" && valor1.length<4) || (valor1.length<4 && valor3=="I")){
			$("#senha_usu1").css("border","1px solid red");
			$("#senha_usu2").css("border","1px solid red");
			return "Sua senha deve conter no mínimo 4 caracteres!";
		}else{
			var er = /[A-Za-z0-9_\-\.]{4,}/;
			if((er.test(valor1)==false && valor1!="" && valor3=="U") || (er.test(valor1)==false && valor3=="I")){
				$("#senha_usu1").css("border","1px solid red");
				$("#senha_usu2").css("border","1px solid red");
				return "Senha contém caractere inválido!";
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
		return "E-mail inválido!";
	}else{
		return "";
	}
}

function addMes(data,mes){
	var minhaData = moment(data,"D/M/YYYY").add('months', mes);
	return moment(minhaData).format('DD/MM/YYYY');
}
function inserir_cli(valor,stt){
	var crt = parseFloat($("#cartei_num").val());
	if(stt==1){
		crt = crt+1;
		$("#dados_"+(crt-1)).html(
		"<select class='cls_cliente input-default' name='dados_name_"+crt+"' style='width:360px;height:22px'>"+valor+"</select>" +
		"<button id='bt1_"+crt+"' class='bts' onclick='inserir_cli($(\"#dados_name_1\").html(),1);'>+</button>" +
		"<button id='bt0_"+crt+"' class='bts' onclick='inserir_cli($(\"#dados_name_1\").html(),0);'>-</button>" + 
		"<div id='dados_"+crt+"'></div>");
		$("#bt1_"+(crt-1)).hide();
		$("#bt0_"+(crt-1)).hide();	
	}else if(stt==0){
		crt = crt-1;
		$("#dados_"+crt).html(" ");
		$("#bt1_"+crt).show();
		$("#bt0_"+crt).show();
	}
	$("#cartei_num").val(crt);
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
function sel_tipo(valor1,valor2){
	
	$.ajax({
		type: "POST",
		url:  "inc/ajax_select.php",
		data: "flag=" + valor2 + "&dados=" + valor1,
		success: function(retorno_ajax){
			if(valor1==0){
				$(".cls_andam").html(retorno_ajax);
				if(valor2==1){
					$("#sel_anda").html("Selecionar Andamentos:");
				}else if(valor2==2){
					$("#sel_anda").html("Selecionar Lançamentos:");
				}	
			}else if(valor1==1){
				$(".cls_usu2").html(retorno_ajax);
				$("#sel_banco").html("Clientes:");
			}
		}
	});
}

function definir_sem(valor1,valor2){
	if($(valor1).prop("checked")==true){
		$(".sem_"+valor2).show();
		$("#meta_valor_"+valor2).attr("readonly",true);
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





