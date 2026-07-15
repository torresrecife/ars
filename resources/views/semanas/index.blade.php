<div style="margin-top:80px">
<script>
window.arsWeekAjaxUrl = "ajax_sem.php";
</script>
<label><h2><u>Semanas</u></h2></label>
<div>
<table class="***REMOVED***list">
	<tr height="30">
		<td class="order"><b>C&oacute;digo</b></td>
		<td class="order"><b>M&ecirc;s</b></td>
		<td class="order"><b>Ano</b></td>
		<td class="order" style="background:#436EEE;color:#ffffff"><b>1&ordf; Semana</b></td>
		<td class="order" style="background:#436EEE;color:#ffffff"><b>2&ordf; Semana</b></td>
		<td class="order" style="background:#436EEE;color:#ffffff"><b>3&ordf; Semana</b></td>
		<td class="order" style="background:#436EEE;color:#ffffff"><b>4&ordf; Semana</b></td>
		<td class="order" style="background:#1E90FF;color:#ffffff"><b>5&ordf; Semana</b></td>
		<td class="order"><b>Alterado em</b></td>
		<td class="order"><b>Cadastrado em</b></td>
		<td class="order"><b>Configura&ccedil;&otilde;es</b></td>
	</tr>
@foreach ($weeks as $arr)
	<tr>
		<td class="order">{{ $arr['semanas_id'] }}</td>
		<td class="order">{{ isset($months[$arr['mes']]) ? $months[$arr['mes']] : $arr['mes'] }}</td>
		<td class="order">{{ $arr['ano'] }}</td>
		<td class="order" style="background:#436EEE;color:#ffffff">{!! $arr['ini_1'] . "&nbsp;&agrave;&nbsp;" . $arr['fim_1'] !!}</td>
		<td class="order" style="background:#436EEE;color:#ffffff">{!! $arr['ini_2'] . "&nbsp;&agrave;&nbsp;" . $arr['fim_2'] !!}</td>
		<td class="order" style="background:#436EEE;color:#ffffff">{!! $arr['ini_3'] . "&nbsp;&agrave;&nbsp;" . $arr['fim_3'] !!}</td>
		<td class="order" style="background:#436EEE;color:#ffffff">{!! $arr['ini_4'] . "&nbsp;&agrave;&nbsp;" . $arr['fim_4'] !!}</td>
		<td class="order" style="background:#1E90FF;color:#ffffff">{!! ($arr['ini_5'] ? $arr['ini_5'] . "&nbsp;&agrave;&nbsp;" . $arr['fim_5'] : '-') !!}</td>
		<td class="order">{{ $arr['dataalt'] }}</td>
		<td class="order">{{ $arr['datacad'] }}</td>
		<td class="order" style="width:130px">{!! fc_botoes_sem($arr['semanas_id'], 'block', $arr['mes']) !!}</td>
	</tr>
@endforeach
</table>
<div id="dialog-edit-sem" title="Editar Semana" style="display:none;text-align:left;overflow-y: scroll;">
	<p class="validateTips">Edite a Semana Abaixo</p>
	<fieldset>
		<div>
			<table style="width:400px">
				<tr>
					<td><label>C&oacute;digo:</label></td>
					<td colspan="3"><input type="text" class="cls_sem" name="id_sem" id="id_sem" style="border:0;background:#fff;width:50px" title="Id da semana" readonly="readonly" /></td>
				</tr>
				<tr>
					<td style="width:50px"><label>M&ecirc;s/Ano:</label></td>
					<td style="width:50px"><input type="text" class="cls_sem" name="mes_sem" id="mes_sem" style="width:90%" title="M&ecirc;s" alt="integer"/></td>
					<td align="center" style="width:50px">/</td>
					<td style="width:50px"><input type="text" class="cls_sem" name="ano_sem" id="ano_sem" style="width:90%" title="Ano" maxlength="4"/></td>
				<tr>
					<td><label>1&ordf; Semana:</label></td>
					<td><input type="text" class="cls_sem" name="ini1_sem" id="ini1_sem" style="width:90%" title="1&ordf; Semana in&iacute;cio" alt="integer"/></td>
					<td align="center" style="width:50px">&agrave;</td>
					<td><input type="text" class="cls_sem" name="fim1_sem" id="fim1_sem" style="width:90%" title="1&ordf; Semana fim" alt="integer"/></td>
				</tr>
				<tr>
					<td><label>2&ordf; Semana:</label></td>
					<td><input type="text" class="cls_sem" name="ini2_sem" id="ini2_sem" style="width:90%" title="2&ordf; Semana in&iacute;cio" alt="integer"/></td>
					<td align="center" style="width:50px">&agrave;</td>
					<td><input type="text" class="cls_sem" name="fim2_sem" id="fim2_sem" style="width:90%" title="2&ordf; Semana fim" alt="integer"/></td>
				</tr>
				<tr>
					<td><label>3&ordf; Semana:</label></td>
					<td><input type="text" class="cls_sem" name="ini3_sem" id="ini3_sem" style="width:90%" title="3&ordf; Semana in&iacute;cio" alt="integer"/></td>
					<td align="center" style="width:50px">&agrave;</td>
					<td><input type="text" class="cls_sem" name="fim3_sem" id="fim3_sem" style="width:90%" title="3&ordf; Semana fim" alt="integer"/></td>
				</tr>
				<tr>
					<td><label>4&ordf; Semana:</label></td>
					<td><input type="text" class="cls_sem" name="ini4_sem" id="ini4_sem" style="width:90%" title="4&ordf; Semana in&iacute;cio" alt="integer"/></td>
					<td align="center" style="width:50px">&agrave;</td>
					<td><input type="text" class="cls_sem" name="fim4_sem" id="fim4_sem" style="width:90%" title="4&ordf; Semana fim" alt="integer"/></td>
				</tr>
				<tr>
					<td><label>5&ordf; Semana:</label></td>
					<td><input type="text" class="cls_sem" name="ini5_sem" id="ini5_sem" style="width:90%" title="5&ordf; Semana in&iacute;cio" alt="integer"/></td>
					<td align="center" style="width:50px">&agrave;</td>
					<td><input type="text" class="cls_sem" name="fim5_sem" id="fim5_sem" style="width:90%" title="5&ordf; Semana fim" alt="integer"/></td>
				</tr>
			</table>
		</div>
	</fieldset>
</div>
