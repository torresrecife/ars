<div class="***REMOVED***-module-offset">
<script>
window.arsWeekResourceBaseUrl = "{{ url('***REMOVED***/semanas') }}";
</script>
<label><h2><u>Semanas</u></h2></label>
<div>
<table class="***REMOVED***list">
	<tr height="30">
		<td class="order"><b>C&oacute;digo</b></td>
		<td class="order"><b>M&ecirc;s</b></td>
		<td class="order"><b>Ano</b></td>
		<td class="order ***REMOVED***-week-cell"><b>1&ordf; Semana</b></td>
		<td class="order ***REMOVED***-week-cell"><b>2&ordf; Semana</b></td>
		<td class="order ***REMOVED***-week-cell"><b>3&ordf; Semana</b></td>
		<td class="order ***REMOVED***-week-cell"><b>4&ordf; Semana</b></td>
		<td class="order ***REMOVED***-week-cell ***REMOVED***-week-cell--optional"><b>5&ordf; Semana</b></td>
		<td class="order"><b>Alterado em</b></td>
		<td class="order"><b>Cadastrado em</b></td>
		<td class="order"><b>Configura&ccedil;&otilde;es</b></td>
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
				'editTitle' => 'Editar Semana',
				'deleteTitle' => 'Excluir Semana',
			])
		</td>
	</tr>
@endforeach
</table>
<div id="dialog-edit-sem" title="Editar Semana" class="***REMOVED***-dialog ***REMOVED***-dialog--scroll is-hidden">
	<p class="validateTips">Edite a Semana Abaixo</p>
	<fieldset>
		<div>
			<table class="***REMOVED***-dialog-table ***REMOVED***-dialog-table--week">
				<tr>
					<td><label>C&oacute;digo:</label></td>
					<td colspan="3"><input type="text" class="cls_sem ***REMOVED***-code-field" name="id_sem" id="id_sem" title="Id da semana" readonly="readonly" /></td>
				</tr>
				<tr>
					<td class="***REMOVED***-label-cell"><label>M&ecirc;s/Ano:</label></td>
					<td class="***REMOVED***-label-cell"><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="mes_sem" id="mes_sem" title="M&ecirc;s" alt="integer"/></td>
					<td align="center" class="***REMOVED***-label-cell">/</td>
					<td class="***REMOVED***-label-cell"><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="ano_sem" id="ano_sem" title="Ano" maxlength="4"/></td>
				<tr>
					<td><label>1&ordf; Semana:</label></td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="ini1_sem" id="ini1_sem" title="1&ordf; Semana in&iacute;cio" alt="integer"/></td>
					<td align="center" class="***REMOVED***-label-cell">&agrave;</td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="fim1_sem" id="fim1_sem" title="1&ordf; Semana fim" alt="integer"/></td>
				</tr>
				<tr>
					<td><label>2&ordf; Semana:</label></td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="ini2_sem" id="ini2_sem" title="2&ordf; Semana in&iacute;cio" alt="integer"/></td>
					<td align="center" class="***REMOVED***-label-cell">&agrave;</td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="fim2_sem" id="fim2_sem" title="2&ordf; Semana fim" alt="integer"/></td>
				</tr>
				<tr>
					<td><label>3&ordf; Semana:</label></td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="ini3_sem" id="ini3_sem" title="3&ordf; Semana in&iacute;cio" alt="integer"/></td>
					<td align="center" class="***REMOVED***-label-cell">&agrave;</td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="fim3_sem" id="fim3_sem" title="3&ordf; Semana fim" alt="integer"/></td>
				</tr>
				<tr>
					<td><label>4&ordf; Semana:</label></td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="ini4_sem" id="ini4_sem" title="4&ordf; Semana in&iacute;cio" alt="integer"/></td>
					<td align="center" class="***REMOVED***-label-cell">&agrave;</td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="fim4_sem" id="fim4_sem" title="4&ordf; Semana fim" alt="integer"/></td>
				</tr>
				<tr>
					<td><label>5&ordf; Semana:</label></td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="ini5_sem" id="ini5_sem" title="5&ordf; Semana in&iacute;cio" alt="integer"/></td>
					<td align="center" class="***REMOVED***-label-cell">&agrave;</td>
					<td><input type="text" class="cls_sem ***REMOVED***-field--fluid" name="fim5_sem" id="fim5_sem" title="5&ordf; Semana fim" alt="integer"/></td>
				</tr>
			</table>
		</div>
	</fieldset>
</div>
