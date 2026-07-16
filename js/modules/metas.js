function my_especie(valor){
	var espe = $("#meta_name_" + valor + " option:selected").attr("especie");
	if(espe==2){
		$("#meta_valor_"+valor).setMask("decimal");
		$(".sem_"+valor).setMask("decimal");
	}else{
		$("#meta_valor_"+valor).setMask("numbers");
		$(".sem_"+valor).setMask("numbers");
	}
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
											AbrirMetasAdmin();
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
									AbrirMetasAdmin();
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
