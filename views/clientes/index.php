<div style="margin-top:80px">
<script>
window.arsClientAjaxUrl = "ajax_cliente.php";
</script>
<label><h2><u>Clientes</u></h2></label>
<div>
<table class="***REMOVED***list">
	<tr height="30">
		<td class="order"><b>Código</b></td>
		<td class="order"><b>Nome</b></td>
		<td class="order"><b>Nome COD</b></td>
		<td class="order"><b>Carteira(s)</b></td>
		<td class="order"><b>DATA</b></td>
		<td class="order"><b>ÁREA</b></td>
		<td class="order"><b>STATUS</b></td>
		<td class="order"><b>Opções</b></td>
	</tr>
<?php foreach ($clients as $client): ?>
	<tr>
		<td class="order"><?php echo $client['banco_id']; ?></td>
		<td class="order"><?php echo htmlspecialchars($client['banco_name'], ENT_QUOTES, 'UTF-8'); ?></td>
		<td class="order"><?php echo htmlspecialchars($client['banco_cod'], ENT_QUOTES, 'UTF-8'); ?></td>
		<td class="order"><?php echo $client['dados_html']; ?></td>
		<td class="order"><?php echo $client['datacad']; ?></td>
		<td class="order"><?php echo htmlspecialchars($client['area_nome'], ENT_QUOTES, 'UTF-8'); ?></td>
		<td class="order"><?php echo isset($statusLabels[$client['banco_status']]) ? $statusLabels[$client['banco_status']] : $client['banco_status']; ?></td>
		<td class="order" style="width:130px"><?php echo fc_botoes_cliente($client['banco_id'], 'block', $client['banco_name']); ?></td>
	</tr>
<?php endforeach; ?>
</table>
<style>
.bts{
	font-size:14pt;
	underlinetext-decoration: underline;
	cursor:pointer;
	height:22px;
}
.cliente-carteiras-lista{
	margin:8px 0 0;
}
.cliente-carteiras-item{
	display:flex;
	align-items:flex-start;
	justify-content:space-between;
	margin-bottom:6px;
	padding:6px 8px;
	border:1px solid #d6d6d6;
	background:#f7f7f7;
}
.cliente-carteiras-nome{
	display:block;
	max-width:360px;
	font-size:9pt;
	line-height:1.3;
}
.cliente-carteiras-vazio{
	margin-top:8px;
	font-size:9pt;
	color:#666;
}
.cliente-carteiras-remover{
	margin-left:8px;
	padding:2px 8px;
	font-size:9pt;
	cursor:pointer;
}
</style>
<div id="dialog-edit-cliente" title="Editar Cliente" style="display:none; text-align:left;">
	<p class="validateTips">Edite o Cliente Abaixo</p>
	<fieldset>
		<div id="tb_dialog" style="width:520px">
			<table>
				<tr>
					<td>Nome do Cliente:<br>
						<input type="text" class="cls_cliente" name="banco_name" id="banco_name" value="" obrigatorio="1" title="Nome do banco" style="width:200px" />
					</td>
					<td>Texto COD <br>
						<input type="text" class="cls_cliente" name="banco_cod" id="banco_cod" value="" obrigatorio="1" title="Nome do banco" style="width:200px" />
					</td>
				</tr>
				<tr>
					<td>Setor:<br>
						<select class="cls_cliente input-default" name="banco_area" id="banco_area" obrigatorio="1" title="Setor" style="width:200px;height:22px;">
							<option value=""></option>
							<?php foreach ($areas as $area): ?>
								<option value="<?php echo $area['area_id']; ?>"><?php echo htmlspecialchars($area['area_nome'], ENT_QUOTES, 'UTF-8'); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td>Status:<br>
						<select class="cls_cliente input-default" name="banco_status" id="banco_status" obrigatorio="1" title="Setor" style="width:200px;height:22px;">
							<option value=""></option>
							<option value="Y">Ativo</option>
							<option value="N">Inativo</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Classificação:<br>
						<input type="text" class="cls_cliente" name="banco_class" id="banco_class" value="" obrigatorio="1" title="Classificação" style="width:200px" />
					</td>
					<td>Prazo do Simulador/Decisor:<br>
						<input type="text" class="cls_cliente" name="simulador" id="simulador" value="" obrigatorio="1" title="Simulador/Decisor" style="width:200px" />
					</td>
				</tr>
				<tr>
					<td>Nome Curto:<br>
						<input type="text" class="cls_cliente" name="banco_curto" id="banco_curto" value="" obrigatorio="1" title="Nome Curto" style="width:200px" />
					</td>
				</tr>
				<tr>
					<td colspan="2">Carteiras vinculadas:<br>
						<div id="cliente-carteiras-vinculadas" class="cliente-carteiras-lista"></div>
						<div id="cliente-carteiras-inputs"></div>
						<div id="cliente-carteiras-vazio" class="cliente-carteiras-vazio">Nenhuma carteira vinculada.</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">Adicionar carteira:<br>
						<select class="input-default" name="dados_name_pool" id="dados_name_pool" title="Carteira" style="width:400px;height:22px;">
							<option value=""></option>
							<?php foreach ($carteiras as $carteira): ?>
								<option value="<?php echo htmlspecialchars($carteira['Carteira'], ENT_QUOTES, 'UTF-8'); ?>"> <?php echo htmlspecialchars($carteira['Carteira'], ENT_QUOTES, 'UTF-8'); ?></option>
							<?php endforeach; ?>
						</select>
						<button id="bt-add-carteira" class="bts" type="button" onclick="clienteCarteirasAdicionar();">+</button>
					</td>
				</tr>
			</table>
			<input type="hidden" class="cls_cliente" name="banco_id" id="banco_id" value="" />
			<input type="hidden" class="cls_cliente" name="cartei_num" id="cartei_num" value="0" />
		</div>
	</fieldset>
</div>
<br>
</div>
</div>
