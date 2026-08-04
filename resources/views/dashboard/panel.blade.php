@if (!empty($viewData['error']))
<br><br><br><br><br>
<div class="dashboard-error">{{ $viewData['error'] }}</div>
<div class="dashboard-error"><input type="button" onclick="javascript:window.history.back()" value="{{ __('Back') }}" class="dashboard-back-button"/></div>
@else
@php
	$bank = $viewData['bank'];
	$weeks = $viewData['weeks'];
	$productionRows = $viewData['productionRows'];
	$financialRows = $viewData['financialRows'];
	$prejudiceRows = $viewData['prejudiceRows'];
	$summary = $viewData['summary'];
	$month = $viewData['month'];
	$year = $viewData['year'];
	$startDate = $viewData['startDate'];
	$regionLabel = isset($viewData['regionLabel']) ? $viewData['regionLabel'] : '';
	$regionId = isset($viewData['regionId']) ? (int) $viewData['regionId'] : 0;
	$areaId = isset($viewData['areaId']) ? (string) $viewData['areaId'] : '';
	$bankId = isset($viewData['bankId']) ? (int) $viewData['bankId'] : 0;
	$showRegionTabs = !empty($viewData['showRegionTabs']);
	$regionTabs = isset($viewData['regionTabs']) ? $viewData['regionTabs'] : array();
	$contentHeight = $viewData['contentHeight'];
	$weekCountClass = count($weeks) === 5 ? 'is-five-weeks' : 'is-four-weeks';
	$splitFinancialTable = !empty($productionRows) && !empty($financialRows);
@endphp
<br><div class="dashboard-title">{{ __('Client') }}: <b>{{ $bank['banco_cod'] }}</b>{!! $showRegionTabs ? '' : $regionLabel !!} | {{ __('Month/Year') }}: <b>{{ $startDate }}</b> <a href="#" onclick="painelNavegarMes('{{ $bankId }}','p'); return false;">&lt;</a> <a href="#" onclick="painelNavegarMes('{{ $bankId }}','n'); return false;">&gt;</a></div>
@if ($showRegionTabs)
<div class="dashboard-region-tabs">
	@foreach ($regionTabs as $tab)
		<a href="#" onclick="painelNavegarRegiao('{{ $bankId }}','{{ (int) $tab['id'] }}'); return false;" class="dashboard-region-tab {{ !empty($tab['active']) ? 'dashboard-region-tab--active' : 'dashboard-region-tab--inactive' }}">{!! $tab['label'] !!}</a>
	@endforeach
</div>
@else
<br>
@endif
<input type="hidden" name="mes" id="mes" value="{{ $month }}"/>
<input type="hidden" name="ano" id="ano" value="{{ $year }}"/>
<input type="hidden" name="regiao_id" id="regiao_id" value="{{ $regionId }}"/>
<input type="hidden" name="area_id" id="panel_area_id" value="{{ e($areaId) }}"/>
<input type="hidden" name="bank_id" id="panel_bank_id" value="{{ $bankId }}"/>
<script>
	window.arsPanelConfig = {
		regionId: {{ (int) $regionId }}
	};
	window.arsDetailAndamentoUrl = "{{ url('detalhes/andamentos') }}";
	window.arsDetailFaturamentoUrl = "{{ url('detalhes/faturamento') }}";
	window.arsPanelContentHeight = {{ (int) $contentHeight }};
