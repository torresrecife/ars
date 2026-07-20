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
	var metaResourceBaseUrl = window.arsMetaResourceBaseUrl || "***REMOVED***/metas";
	var metaResourceUrl = function(id){
		return metaResourceBaseUrl + "/" + id;
	};

	var tt = "";
	var tu = "";
	var resetMetaDialog = function(){
		$("#meta_id").val("");
		$("#meta_name_1").val("");
		$("#regiao_id_1").val("");
		$("#meta_valor_1").val("");
		$("#meta_valor_1").attr("readonly",false);
		$("#def_sem_1").prop("checked",false);
		$("#sem1_valor_1").val("").hide();
		$("#sem2_valor_1").val("").hide();
		$("#sem3_valor_1").val("").hide();
		$("#sem4_valor_1").val("").hide();
		$("#sem5_valor_1").val("").hide();
		$("#metas_1").html("");
		$("#metas_num").val("1");
		$("#inp1_1").show();
	};
	var coletarPayloadMeta = function(){
		var payload = {};
		var invalido = false;

		$('.cls_meta').each(function(){
			if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
				alert(arsFormat("The field :field is required.", {field: $(this).attr("title")}));
				$(this).focus();
				invalido = true;
				return false;
			}
			payload[$(this).attr("name")] = $(this).val();
		});
		if(invalido){
			return false;
		}

		var numes = 0;
		$('.cls_metas2').each(function(){
			numes++;
			payload[$(this).attr("name")] = $(this).val();
			payload["regiao_id_" + numes] = $("#regiao_id_" + numes).val();
			payload["meta_valor_" + numes] = $("#meta_valor_" + numes).val();
			payload["def_sem_" + numes] = $("#def_sem_" + numes).prop("checked") ? "Y" : "N";
			payload["sem1_valor_" + numes] = $("#sem1_valor_" + numes).val();
			payload["sem2_valor_" + numes] = $("#sem2_valor_" + numes).val();
			payload["sem3_valor_" + numes] = $("#sem3_valor_" + numes).val();
			payload["sem4_valor_" + numes] = $("#sem4_valor_" + numes).val();
			payload["sem5_valor_" + numes] = $("#sem5_valor_" + numes).val();
		});
		payload.numes = numes;

		return payload;
	};
	var abrirDialogMeta = function(){
		$("#dialog-edit-metas").dialog({
			title: tt,
			modal: true,
			autoOpen: true,
			height: 400,
			width: 1080,
			buttons: {
				[arsTranslate("Save")]: function() {
					var payload = coletarPayloadMeta();
					if(payload===false){
						return false;
					}
					arsJsonSubmit(
						valor2=="I" ? "POST" : "PUT",
						valor2=="I" ? metaResourceBaseUrl : metaResourceUrl($("#meta_id").val()),
						payload,
						arsTranslate("Error saving goal."),
						function(){
							$("#dialog-edit-metas").dialog("close");
							msgbox("<br><table align='center'><tr><td>" + tu + "</td></tr></table><br>", {
								[arsTranslate("Close")]: function(){
									$(this).dialog("close");
									AbrirMetasAdmin();
								}
							});
						}
					);
				},
				[arsTranslate("Exit")]: function() {
					$(this).dialog("close");
				}
			},
			close: function() {
				resetMetaDialog();
			}
		});
	};
	if(valor2=="I"){
		tt=arsTranslate("New Goal");
		tu=arsTranslate("Goal(s) created successfully.");
		$(".validateMetas").text(arsTranslate("Create a new goal"));
		resetMetaDialog();
		$("#regiao_id_1").val("");
		abrirDialogMeta();
		return;
	}else if(valor2=="U"){
		tt=arsTranslate("Edit Goal");
		tu=arsTranslate("Goal edited successfully.");
		$(".validateMetas").text(arsTranslate("Edit the goal below"));
	}
	arsJsonGet(metaResourceUrl(valor1), arsTranslate("Error loading goal data."), function(ret){
		if((ret.def_sem || "N")=="Y"){
			$("#def_sem_1").prop("checked",true);
			$(".sem_1").show();
			$("#meta_valor_1").attr("readonly",true);
		}else{
			$("#def_sem_1").prop("checked",false);
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
	});
}
function fc_del_metas(valor1,valor2){
	var metaResourceBaseUrl = window.arsMetaResourceBaseUrl || "***REMOVED***/metas";
	msgbox("<br><table align='center'><tr><td style='font-size:8pt'>" + arsFormat("Do you really want to delete the goal :name?", {name: "<b>" + valor2 + "</b>"}) + "</td></tr></table><br>",{
		[arsTranslate("Yes")]: function(){
			var confirmDialog = $(this);
			arsJsonSubmit("DELETE", metaResourceBaseUrl + "/" + valor1, {}, arsTranslate("Error deleting goal."), function(){
				confirmDialog.dialog("close");
				msgbox("<br><table align='center'><tr><td>" + arsTranslate("Goal deleted successfully.") + "</td></tr></table><br>",{
					[arsTranslate("Close")]: function(){
						$( this ).dialog( "close" );
						AbrirMetasAdmin();
					}
				});
			});
		},
		[arsTranslate("No")]: function(){
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
		"<select class='cls_metas2 input-default' name='meta_name_"+crt+"' onchange='my_especie("+crt+");' title='" + arsTranslate("Goal") + "' style='width:260px;height:22px;float:left'>"+valor+"</select>" +
		"<select class='cls_meta_regiao input-default' name='regiao_id_"+crt+"' id='regiao_id_"+crt+"' style='width:160px;height:22px;float:left'>"+$("#regiao_id_1").html()+"</select>" +
		"<input type='text' class='cls_meta' name='meta_valor_"+crt+"' id='meta_valor_"+crt+"' value='' obrigatorio='1' title='" + arsTranslate("Total goal") + "' style='width:120px;float:left'/>" +
		"<input type='checkbox' class='cls_meta' name='def_sem_"+crt+"' id='def_sem_"+crt+"' onclick='definir_sem(this,"+crt+");' value='' title='" + arsTranslate("Manual definition") + "' style='width:20px;'>" +
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
		var mvali = parseFloat((sem1?sem1:0));
		var mva2i = parseFloat((sem2?sem2:0));
		var mva3i = parseFloat((sem3?sem3:0));
		var mva4i = parseFloat((sem4?sem4:0));
		var mva5i = parseFloat((sem5?sem5:0));
		var mvati = mvali+mva2i+mva3i+mva4i+mva5i;
		$("#meta_valor_"+valor2).val(mvati);
	}
}
