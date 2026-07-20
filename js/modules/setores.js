function fc_edit_setor(valor1, valor2){
	var sectorResourceBaseUrl = window.arsSectorResourceBaseUrl || "***REMOVED***/setores";
	var sectorResourceUrl = function(id){
		return sectorResourceBaseUrl + "/" + id;
	};
	var tt = "";
	var successMessage = "";

	if(valor2=="I"){
		tt=arsTranslate("New Sector");
		successMessage=arsTranslate("Sector created successfully.");
		$(".validateTips").text(arsTranslate("Create a new sector"));
	}else if(valor2=="U"){
		tt=arsTranslate("Edit Sector");
		successMessage=arsTranslate("Field updated successfully.");
		$(".validateTips").text(arsTranslate("Edit the sector below"));
	}

	var abrirDialogSetor = function(){
		$("#dialog-edit-setor").dialog({
			title: tt,
			modal: true,
			autoOpen: true,
			height: 440,
			width: 450,
			buttons: {
				[arsTranslate("Save")]: function() {
					var payload = {};
					var invalido = false;
					$('.cls_setor').each(function(){
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

					arsJsonSubmit(
						valor2=="I" ? "POST" : "PUT",
						valor2=="I" ? sectorResourceBaseUrl : sectorResourceUrl($("#id_setor").val()),
						payload,
						arsTranslate("Error saving sector."),
						function(){
							$("#dialog-edit-setor").dialog("close");
							msgbox("<br><table align='center'><tr><td>" + successMessage + "</td></tr></table><br>", {
								[arsTranslate("Close")]: function(){
									$(this).dialog("close");
									AbrirModulo('setores');
								}
							});
						}
					);
				},
				[arsTranslate("Exit")]: function() {
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

	arsJsonGet(sectorResourceUrl(valor1), arsTranslate("Error loading sector data."), function(ret){
		$("#id_setor").val(ret.area_id || "");
		$("#nome_setor").val(ret.area_nome || "");
		abrirDialogSetor();
	});
}

function fc_del_setor(valor1, valor2){
	var sectorResourceBaseUrl = window.arsSectorResourceBaseUrl || "***REMOVED***/setores";
	msgbox("<br><table align='center'><tr><td style='font-size:8pt'>" + arsFormat("Do you really want to delete the sector :name?", {name: "<b>" + valor2 + "</b>"}) + "</td></tr></table><br>",{
		[arsTranslate("Yes")]: function(){
			var dialog = $(this);
			arsJsonSubmit("DELETE", sectorResourceBaseUrl + "/" + valor1, {}, arsTranslate("Error deleting sector."), function(){
				dialog.dialog("close");
				msgbox("<br><table align='center'><tr><td>" + arsTranslate("Sector deleted successfully.") + "</td></tr></table><br>",{
					[arsTranslate("Close")]: function(){
						$(this).dialog("close");
						AbrirModulo('setores');
					}
				});
			});
		},
		[arsTranslate("No")]: function(){
			$(this).dialog("close");
		}
	});
}