</script>
<table align="center" height="auto" width="100%" border="0" cellspacing="1" cellpadding="1" id="tb_pro" class="dashboard-table {{ $weekCountClass }}">
	<tr>
		<td align="center" rowspan="2" class="dashboard-table__spacer-cell"></td>
		<td align="center" rowspan="2" class="cls_sema cls_indic">{{ __('Indicator') }}</td>
		@foreach ($weeks as $week)
			<td align="center" colspan="3" class="cls_sema">{{ $week['label'] }}</td>
		@endforeach
		<td align="center" colspan="3" rowspan="1" class="cls_sema cls_vals">{{ __('Total') }}</td>
	</tr>
	<tr>
		@foreach ($weeks as $week)
			<td align="center" class="cls_dados cls_vals">{{ __('Goal') }}</td>
			<td align="center" class="cls_dados cls_vals">{{ __('Realized') }}</td>
			<td align="center" class="cls_dados cls_vals">{{ __('Status Light') }}</td>
		@endforeach
		<td align="center" class="cls_dados cls_bk2">{{ __('Goal') }}</td>
		<td align="center" class="cls_dados cls_bk2">{{ __('Realized') }}</td>
		<td align="center" class="cls_dados cls_bk2">{{ __('Status Light') }}</td>
	</tr>
	@foreach ($productionRows as $rowIndex => $row)
	<tr class="dashboard-table__row">
		@if ($rowIndex === 0)
			<td align="center" rowspan="{{ count($productionRows) }}" class="cls_colun"><div class="dashboard-table__side-label"><b>{{ __('Operation') }}</b></div></td>
		@endif
		<td class="cls_indic">{{ $row['name'] }}</td>
		@foreach ($row['weekData'] as $weekIndex => $weekData)
			<td align="center" class="cls_vals dashboard-table__metric-meta">{{ number_format($weekData['meta'], 0, ',', '.') }}</td>
			<td align="center" class="cls_vals cls_real" onclick="painelAbrirDetalhe('{{ (int) $row['andaId'] }}','{{ (int) $bank['banco_id'] }}','{{ addslashes($bank['banco_name']) }}','{{ (int) $month }}','{{ (int) $year }}','{{ (int) $weekIndex }}','and');">{{ $weekData['real'] }}</td>
			<td align="center" class="cls_vals"><img src="http://admin/img/{{ $weekData['icon'] }}" class="box" />{{ number_format($weekData['percent'], 0, ',', '') }}%</td>
		@endforeach
		<td align="center" class="cls_vals cls_real cls_bk dashboard-table__total-meta"><b>{{ number_format($row['totalMeta'], 0, ',', '.') }}</b></td>
		<td align="center" class="cls_vals cls_real cls_bk" onclick="painelAbrirDetalhe('{{ (int) $row['andaId'] }}','{{ (int) $bank['banco_id'] }}','{{ addslashes($bank['banco_name']) }}','{{ (int) $month }}','{{ (int) $year }}','total','and');"><b>{{ $row['totalReal'] }}</b></td>
		<td align="center" class="cls_vals cls_bk"><img src="http://admin/img/{{ $row['totalIcon'] }}" class="box" />{{ number_format($row['totalPercent'], 0, ',', '') }}%</td>
	</tr>
	@endforeach
