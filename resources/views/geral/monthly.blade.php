<meta http-equiv="content-type" content="text/html; charset=utf-8">
<button type="button" onclick="AbrirRelatorio(1);" class="report-switch report-switch--monthly">{{ __('Monthly') }}</button><br>
<br><div class="report-title">{!! $titleArea !!}{!! isset($regionLabel) ? $regionLabel : '' !!} | {{ __('Month/Year') }}: <b>{{ e($startDate) }}</b> </div><br>
@php
	$weekCountClass = count($weeks) === 4 ? 'is-four-weeks' : 'is-five-weeks';
@endphp
<script>
	window.arsDetailFaturamentoUrl = "{{ url('detalhes/faturamento') }}";
	window.arsReportContentHeight = {{ (int) $contentHeight }};
</script>
<table align="center" height="50%" border="0" cellspacing="3" cellpadding="3" class="report-table report-table--monthly {{ $weekCountClass }}">
	<tr>
		<td align="center" rowspan="2" class="cls_sema cls_indic">{{ __('Clients') }}</td>
		@foreach ($weeks as $week)
			<td align="center" colspan="3" class="cls_sema">{{ $week['label'] }}</td>
		@endforeach
		<td class="report-cell--gap"></td>
		<td align="center" colspan="3" rowspan="1" class="cls_sema cls_vals">{{ __('Total') }}</td>
	</tr>
	<tr>
		@foreach ($weeks as $week)
			<td align="center" class="cls_dados cls_vals">{{ __('Goal') }}</td>
			<td align="center" class="cls_dados cls_vals">{{ __('Realized') }}</td>
			<td align="center" class="cls_dados cls_vals">{{ __('Percent') }}</td>
		@endforeach
		<td class=""></td>
		<td align="center" class="cls_dados cls_bk2">{{ __('Goal') }}</td>
		<td align="center" class="cls_dados cls_bk2">{{ __('Realized') }}</td>
		<td align="center" class="cls_dados cls_bk2">{{ __('Status Light') }}</td>
	</tr>
	@foreach ($rows as $row)
	<tr class="report-row">
		<td class="cls_indic report-cell--padded">{{ e($row['name']) }}</td>
		@foreach ($row['weekData'] as $weekData)
			<td class="cls_vals cls_body report-cell--meta" align="center">{{ number_format($weekData['meta'], 2, ',', '.') }}</td>
			<td class="cls_vals cls_body cls_real" align="center" onclick="relatorioAbrirDetalhe('{{ implode(',', $weekData['codes']) }}','{{ e($row['name']) }}');">{{ number_format($weekData['real'], 2, ',', '.') }}</td>
			<td class="cls_body" align="center"><img src="http://***REMOVED***/img/{{ $weekData['icon'] }}" class="box" />{{ number_format($weekData['percent'], 0, ',', '') }} %</td>
		@endforeach
		<td class="">&nbsp;</td>
		<td class="cls_body cls_bk report-cell--total-meta" align="center"><b>{{ number_format($row['totalMeta'], 2, ',', '.') }}</b></td>
		<td class="cls_body cls_bk report-cell--black-text" align="center"><b>{{ number_format($row['totalReal'], 2, ',', '.') }}</b></td>
		<td class="cls_body cls_bk report-cell--black-text" align="center"><img src="http://***REMOVED***/img/{{ $row['totalIcon'] }}" class="box" />{{ number_format($row['totalPercent'], 0, ',', '') }} %</td>
	</tr>
	@endforeach
	<tr height="5px"></tr>
	<tr>
		<td class="cls_bk"><b>{{ __('Total') }}</b></td>
		@foreach ($totals['weeks'] as $weekTotal)
			<td align="center" class="cls_vals2 cls_bk report-cell--total-meta"><b>{{ number_format($weekTotal['meta'], 2, ',', '.') }}</b></td>
			<td align="center" class="cls_vals2 cls_bk"><b>{{ number_format($weekTotal['real'], 2, ',', '.') }}</b></td>
			<td align="center" class="cls_vals2 cls_bk"><img src="http://***REMOVED***/img/{{ $weekTotal['icon'] }}" class="box" /><b>{{ number_format($weekTotal['percent'], 0, ',', '') }} %</b></td>
		@endforeach
		<td class="">&nbsp;</td>
		<td align="center" class="cls_vals2 cls_bk report-cell--total-meta"><b>{{ number_format($totals['meta'], 2, ',', '.') }}</b></td>
		<td align="center" class="cls_vals2 cls_bk"><b>{{ number_format($totals['real'], 2, ',', '.') }}</b></td>
		<td align="center" class="cls_vals2 cls_bk"><img src="http://***REMOVED***/img/{{ $totals['icon'] }}" class="box" /><b>{{ number_format($totals['percent'], 0, ',', '') }} %</b></td>
	</tr>
	<input type="hidden" name="startSetor" value="{{ e($startSector) }}"/>
	<input type="hidden" name="startDate" value="{{ e($startDate) }}" />
	<input type="hidden" name="mes" value="{{ (int) $month }}" />
	<input type="hidden" name="ano" value="{{ (int) $year }}" />
	<input type="hidden" name="regiao_id" value="{{ isset($regionId) ? (int) $regionId : 0 }}" />
</table>
