<div class="***REMOVED***-module-offset">
<script>
window.arsWeekResourceBaseUrl = "{{ url('***REMOVED***/semanas') }}";
</script>
<label><h2><u>{{ __('Weeks') }}</u></h2></label>
<div>
<table class="***REMOVED***list">
	<tr height="30">
		<td class="order"><b>{{ __('Code') }}</b></td>
		<td class="order"><b>{{ __('Month') }}</b></td>
		<td class="order"><b>{{ __('Year') }}</b></td>
		<td class="order ***REMOVED***-week-cell"><b>{{ __('1st Week') }}</b></td>
		<td class="order ***REMOVED***-week-cell"><b>{{ __('2nd Week') }}</b></td>
		<td class="order ***REMOVED***-week-cell"><b>{{ __('3rd Week') }}</b></td>
		<td class="order ***REMOVED***-week-cell"><b>{{ __('4th Week') }}</b></td>
		<td class="order ***REMOVED***-week-cell ***REMOVED***-week-cell--optional"><b>{{ __('5th Week') }}</b></td>
		<td class="order"><b>{{ __('Updated At') }}</b></td>
		<td class="order"><b>{{ __('Created At') }}</b></td>
		<td class="order"><b>{{ __('Settings') }}</b></td>
	</tr>
@foreach ($weeks as $arr)
	<tr>
		<td class="order">{{ $arr['semanas_id'] }}</td>
		<td class="order">{{ isset($months[$arr['mes']]) ? $months[$arr['mes']] : $arr['mes'] }}</td>
		<td class="order">{{ $arr['ano'] }}</td>
		<td class="order ***REMOVED***-week-cell">{!! $arr['ini_1'] . "&nbsp;&agrave;&nbsp;" . $arr['fim_1'] !!}</td>
		<td class="order ***REMOVED***-week-cell">{!! $arr['ini_2'] . "&nbsp;&agrave;&nbsp;" . $arr['fim_2'] !!}</td>
		<td class="order ***REMOVED***-week-cell">{!! $arr['ini_3'] . "&nbsp;&agrave;&nbsp;" . $arr['fim_3'] !!}</td>
		<td class="order ***REMOVED***-week-cell">{!! $arr['ini_4'] . "&nbsp;&agrave;&nbsp;" . $arr['fim_4'] !!}</td>
		<td class="order ***REMOVED***-week-cell ***REMOVED***-week-cell--optional">{!! ($arr['ini_5'] ? $arr['ini_5'] . "&nbsp;&agrave;&nbsp;" . $arr['fim_5'] : '-') !!}</td>
		<td class="order">{{ $arr['dataalt'] }}</td>
		<td class="order">{{ $arr['datacad'] }}</td>
		<td class="order ***REMOVED***-action-cell">
			@include('partials.***REMOVED***-action-buttons', [
				'display' => 'block',
				'editAction' => "fc_edit_sem(" . (int) $arr['semanas_id'] . ",'U')",
				'deleteAction' => "fc_del_sem(" . (int) $arr['semanas_id'] . "," . json_encode((string) $arr['mes']) . ")",
				'editTitle' => __('Edit Week'),
				'deleteTitle' => __('Delete Week'),
			])
		</td>
	</tr>
@endforeach
</table>
<div id="dialog-edit-sem" title="{{ __('Edit Week') }}" class="***REMOVED***-dialog ***REMOVED***-dialog--scroll is-hidden">
	<p class="validateTips">{{ __('Edit the week below') }}</p>
	<fieldset>
		<div>
			<table class="***REMOVED***-dialog-table ***REMOVED***-dialog-table--week">
				<tr>
					<td><label>{{ __('Code') }}:</label></td>
					<td colspan="3"><input type="text" class="cls_sem ***REMOVED***-code-field" name="id_sem" id="id_sem" title="{{ __('Week ID') }}" readonly="readonly" /></td>
				</tr>
				<tr>
					<td class="***REMOVED***-label-cell"><label>{{ __('Month/Year') }}:</label></td>
					<td class="***REMOVED***-label-cell"><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="mes_sem" id="mes_sem" title="{{ __('Month') }}" alt="integer"/></td>
					<td align="center" class="***REMOVED***-label-cell">/</td>
					<td class="***REMOVED***-label-cell"><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="ano_sem" id="ano_sem" title="{{ __('Year') }}" maxlength="4"/></td>
				<tr>
					<td><label>{{ __('1st Week') }}:</label></td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="ini1_sem" id="ini1_sem" title="{{ __('1st Week start') }}" alt="integer"/></td>
					<td align="center" class="***REMOVED***-label-cell">&agrave;</td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="fim1_sem" id="fim1_sem" title="{{ __('1st Week end') }}" alt="integer"/></td>
				</tr>
				<tr>
					<td><label>{{ __('2nd Week') }}:</label></td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="ini2_sem" id="ini2_sem" title="{{ __('2nd Week start') }}" alt="integer"/></td>
					<td align="center" class="***REMOVED***-label-cell">&agrave;</td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="fim2_sem" id="fim2_sem" title="{{ __('2nd Week end') }}" alt="integer"/></td>
				</tr>
				<tr>
					<td><label>{{ __('3rd Week') }}:</label></td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="ini3_sem" id="ini3_sem" title="{{ __('3rd Week start') }}" alt="integer"/></td>
					<td align="center" class="***REMOVED***-label-cell">&agrave;</td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="fim3_sem" id="fim3_sem" title="{{ __('3rd Week end') }}" alt="integer"/></td>
				</tr>
				<tr>
					<td><label>{{ __('4th Week') }}:</label></td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="ini4_sem" id="ini4_sem" title="{{ __('4th Week start') }}" alt="integer"/></td>
					<td align="center" class="***REMOVED***-label-cell">&agrave;</td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="fim4_sem" id="fim4_sem" title="{{ __('4th Week end') }}" alt="integer"/></td>
				</tr>
				<tr>
					<td><label>{{ __('5th Week') }}:</label></td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="ini5_sem" id="ini5_sem" title="{{ __('5th Week start') }}" alt="integer"/></td>
					<td align="center" class="***REMOVED***-label-cell">&agrave;</td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="fim5_sem" id="fim5_sem" title="{{ __('5th Week end') }}" alt="integer"/></td>
				</tr>
			</table>
		</div>
	</fieldset>
</div>
