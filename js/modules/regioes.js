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
		alert("Selecione uma UF para adicionar.");
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
			alert("Essa UF ja esta vinculada a regiao.");
		}
		return false;
	}
	$("#regiao-ufs-vinculadas").append(
		"<div class='regiao-ufs-item' data-uf=\"" + regiaoUfsEscape(valor) + "\">"
		+ "<span class='regiao-ufs-nome'>" + regiaoUfsEscape(valor) + "</span>"
		+ "<button type='button' class='regiao-ufs-remover' onclick='regiaoUfsRemover(this);'>Remover</button>"
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
var regiaoAjaxUrl = window.arsRegionAjaxUrl || "ajax/regioes";
		var regiaoJsonData = function(extra){
			return $.extend({ response_format: "json" }, extra || {});
		};
		var tt = "";
		var tu = "";
		if(valor2=="I"){
			tt="Nova Regiao";
			tu="criada";
			$(".validateRegiao").text("Crie uma nova regiao");
			regiaoUfsReset();
			$('.cls_regiao').each(function() { $(this).val(""); });
			$("#regiao_status").val("Y");
			$("#regiao_slug").data("manual", false);
		}else if(valor2=="U"){
			tt="Editar Regiao";
			tu="editada";
			$(".validateRegiao").text("Edite a regiao abaixo");
		}
		var abrirDialogRegiao = function(){
			$("#dialog-edit-regiao").dialog({
				title: tt,
				modal: true,
				autoOpen: true,
				height: 420,
				width: 580,
				buttons: {
					Salvar: function() {
						var mdados = "";
						var invalido = false;
						$('.cls_regiao').each(function(){
							if($(this).val()=="" && $(this).attr("obrigatorio")=="1"){
								alert("O campo " + $(this).attr("title") + " e obrigatorio ");
								$(this).focus();
								invalido = true;
								return false;
							}
							mdados += $(this).attr("name")+"="+escape($(this).val())+"&";
						});
						if(invalido){ return false; }
						if($(".regiao-ufs-item").length===0){
							alert("Selecione ao menos uma UF para a regiao.");
							$("#regiao_uf_pool").focus();
							return false;
						}
						$.ajax({
							type: "POST",
							url:  regiaoAjaxUrl,
							dataType: "json",
							data: "flag=" + valor2 + "&response_format=json&" + mdados,
							success: function(response){
								if(response && response.ok===true){
									$( "#dialog-edit-regiao" ).dialog( "close" );
									msgbox("<br><table align='center'><tr><td>Regiao " + tu + " com sucesso !</td></tr></table><br>", {
										Fechar: function(){ $( this ).dialog( "close" ); AbrirModulo('regioes'); }
									});
								}else if(response && response.code=="duplicate"){
									alert("Slug de regiao ja cadastrado!");
								}else{
									alert((response && response.message) ? response.message : "Erro ao salvar a regiao.");
								}
							}
						});
					},
					Sair: function() { $( this ).dialog( "close" ); }
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
		$.ajax({
			type: "POST",
			url:  regiaoAjaxUrl,
			dataType: "json",
			data: regiaoJsonData({ flag: "E", regiao_id: valor1 }),
			success: function(response){
				if(!response || response.ok!==true || !response.data){
					alert((response && response.message) ? response.message : "Erro ao carregar os dados da regiao.");
					return;
				}
				var ret = response.data || {};
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
			}
		});
}
function fc_del_regiao(valor1,valor2){
var regiaoAjaxUrl = window.arsRegionAjaxUrl || "ajax/regioes";
		msgbox("<br><table align='center'><tr><td style='font-size:8pt'>Deseja realmente deletar a regiao <b>" + valor2 + "</b> ?</td></tr></table><br>",{
			"Sim": function(){
				$.ajax({
					type: "POST",
					url:  regiaoAjaxUrl,
					dataType: "json",
					data: { flag: "D", regiao_id: valor1, response_format: "json" },
					success: function(response){
						$( this ).dialog( "close" );
						if(response && response.ok===true){
							msgbox("<br><table align='center'><tr><td>Regiao deletada com sucesso !</td></tr></table><br>",{
								Fechar: function(){ $( this ).dialog( "close" ); AbrirModulo('regioes'); }
							});
						}else if(response && response.code=="linked_users"){
							alert("Nao e possivel excluir a regiao porque existem usuarios vinculados a ela.");
						}else{
							alert((response && response.message) ? response.message : "Erro ao excluir a regiao.");
						}
					}
				});
			},
			"Nao": function(){ $( this ).dialog( "close" ); }
		});
}

function regiaoSlugify(valor){
	return String(valor || "")
		.toLowerCase()
		.replace(/[Ã¡Ã Ã£Ã¢Ã¤]/g, "a")
		.replace(/[Ã©Ã¨ÃªÃ«]/g, "e")
		.replace(/[Ã­Ã¬Ã®Ã¯]/g, "i")
		.replace(/[Ã³Ã²ÃµÃ´Ã¶]/g, "o")
		.replace(/[ÃºÃ¹Ã»Ã¼]/g, "u")
		.replace(/Ã§/g, "c")
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
