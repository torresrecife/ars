<div class="***REMOVED***-module-offset">
<script>
window.arsRegionResourceBaseUrl = "{{ url('***REMOVED***/regioes') }}";
</script>
<label><h2><u>{{ __('Regions') }}</u></h2></label>
<div>
<table class="***REMOVED***list">
	<tr height="30">
		<td class="order"><b>{{ __('Code') }}</b></td>
		<td class="order"><b>{{ __('Name') }}</b></td>
		<td class="order"><b>Slug</b></td>
		<td class="order"><b>UFs</b></td>
		<td class="order"><b>{{ __('Users') }}</b></td>
		<td class="order"><b>{{ __('Status') }}</b></td>
		<td class="order"><b>{{ __('Options') }}</b></td>
	</tr>
@foreach ($regions as $region)
	<tr>
		<td class="order">{{ (int) $region['regiao_id'] }}</td>
		<td class="order">{{ e($region['regiao_nome']) }}</td>
		<td class="order">{{ e($region['regiao_slug']) }}</td>
		<td class="order">{{ e($region['ufs']) }}</td>
		<td class="order">{{ (int) $region['total_usuarios'] }}</td>
		<td class="order">{{ ((string) $region['regiao_status'] === 'Y') ? __('Active') : __('Inactive') }}</td>
		<td class="order ***REMOVED***-action-cell">
			@include('partials.***REMOVED***-action-buttons', [
				'display' => 'block',
				'editAction' => "fc_edit_regiao(" . (int) $region['regiao_id'] . ",'U')",
				'deleteAction' => "fc_del_regiao(" . (int) $region['regiao_id'] . "," . json_encode((string) $region['regiao_nome']) . ")",
				'editTitle' => __('Edit Region'),
				'deleteTitle' => __('Delete Region'),
			])
		</td>
	</tr>
@endforeach
</table>
<div id="dialog-edit-regiao" title="{{ __('Edit Region') }}" class="***REMOVED***-dialog is-hidden">
	<p class="validateRegiao">{{ __('Edit the region below') }}</p>
	<fieldset>
		<div>
			<table class="***REMOVED***-dialog-table ***REMOVED***-dialog-table--region">
				<tr>
					<td width="25%"><label>{{ __('Name') }}:</label></td>
					<td width="75%"><input type="text" class="cls_regiao" name="regiao_nome" id="regiao_nome" value="" obrigatorio="1" title="{{ __('Region Name') }}"/></td>
				</tr>
				<tr>
					<td><label>Slug:</label></td>
					<td><input type="text" class="cls_regiao" name="regiao_slug" id="regiao_slug" value="" obrigatorio="1" title="{{ __('Region Slug') }}"/></td>
				</tr>
				<tr>
					<td><label>UFs:</label></td>
					<td>
						<div class="regiao-ufs-box">
							<div id="regiao-ufs-vinculadas" class="regiao-ufs-lista"></div>
							<div id="regiao-ufs-vazio" class="regiao-ufs-vazio">{{ __('No linked states.') }}</div>
						</div>
						<div class="regiao-ufs-adicionar">
							<select class="input-default" name="regiao_uf_pool" id="regiao_uf_pool" title="UF">
								<option value=""></option>
								@foreach ($ufs as $uf)
									<option value="{{ $uf }}">{{ $uf }}</option>
								@endforeach
							</select>
							<button class="bts" type="button" onclick="regiaoUfsAdicionar();">+</button>
						</div>
						<input type="hidden" class="cls_regiao" name="regiao_ufs" id="regiao_ufs" value="" />
					</td>
				</tr>
				<tr>
					<td><label>Status</label></td>
					<td>
						<select class="cls_regiao" name="regiao_status" id="regiao_status" obrigatorio="1" title="{{ __('Status') }}">
							<option value="Y">{{ __('Active') }}</option>
							<option value="N">{{ __('Inactive') }}</option>
						</select>
					</td>
				</tr>
			</table>
			<input type="hidden" class="cls_regiao" name="regiao_id" id="regiao_id_edit" value="" />
		</div>
	</fieldset>
</div>
</div>
</div>
