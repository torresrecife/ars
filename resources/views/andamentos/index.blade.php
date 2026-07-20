<div class="***REMOVED***-module-offset">
<script>
window.arsAndamentoResourceBaseUrl = "{{ url('***REMOVED***/andamentos') }}";
window.arsSelectAjaxUrl = "{{ url('ajax/select') }}";
</script>
<label><h2><u>{{ __('Progress') }}</u></h2></label>
<div>
<table class="***REMOVED***list ***REMOVED***list--full">
	<tr height="30">
		<td class="order"><b>{{ __('Code') }}</b></td>
		<td class="order"><b>{{ __('Name') }}</b></td>
		<td class="order"><b>{{ __('Code Name') }}</b></td>
		<td class="order"><b>{{ __('Progress') }}</b></td>
		<td class="order"><b>{{ __('Type') }}</b></td>
		<td class="order"><b>{{ __('Panel') }}</b></td>
		<td class="order"><b>{{ __('Panel Title') }}</b></td>
		<td class="order"><b>{{ __('Options') }}</b></td>
	</tr>
@foreach ($andamentos as $andamento)
	<tr>
		<td class="order">{{ (int) $andamento['anda_id'] }}</td>
		<td class="order">{{ e($andamento['nome']) }}</td>
		<td class="order">{{ e($andamento['chave']) }}</td>
		<td class="order">{{ e($andamento['anda_neo']) }}</td>
		<td class="order ***REMOVED***-type-badge {{ (int) $andamento['especie'] === 1 ? '***REMOVED***-type-badge--production' : '***REMOVED***-type-badge--financial' }}">{{ e($metaTipos[(int) $andamento['especie']]) }}</td>
		<td class="order">{{ e($andamento['painel']) }}</td>
		<td class="order">{{ e($andamento['titulo']) }}</td>
		<td class="order ***REMOVED***-action-cell">
			@include('partials.***REMOVED***-action-buttons', [
				'display' => 'block',
				'editAction' => "fc_edit_andamento(" . (int) $andamento['anda_id'] . ",'U')",
				'deleteAction' => "fc_del_andamento(" . (int) $andamento['anda_id'] . "," . json_encode((string) $andamento['nome']) . ")",
				'editTitle' => __('Edit Progress'),
				'deleteTitle' => __('Delete Progress'),
			])
		</td>
	</tr>
@endforeach
</table>
<div id="dialog-edit-andamento" title="{{ __('Edit Progress') }}" class="***REMOVED***-dialog is-hidden">
	<p class="validateTips">{{ __('Edit the progress below') }}</p>
	<fieldset>
		<div id="tb_dialog" class="***REMOVED***-dialog-panel">
			<table class="***REMOVED***-dialog-table">
				<tr>
					<td>{{ __('Progress Name') }}:<br><input type="text" class="cls_andamento ***REMOVED***-field--standard" name="nome" id="nome" value="" obrigatorio="1" title="{{ __('Progress Name') }}" /></td>
					<td>{{ __('Key Name') }}: <br><input type="text" class="cls_andamento ***REMOVED***-field--standard" name="chave" id="chave" value="" obrigatorio="1" title="{{ __('Key Name') }}" /></td>
				</tr>
				<tr>
					<td>{{ __('Panel') }}: <br>
						<select class="cls_andamento input-default ***REMOVED***-field--standard ***REMOVED***-field--select" name="painel" id="painel" onchange="sel_tipo(0,this.value)" obrigatorio="1" title="{{ __('Panel') }}">
							<option value=""></option><option value="Y">{{ __('Yes') }}</option><option value="N">{{ __('No') }}</option>
						</select>
					</td>
					<td>{{ __('Panel Title') }}: <br><input type="text" class="cls_andamento ***REMOVED***-field--standard" name="titulo" id="titulo" value="" obrigatorio="1" title="{{ __('Title Name') }}" /></td>
				</tr>
				<tr>
					<td colspan="2">{{ __('Type') }}: <br>
						<select class="cls_andamento input-default ***REMOVED***-field--standard ***REMOVED***-field--select" name="especie" id="especie" onchange="sel_tipo(0,this.value)" obrigatorio="1" title="{{ __('Type') }}">
							<option value=""></option><option value="1">{{ __('Production') }}</option><option value="2">{{ __('Financial') }}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2"><label id="sel_anda">{{ __('Select progress items') }}:</label><br/>
						<div id="andamento-tipos-vinculados" class="andamento-tipos-lista"></div>
						<div id="andamento-tipos-inputs"></div>
						<div id="andamento-tipos-vazio" class="andamento-tipos-vazio">{{ __('No linked progress items.') }}</div>
						<div class="***REMOVED***-dialog-row-gap">
							<select class="input-default ***REMOVED***-field--large ***REMOVED***-field--select" name="andam_name_pool" id="andam_name_pool" obrigatorio="1" title="{{ __('Progress') }}"></select>
							<button id="inp1_1" class="bts" type="button" onclick="andamentoTiposAdicionar();">+</button>
						</div>
					</td>
				</tr>
			</table>
			<input type="hidden" class="cls_andamento" name="anda_id" id="anda_id" value="" />
		</div>
	</fieldset>
</div>
<br>
