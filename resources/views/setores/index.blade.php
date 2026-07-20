<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
		<label><h2><u>{{ __('Areas') }}</u></h2></label>
			<div class="icon-wrapper">
				<table class="***REMOVED***list" width="60%" align="center">
					<tr height="30">
						<td class="order"><b>{{ __('Code') }}</b></td>
						<td class="order"><b>{{ __('Name') }}</b></td>
						<td class="order"><b>{{ __('Created At') }}</b></td>
						<td class="order"><b>{{ __('Options') }}</b></td>
					</tr>
					@foreach ($areas as $area)
						<tr>
							<td class="order">{{ (int) $area['area_id'] }}</td>
							<td class="order">{{ e($area['area_nome']) }}</td>
							<td class="order">{{ e($area['area_date']) }}</td>
							<td class="order ***REMOVED***-action-cell">
								@include('partials.***REMOVED***-action-buttons', [
									'display' => 'block',
									'editAction' => "fc_edit_setor(" . (int) $area['area_id'] . ",'U')",
									'deleteAction' => "fc_del_setor(" . (int) $area['area_id'] . "," . json_encode((string) $area['area_nome']) . ")",
									'editTitle' => __('Edit Sector'),
									'deleteTitle' => __('Delete Sector'),
								])
							</td>
						</tr>
					@endforeach
				</table>
			</div>
		</div>
	</div>
</div>
<div id="dialog-edit-setor" title="{{ __('Edit Sector') }}" class="***REMOVED***-dialog is-hidden">
	<p class="validateTips">{{ __('Edit the area below') }}</p>
	<fieldset>
		<div>
			<table>
				<tr>
					<td><label>{{ __('Area Name') }}</label></td>
					<td><input type="text" class="cls_setor" name="area_nome" id="nome_setor" value="" obrigatorio="1" title="{{ __('Sector Name') }}"/></td>
				</tr>
			</table>
			<input type="hidden" class="cls_setor" name="area_id" id="id_setor" value="" />
		</div>
	</fieldset>
</div>
<script>
window.arsSectorResourceBaseUrl = "{{ url('***REMOVED***/setores') }}";
</script>
