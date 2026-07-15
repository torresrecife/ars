<div style="margin-top:80px">
<script>
window.arsClientAjaxUrl = "{{ url('ajax/clientes') }}";
</script>
<label><h2><u>Clientes</u></h2></label>
<div>
<table class="***REMOVED***list">
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
		<td class="order" style="width:130px">{!! fc_botoes_cliente($client['banco_id'], 'block', $client['banco_name']) !!}</td>
	</tr>
@endforeach
</table>
<style>
.bts{font-size:14pt;underlinetext-decoration: underline;cursor:pointer;height:22px;}
.cliente-carteiras-lista{margin:8px 0 0;}
.cliente-carteiras-item{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:6px;padding:6px 8px;border:1px solid #d6d6d6;background:#f7f7f7;}
.cliente-carteiras-nome{display:block;max-width:360px;font-size:9pt;line-height:1.3;}
.cliente-carteiras-vazio{margin-top:8px;font-size:9pt;color:#666;}
.cliente-carteiras-remover{margin-left:8px;padding:2px 8px;font-size:9pt;cursor:pointer;}
</style>
<div id="dialog-edit-cliente" title="Editar Cliente" style="display:none; text-align:left;">
	<p class="validateTips">Edite o Cliente Abaixo</p>
	<fieldset>
		<div id="tb_dialog" style="width:520px">
			<table>
				<tr>
					<td>Nome do Cliente:<br><input type="text" class="cls_cliente" name="banco_name" id="banco_name" value="" obrigatorio="1" title="Nome do banco" style="width:200px" /></td>
					<td>Texto COD <br><input type="text" class="cls_cliente" name="banco_cod" id="banco_cod" value="" obrigatorio="1" title="Nome do banco" style="width:200px" /></td>
				</tr>
				<tr>
					<td>Setor:<br>
						<select class="cls_cliente input-default" name="banco_area" id="banco_area" obrigatorio="1" title="Setor" style="width:200px;height:22px;">
							<option value=""></option>
							@foreach ($areas as $area)
								<option value="{{ $area['area_id'] }}">{{ e($area['area_nome']) }}</option>
							@endforeach
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
					<td>Classifica&ccedil;&atilde;o:<br><input type="text" class="cls_cliente" name="banco_class" id="banco_class" value="" obrigatorio="1" title="Classifica&ccedil;&atilde;o" style="width:200px" /></td>
					<td>Prazo do Simulador/Decisor:<br><input type="text" class="cls_cliente" name="simulador" id="simulador" value="" obrigatorio="1" title="Simulador/Decisor" style="width:200px" /></td>
				</tr>
				<tr><td>Nome Curto:<br><input type="text" class="cls_cliente" name="banco_curto" id="banco_curto" value="" obrigatorio="1" title="Nome Curto" style="width:200px" /></td></tr>
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
