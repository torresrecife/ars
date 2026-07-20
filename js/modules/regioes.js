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
		alert(arsTranslate("Select a state to add."));
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
			alert(arsTranslate("This state is already linked to the region."));
		}
		return false;
	}
	$("#regiao-ufs-vinculadas").append(
		"<div class='regiao-ufs-item' data-uf=\"" + regiaoUfsEscape(valor) + "\">"
		+ "<span class='regiao-ufs-nome'>" + regiaoUfsEscape(valor) + "</span>"
		+ "<button type='button' class='regiao-ufs-remover' onclick='regiaoUfsRemover(this);'>" + arsTranslate("Remove") + "</button>"
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
function fc_edit_regiao(valor1,valor2){
	var regiaoResourceBaseUrl = window.arsRegionResourceBaseUrl || "admin/regioes";
	var regiaoResourceUrl = function(id){
		return regiaoResourceBaseUrl + "/" + id;
	};
	var tt = "";
	var successMessage = "";
	if(valor2=="I"){
		tt=arsTranslate("New Region");
		successMessage=arsTranslate("Region created successfully.");
		$(".validateRegiao").text(arsTranslate("Create a new region"));
		regiaoUfsReset();
		$('.cls_regiao').each(function() { $(this).val(""); });
		$("#regiao_status").val("Y");
		$("#regiao_slug").data("manual", false);
	}else if(valor2=="U"){
		tt=arsTranslate("Edit Region");
		successMessage=arsTranslate("Region updated successfully.");
		$(".validateRegiao").text(arsTranslate("Edit the region below"));
	}
	var abrirDialogRegiao = function(){
		$("#dialog-edit-regiao").dialog({
			title: tt,
			modal: true,
			autoOpen: true,
			height: 420,
			width: 580,
			buttons: {
				[arsTranslate("Save")]: function() {
					var invalido = false;
					$('.cls_regiao').each(function(){
						if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
							alert(arsFormat("The field :field is required.", {field: $(this).attr("title")}));
							$(this).focus();
							invalido = true;
							return false;
						}
					});
					if(invalido){ return false; }
					if($(".regiao-ufs-item").length===0){
						alert(arsTranslate("Select at least one state for the region."));
						$("#regiao_uf_pool").focus();
						return false;
					}
					var payload = {};
					$('.cls_regiao').each(function(){
						payload[$(this).attr("name")] = $(this).val();
					});
					arsJsonSubmit(
						valor2=="I" ? "POST" : "PUT",
						valor2=="I" ? regiaoResourceBaseUrl : regiaoResourceUrl($("#regiao_id_edit").val()),
						payload,
						arsTranslate("Error saving region."),
						function(){
							$("#dialog-edit-regiao").dialog("close");
							msgbox("<br><table align='center'><tr><td>" + successMessage + "</td></tr></table><br>", {
								[arsTranslate("Close")]: function(){ $( this ).dialog( "close" ); AbrirModulo('regioes'); }
							});
						}
					);
				},
				[arsTranslate("Exit")]: function() { $( this ).dialog( "close" ); }
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
	arsJsonGet(regiaoResourceUrl(valor1), arsTranslate("Error loading region data."), function(ret){
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
	});
}
function fc_del_regiao(valor1,valor2){
	var regiaoResourceBaseUrl = window.arsRegionResourceBaseUrl || "admin/regioes";
	msgbox("<br><table align='center'><tr><td style='font-size:8pt'>" + arsFormat("Do you really want to delete the region :name?", {name: "<b>" + valor2 + "</b>"}) + "</td></tr></table><br>",{
		[arsTranslate("Yes")]: function(){
			var dialog = $(this);
			arsJsonSubmit("DELETE", regiaoResourceBaseUrl + "/" + valor1, {}, arsTranslate("Error deleting region."), function(){
				dialog.dialog("close");
				msgbox("<br><table align='center'><tr><td>" + arsTranslate("Region deleted successfully.") + "</td></tr></table><br>",{
					[arsTranslate("Close")]: function(){ $( this ).dialog( "close" ); AbrirModulo('regioes'); }
				});
			});
		},
		[arsTranslate("No")]: function(){ $( this ).dialog( "close" ); }
	});
}
function regiaoSlugify(valor){
	return String(valor || "")
		.toLowerCase()
		.replace(/[áàãâä]/g, "a")
		.replace(/[éèêë]/g, "e")
		.replace(/[íìîï]/g, "i")
		.replace(/[óòõôö]/g, "o")
		.replace(/[úùûü]/g, "u")
		.replace(/ç/g, "c")
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