@if ($splitFinancialTable)
</table>
@endif
@if ($splitFinancialTable)
<br><br>
<table align="center" height="20%" width="100%" border="0" cellspacing="1" cellpadding="1" id="tb_fim" class="dashboard-table {{ $weekCountClass }}">
@endif
	@foreach ($financialRows as $rowIndex => $row)
	<tr class="dashboard-table__row">
		@if ($rowIndex === 0)
			<td align="center" rowspan="{{ count($financialRows) }}" class="cls_colun_2"><div class="dashboard-table__side-label dashboard-table__side-label--financial"><b>{{ __('Financial') }}</b></div></td>
		@endif
		<td class="cls_indic">{{ $row['name'] }}</td>
		@foreach ($row['weekData'] as $weekIndex => $weekData)
			<td align="center" class="cls_vals dashboard-table__metric-meta">{{ number_format($weekData['meta'], 2, ',', '.') }}</td>
			<td align="center" class="cls_vals cls_real" onclick="painelAbrirDetalhe('{{ (int) $row['andaId'] }}','{{ (int) $bank['banco_id'] }}','{{ addslashes($bank['banco_name']) }}','{{ (int) $month }}','{{ (int) $year }}','{{ (int) $weekIndex }}','fat');">{{ number_format($weekData['real'], 2, ',', '.') }}</td>
			<td align="center" class="cls_vals"><img src="http://admin/img/{{ $weekData['icon'] }}" class="box" />{{ number_format($weekData['percent'], 0, ',', '') }}%</td>
		@endforeach
		<td align="center" class="cls_vals cls_real cls_bk dashboard-table__total-meta"><b>{{ number_format($row['totalMeta'], 2, ',', '.') }}</b></td>
		<td align="center" class="cls_vals cls_real cls_bk" onclick="painelAbrirDetalhe('{{ (int) $row['andaId'] }}','{{ (int) $bank['banco_id'] }}','{{ addslashes($bank['banco_name']) }}','{{ (int) $month }}','{{ (int) $year }}','total','fat');"><b>{{ number_format($row['totalReal'], 2, ',', '.') }}</b></td>
		<td align="center" class="cls_vals cls_bk"><img src="http://admin/img/{{ $row['totalIcon'] }}" class="box" />{{ number_format($row['totalPercent'], 0, ',', '') }}%</td>
	</tr>
	@endforeach
	<tr height="5px"></tr>
	<tr>
		<td class="dashboard-table__spacer-cell"></td>
		<td class="cls_vals2 cls_bk"><b>{{ __('Financial Total') }}</b></td>
		@foreach ($summary['weekTotals'] as $weekTotal)
			<td align="center" class="cls_vals2 cls_bk dashboard-table__total-meta"><b>{{ number_format($weekTotal['meta'], 2, ',', '.') }}</b></td>
			<td align="center" class="cls_vals2 cls_bk"><b>{{ number_format($weekTotal['real'], 2, ',', '.') }}</b></td>
			<td align="center" class="cls_vals2 cls_bk"><img src="http://admin/img/{{ $weekTotal['icon'] }}" class="box" />&nbsp;<b>{{ number_format($weekTotal['percent'], 1, ',', '') }}%</b></td>
		@endforeach
		<td align="center" class="cls_vals2 cls_bk dashboard-table__total-meta"><b>{{ number_format($summary['metaTotal'], 2, ',', '.') }}</b></td>
		<td align="center" class="cls_vals2 cls_bk"><b>{{ number_format($summary['realTotal'], 2, ',', '.') }}</b></td>
		<td align="center" class="cls_vals2 cls_bk"><img src="http://admin/img/{{ $summary['grandIcon'] }}" class="box" />&nbsp;<b>{{ number_format($summary['grandPercent'], 1, ',', '') }}%</b></td>
	</tr>
	<tr height="5px"></tr>
	@foreach ($prejudiceRows as $row)
	<tr>
		<td class="dashboard-table__spacer-cell"></td>
		<td class="cls_vals2 cls_red"><b>{{ __('Losses') }}</b></td>
		@foreach ($row['weekData'] as $weekIndex => $weekData)
			<td align="center" class="cls_vals2 cls_red2"><b>0,00</b></td>
			<td align="center" class="cls_vals2 cls_real cls_red" onclick="painelAbrirDetalhe('{{ (int) $row['andaId'] }}','{{ (int) $bank['banco_id'] }}','{{ addslashes($bank['banco_name']) }}','{{ (int) $month }}','{{ (int) $year }}','{{ (int) $weekIndex }}','fat');"><b>{{ number_format($weekData['real'], 2, ',', '.') }}</b></td>
			<td align="center" class="cls_vals2 cls_red"><b>-</b></td>
		@endforeach
		<td align="center" class="cls_vals2 cls_red"><b>0,00</b></td>
		<td align="center" class="cls_vals2 cls_real cls_red" onclick="painelAbrirDetalhe('{{ (int) $row['andaId'] }}','{{ (int) $bank['banco_id'] }}','{{ addslashes($bank['banco_name']) }}','{{ (int) $month }}','{{ (int) $year }}','total','fat');"><b>{{ number_format($row['totalReal'], 2, ',', '.') }}</b></td>
		<td align="center" class="cls_vals2 cls_red"><b>-</b></td>
	</tr>
	@endforeach
</table>
<br>
<table align="center" height="6%" width="25%" border="1" cellspacing="3" cellpadding="3" id="tb_tot" class="dashboard-total-table">
	<tr>
		<td align="center" class="dashboard-total-table__muted">R$ {{ number_format($summary['metaTotal'], 2, ',', '.') }}<br></td>
		<td align="center" class="dashboard-total-table__plain">R$ {{ number_format($summary['netRealTotal'], 2, ',', '.') }}<br></td>
		<td align="center" class="dashboard-total-table__plain"><img src="http://admin/img/{{ $summary['netIcon'] }}" class="box" />{{ number_format($summary['netPercent'], 1, ',', '') }}%</td>
	</tr>
</table>
<br><br><br><br>
@endif
