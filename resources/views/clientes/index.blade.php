<div class="***REMOVED***-module-offset">
<script>
window.arsClientResourceBaseUrl = "{{ url('***REMOVED***/clientes') }}";
</script>
<label><h2><u>{{ __('Clients') }}</u></h2></label>
<div>
<table class="***REMOVED***list">
	<tr height="30">
		<td class="order"><b>{{ __('Code') }}</b></td>
		<td class="order"><b>{{ __('Name') }}</b></td>
		<td class="order"><b>{{ __('Code Name') }}</b></td>
		<td class="order"><b>{{ __('Wallet(s)') }}</b></td>
		<td class="order"><b>{{ __('Date') }}</b></td>
		<td class="order"><b>{{ __('Area') }}</b></td>
		<td class="order"><b>{{ __('Status') }}</b></td>
		<td class="order"><b>{{ __('Options') }}</b></td>
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
		<td class="order ***REMOVED***-action-cell">
			@include('partials.***REMOVED***-action-buttons', [
				'display' => 'block',
				'editAction' => "fc_edit_cliente(" . (int) $client['banco_id'] . ",'U')",
				'deleteAction' => "fc_del_cliente(" . (int) $client['banco_id'] . "," . json_encode((string) $client['banco_name']) . ")",
				'editTitle' => __('Edit Client'),
				'deleteTitle' => __('Delete Client'),
			])
		</td>
	</tr>
@endforeach
</table>
<div id="dialog-edit-cliente" title="{{ __('Edit Client') }}" class="***REMOVED***-dialog is-hidden">
	<p class="validateTips">{{ __('Edit the client below') }}</p>
	<fieldset>
		<div id="tb_dialog" class="***REMOVED***-dialog-panel">
			<table>
				<tr>
					<td>{{ __('Client Name') }}:<br><input type="text" class="cls_cliente ***REMOVED***-field--standard" name="banco_name" id="banco_name" value="" obrigatorio="1" title="{{ __('Client Name') }}" /></td>
					<td>{{ __('Code Text') }}<br><input type="text" class="cls_cliente ***REMOVED***-field--standard" name="banco_cod" id="banco_cod" value="" obrigatorio="1" title="{{ __('Code Text') }}" /></td>
				</tr>
				<tr>
					<td>{{ __('Sector') }}:<br>
						<select class="cls_cliente input-default ***REMOVED***-field--standard ***REMOVED***-field--select" name="banco_area" id="banco_area" obrigatorio="1" title="Setor">
							<option value=""></option>
							@foreach ($areas as $area)
								<option value="{{ $area['area_id'] }}">{{ e($area['area_nome']) }}</option>
							@endforeach
						</select>
					</td>
					<td>{{ __('Status') }}:<br>
						<select class="cls_cliente input-default ***REMOVED***-field--standard ***REMOVED***-field--select" name="banco_status" id="banco_status" obrigatorio="1" title="Setor">
							<option value=""></option>
							<option value="Y">{{ __('Active') }}</option>
							<option value="N">{{ __('Inactive') }}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>{{ __('Classification') }}:<br><input type="text" class="cls_cliente ***REMOVED***-field--standard" name="banco_class" id="banco_class" value="" obrigatorio="1" title="{{ __('Classification') }}" /></td>
					<td>{{ __('Simulator/Decision Deadline') }}:<br><input type="text" class="cls_cliente ***REMOVED***-field--standard" name="simulador" id="simulador" value="" obrigatorio="1" title="{{ __('Simulator/Decision Deadline') }}" /></td>
				</tr>
				<tr><td>{{ __('Short Name') }}:<br><input type="text" class="cls_cliente ***REMOVED***-field--standard" name="banco_curto" id="banco_curto" value="" obrigatorio="1" title="{{ __('Short Name') }}" /></td></tr>
				<tr>
					<td colspan="2">{{ __('Linked wallets') }}:<br>
						<div id="cliente-carteiras-vinculadas" class="cliente-carteiras-lista"></div>
						<div id="cliente-carteiras-inputs"></div>
						<div id="cliente-carteiras-vazio" class="cliente-carteiras-vazio">{{ __('No linked wallets.') }}</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">{{ __('Add wallet') }}:<br>
						<select class="input-default ***REMOVED***-field--large ***REMOVED***-field--select" name="dados_name_pool" id="dados_name_pool" title="Carteira">
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
