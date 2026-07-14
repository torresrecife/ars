<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
		<label><h2><u>Áreas</u></h2></label>
			<div class="icon-wrapper">
				<table class="adminlist" width="60%" align="center">
					<tr height="30">
						<td class="order"><b>Código</b></td>
						<td class="order"><b>Nome</b></td>
						<td class="order"><b>Data Cadastro</b></td>
						<td class="order"><b>Opções</b></td>
					</tr>
					<?php foreach ($areas as $area): ?>
						<tr>
							<td class="order"><?php echo (int) $area['area_id']; ?></td>
							<td class="order"><?php echo htmlspecialchars($area['area_nome'], ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="order"><?php echo htmlspecialchars($area['area_date'], ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="order" style="width:130px"><?php echo fc_botoes_setor($area['area_id'], 'block', $area['area_nome']); ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div>
</div>
<div id="dialog-edit-setor" title="Editar Setor" style="display:none; text-align:left;">
	<p class="validateTips">Edite a Área Abaixo</p>
	<fieldset>
		<div>
			<table>
				<tr>
					<td><label>Nome da Área</label></td>
					<td><input type="text" class="cls_setor" name="area_nome" id="nome_setor" value="" obrigatorio="1" title="Nome do Setor"/></td>
				</tr>
			</table>
			<input type="hidden" class="cls_setor" name="area_id" id="id_setor" value="" />
		</div>
	</fieldset>
</div>
<script>
window.arsSectorAjaxUrl = "/ars2/ajax_setor.php";
</script>
