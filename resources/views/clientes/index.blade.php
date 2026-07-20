<div class="admin-module-offset">
<script>
window.arsClientResourceBaseUrl = "{{ url('admin/clientes') }}";
</script>
<label><h2><u>Clientes</u></h2></label>
<div>
<table class="adminlist">
	<tr height="30">
		<td class="order"><b>C&oacute;digo</b></td>
		<td class="order"><b>Nome</b></td>
		<td class="order"><b>Nome COD</b></td>
		<td class="order"><b>Carteira(s)</b></td>
		<td class="order"><b>DATA</b></td>
		<td class="order"><b>&Aacute;REA</b></td>
		<td class="order"><b>STATUS</b></td>
		<td class="order"><b>Op&ccedil;&otilde;es</b></td>
	</tr>
@foreach ($clients as $client)
	<tr>
		<td class="order">{{ $client['banco_id'] }}</td>
		<td class="order">{{ e($client['banco_name']) }}</td>
		<td class="order">{{ e($client['banco_cod']) }}</td>
		<td class="order">{!! $client['dados_html'] !!}</td>
		<td class="order">{{ $client['datacad'] }}</td>
		<td class="order">{{ e($client['area_nome']) }}</td>
		<td class="order">{{ isset($statusLabels[$client['banco_status']]) ? $statusLabels[$client['banco_status']] : $client['banco_status'] }}</td>
		<td class="order admin-action-cell">
			@include('partials.admin-action-buttons', [
				'display' => 'block',
				'editAction' => "fc_edit_cliente(" . (int) $client['banco_id'] . ",'U')",
				'deleteAction' => "fc_del_cliente(" . (int) $client['banco_id'] . "," . json_encode((string) $client['banco_name']) . ")",
				'editTitle' => 'Editar Cliente',
				'deleteTitle' => 'Excluir Cliente',
			])
		</td>
	</tr>
@endforeach
</table>
<div id="dialog-edit-cliente" title="Editar Cliente" class="admin-dialog is-hidden">
	<p class="validateTips">Edite o Cliente Abaixo</p>
	<fieldset>
		<div id="tb_dialog" class="admin-dialog-panel">
			<table>
				<tr>
					<td>Nome do Cliente:<br><input type="text" class="cls_cliente admin-field--standard" name="banco_name" id="banco_name" value="" obrigatorio="1" title="Nome do banco" /></td>
					<td>Texto COD <br><input type="text" class="cls_cliente admin-field--standard" name="banco_cod" id="banco_cod" value="" obrigatorio="1" title="Nome do banco" /></td>
				</tr>
				<tr>
					<td>Setor:<br>
						<select class="cls_cliente input-default admin-field--standard admin-field--select" name="banco_area" id="banco_area" obrigatorio="1" title="Setor">
							<option value=""></option>
							@foreach ($areas as $area)
								<option value="{{ $area['area_id'] }}">{{ e($area['area_nome']) }}</option>
							@endforeach
						</select>
					</td>
					<td>Status:<br>
						<select class="cls_cliente input-default admin-field--standard admin-field--select" name="banco_status" id="banco_status" obrigatorio="1" title="Setor">
							<option value=""></option>
							<option value="Y">Ativo</option>
							<option value="N">Inativo</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Classifica&ccedil;&atilde;o:<br><input type="text" class="cls_cliente admin-field--standard" name="banco_class" id="banco_class" value="" obrigatorio="1" title="Classifica&ccedil;&atilde;o" /></td>
					<td>Prazo do Simulador/Decisor:<br><input type="text" class="cls_cliente admin-field--standard" name="simulador" id="simulador" value="" obrigatorio="1" title="Simulador/Decisor" /></td>
				</tr>
				<tr><td>Nome Curto:<br><input type="text" class="cls_cliente admin-field--standard" name="banco_curto" id="banco_curto" value="" obrigatorio="1" title="Nome Curto" /></td></tr>
				<tr>
					<td colspan="2">Carteiras vinculadas:<br>
						<div id="cliente-carteiras-vinculadas" class="cliente-carteiras-lista"></div>
						<div id="cliente-carteiras-inputs"></div>
						<div id="cliente-carteiras-vazio" class="cliente-carteiras-vazio">Nenhuma carteira vinculada.</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">Adicionar carteira:<br>
						<select class="input-default admin-field--large admin-field--select" name="dados_name_pool" id="dados_name_pool" title="Carteira">
							<option value=""></option>
							@foreach ($carteiras as $carteira)
								<option value="{{ e($carteira['Carteira']) }}"> {{ e($carteira['Carteira']) }}</option>
							@endforeach
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
