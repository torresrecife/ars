<meta http-equiv="content-type" content="text/html; charset=utf-8">
<button type="button" onclick="AbrirRelatorio(0);" class="report-switch">Semanal</button>
<br><div class="report-title">{!! $titleArea !!}{!! isset($regionLabel) ? $regionLabel : '' !!} | M&ecirc;s / Ano: <b>{{ e($startDate) }}</b> </div><br>
<script>
	window.arsDetailFaturamentoUrl = "{{ url('detalhes/faturamento') }}";
	window.arsReportContentHeight = {{ (int) $contentHeight }};
</script>
<table align="center" height="50%" border="0" cellspacing="3" cellpadding="3" class="report-table report-table--weekly">
	<tr>
		<td align="center" colspan="6" class="cls_indic">PRODUCAO - BVAA</td>
		<td align="center" colspan="0"></td>
		<td align="center" colspan="1" class="cls_indic2">TOTAL</td>
	</tr>
	<tr>
		<td align="center" class="cls_dados cls_vals">CLIENTE</td>
		<td align="center" class="cls_dados cls_vals">META/MES</td>
		<td align="center" class="cls_dados cls_vals">META/HOJE</td>
		<td align="center" class="cls_dados cls_vals">REALIZADO</td>
		<td align="center" class="cls_dados cls_vals">SALDO (Parcial)</td>
		<td align="center" class="cls_dados cls_perc">PERC / HOJE</td>
		<td align="center" class="cls_perc2">&nbsp;</td>
		<td align="center" class="cls_dados cls_perc">PERC / MES</td>
	</tr>
	@foreach ($rows as $row)
	<tr class="report-row">
		<td class="cls_body report-cell--padded">{{ e($row['name']) }}</td>
		<td class="cls_body" align="center">R$ {{ number_format($row['metaMonth'], 2, ',', '.') }}</td>
		<td class="cls_body" align="center">R$ {{ number_format($row['metaToday'], 2, ',', '.') }}</td>
		<td class="cls_body cls_real" align="center" onclick="relatorioAbrirDetalhe('{{ implode(',', $row['codes']) }}','{{ e($row['name']) }}');">R$ {{ number_format($row['realized'], 2, ',', '.') }}</td>
		<td class="cls_body" align="center">R$ {{ number_format($row['balance'], 2, ',', '.') }}</td>
		<td class="cls_body report-cell--black-text {{ $row['colorClass'] }}" align="center">{{ number_format($row['percentToday'], 1, ',', '.') }}%</td>
		<td class="cls_perc2 report-cell--black-text" align="center">&nbsp;</td>
		<td class="cls_body report-cell--black-text {{ $row['colorClass'] }}" align="center"><b>{{ number_format($row['percentMonth'], 1, ',', '.') }}%</b></td>
	</tr>
	@endforeach
	<input type="hidden" name="startSetor" value="{{ e($startSector) }}"/>
	<input type="hidden" name="startDate" value="{{ e($startDate) }}" />
	<input type="hidden" name="mes" value="{{ (int) $month }}" />
	<input type="hidden" name="ano" value="{{ (int) $year }}" />
	<input type="hidden" name="regiao_id" value="{{ isset($regionId) ? (int) $regionId : 0 }}" />
	<tr><td colspan="8"></td></tr>
	<tr class="cls_dados">
		<td align="center" class="cls_body"><b>TOTAIS</b></td>
		<td align="center" class="cls_body"><b>R$ {{ number_format($totals['metaMonth'], 2, ',', '.') }}</b></td>
		<td align="center" class="cls_body"><b>R$ {{ number_format($totals['metaToday'], 2, ',', '.') }}</b></td>
		<td align="center" class="cls_body"><b>R$ {{ number_format($totals['realized'], 2, ',', '.') }}</b></td>
		<td align="center" class="cls_body"><b>R$ {{ number_format($totals['balance'], 2, ',', '.') }}</b></td>
		<td align="center" class="cls_perc {{ $totals['colorClass'] }}"><b>{{ number_format($totals['percentToday'], 1, ',', '.') }}%</b></td>
		<td align="center" class="cls_perc2">&nbsp;</td>
		<td align="center" class="cls_perc {{ $totals['colorClass'] }}"><b>{{ number_format($totals['percentMonth'], 1, ',', '.') }}%</b></td>
	</tr>
</table>
