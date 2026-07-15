<meta http-equiv="content-type" content="text/html; charset=utf-8">
<button type="button" onclick="AbrirRelatorio(1);" style="float:right; position:relative;margin-right:50px;border:1px dotted #999;">Mensal</button><br>
<br><div style="font-family:arial;margin-left:40px;font-size:10pt;">{!! $titleArea !!}{!! isset($regionLabel) ? $regionLabel : '' !!} | M&ecirc;s / Ano: <b>{{ e($startDate) }}</b> </div><br>
<script>
	function send_form(valor1,valor2){
		$("#codig_lnc").val(valor1);
		$("#banco_lnc").val(valor2);
		$("#form_ars").attr("action","dados_fatur.php");
		$("#form_ars").attr("target","_blank");
		$("#form_ars").submit();
	}
</script>
<style>
td.cls_dados{border-left-width:1px;border:1px dotted #999;height:30px;}
td.cls_body{border-left-width:1px;border:1px dotted #999;height:30px;text-align:right;padding-right:5px;}
.cls_dados{background:#DBDBDB;}
.cls_sema{background:#ccc;font-weight:bold;border:1px dotted #999;}
.box{margin-left:5px;float:left;}
.cls_vals{width:{{ count($weeks) === 4 ? '4%' : '5%' }};}
.cls_vals2{height:25px;width:{{ count($weeks) === 4 ? '4%' : '5%' }};border:1px dotted #999;text-align:right;padding-right:5px;}
.cls_indic{height:30px;width:13%;border:1px dotted #999;}
.cls_real{font-weight:bold;color:#8B4513;cursor:pointer;}
.cls_real:hover{background:#ebebeb;}
.cls_bk{background:#F5F6CE;}
.cls_bk2{background:#F2F3C5;}
</style>
<table align="center" height="50%" width="99%" border="0" cellspacing="3" cellpadding="3" style="font-family:arial;font-size:8pt; border-collapse: collapse;background:#ffffff">
	<tr>
		<td align="center" rowspan="2" class="cls_sema cls_indic">CLIENTES</td>
		@foreach ($weeks as $week)
			<td align="center" colspan="3" class="cls_sema">{{ $week['label'] }}</td>
		@endforeach
		<td style="width:0.5%"></td>
		<td align="center" colspan="3" rowspan="1" class="cls_sema cls_vals">TOTAL</td>
	</tr>
	<tr>
		@foreach ($weeks as $week)
			<td align="center" class="cls_dados cls_vals">META</td>
			<td align="center" class="cls_dados cls_vals">REALIZADO</td>
			<td align="center" class="cls_dados cls_vals">PERC.</td>
		@endforeach
		<td class=""></td>
		<td align="center" class="cls_dados cls_bk2">META</td>
		<td align="center" class="cls_dados cls_bk2">REALIZADO</td>
		<td align="center" class="cls_dados cls_bk2">FAROL</td>
	</tr>
	@foreach ($rows as $row)
	<tr style="height:30px">
		<td class="cls_indic" style="padding-left:5px">{{ e($row['name']) }}</td>
		@foreach ($row['weekData'] as $weekData)
			<td class="cls_vals cls_body" align="center" style="background:#F0F0F0">{{ number_format($weekData['meta'], 2, ',', '.') }}</td>
			<td class="cls_vals cls_body cls_real" align="center" onclick="send_form('{{ implode(',', $weekData['codes']) }}','{{ e($row['name']) }}');">{{ number_format($weekData['real'], 2, ',', '.') }}</td>
			<td class="cls_body" align="center"><img src="http://***REMOVED***/img/{{ $weekData['icon'] }}" class="box" />{{ number_format($weekData['percent'], 0, ',', '') }} %</td>
		@endforeach
		<td class="">&nbsp;</td>
		<td class="cls_body cls_bk" align="center" style="background:#F2F5A9"><b>{{ number_format($row['totalMeta'], 2, ',', '.') }}</b></td>
		<td class="cls_body cls_bk" align="center" style="color:#000"><b>{{ number_format($row['totalReal'], 2, ',', '.') }}</b></td>
		<td class="cls_body cls_bk" align="center" style="color:#000"><img src="http://***REMOVED***/img/{{ $row['totalIcon'] }}" class="box" />{{ number_format($row['totalPercent'], 0, ',', '') }} %</td>
	</tr>
	@endforeach
	<tr height="5px"></tr>
	<tr>
		<td class="cls_bk" style="background:#F2F3C5;border:1px dotted #999;"><b>TOTAL</b></td>
		@foreach ($totals['weeks'] as $weekTotal)
			<td align="center" class="cls_vals2 cls_bk" style="background:#F2F5A9"><b>{{ number_format($weekTotal['meta'], 2, ',', '.') }}</b></td>
			<td align="center" class="cls_vals2 cls_bk"><b>{{ number_format($weekTotal['real'], 2, ',', '.') }}</b></td>
			<td align="center" class="cls_vals2 cls_bk"><img src="http://***REMOVED***/img/{{ $weekTotal['icon'] }}" class="box" /><b>{{ number_format($weekTotal['percent'], 0, ',', '') }} %</b></td>
		@endforeach
		<td class="">&nbsp;</td>
		<td align="center" class="cls_vals2 cls_bk" style="background:#F2F5A9"><b>{{ number_format($totals['meta'], 2, ',', '.') }}</b></td>
		<td align="center" class="cls_vals2 cls_bk"><b>{{ number_format($totals['real'], 2, ',', '.') }}</b></td>
		<td align="center" class="cls_vals2 cls_bk"><img src="http://***REMOVED***/img/{{ $totals['icon'] }}" class="box" /><b>{{ number_format($totals['percent'], 0, ',', '') }} %</b></td>
	</tr>
	<input type="hidden" name="codig_lnc" id="codig_lnc" />
	<input type="hidden" name="banco_lnc" id="banco_lnc" />
	<input type="hidden" name="startSetor" value="{{ e($startSector) }}"/>
	<input type="hidden" name="startDate" value="{{ e($startDate) }}" />
	<input type="hidden" name="mes" value="{{ (int) $month }}" />
	<input type="hidden" name="ano" value="{{ (int) $year }}" />
	<input type="hidden" name="regiao_id" value="{{ isset($regionId) ? (int) $regionId : 0 }}" />
</table>
<style>
#content-box{height:{{ (int) $contentHeight }}px;}
</style>
