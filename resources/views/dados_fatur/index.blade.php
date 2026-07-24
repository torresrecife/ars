<html height="100%">
<link rel="stylesheet" href="{{ asset('css/ars-modern.css') }}">
<script type="text/javascript" src="{{ asset('js/jquery-1.8.0.min.js') }}"></script>
@if (is_file(public_path('mix-manifest.json')) && is_file(public_path('build/js/ars-details.js')))
<script type="text/javascript" src="{{ asset('public/' . ltrim(mix('/build/js/ars-details.js'), '/')) }}"></script>
@else
<script type="text/javascript" src="{{ asset('js/jFilterXCel2003.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/modules/details.js') }}"></script>
@endif
<body>
@php $index = 1; @endphp
<table align="center" id="tbf1" border="1" cellspacing="5" cellpadding="5" bordercolor="#ccc" class="detail-table detail-table--financial">
<tr class="detail-table__header">
<th align="center" class="comFiltro"><b>N.</b></th>
<th align="center" class="comFiltro"><b>{{ __('Code') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Plaintiff') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Defendant') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Case') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('CNJ Case') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Account') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('County') }}</b></th>
<th align="center" class="comFiltro"><b>UF</b></th>
<th align="center" class="comFiltro"><b>{{ __('Registry Office') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Entry Code') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Contractor No.') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Progress') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Value') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Event Date') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Created Date') }}</b></th></tr>
@foreach ($rows as $row)
<tr>
<td align="center" class="cls_td">{{ $index++ }}</td>
<td align="center" class="cls_real detail-table__row--interactive" onclick="enviar_neo({{ (int) $row['Codigo'] }})">{{ $row['Codigo'] }}</td>
<td align="center">{{ e((string) $row['Adverso2']) }}</td>
<td align="center">{{ e((string) $row['Adverso']) }}</td>
<td align="center">{{ e((string) $row['processo_exibicao']) }}</td>
<td align="center">{{ e((string) $row['processo_cnj_exibicao']) }}</td>
<td align="center">{{ e((string) $row['ContaContratoNeoCobranca']) }}</td>
<td align="center">{{ (string) $row['comarca_exibicao'] }}</td>
<td align="center">{{ e((string) $row['estado_exibicao']) }}</td>
<td align="center">{{ e((string) $row['Cartorio']) }}</td>
<td align="center">{{ $row['CodigoLancamento'] }}</td>
<td align="center">{{ e((string) $row['IdentificadorContratante']) }}</td>
<td align="center">{{ e((string) $row['Andamento']) }}</td>
<td align="right" class="cls_rs">{{ number_format((float) $row['valores'], 2, ',', '.') }}</td>
<td align="right">{{ $row['DataEvento'] }}</td>
<td align="right">{{ $row['DataCadastro'] }}</td>
</tr>
@endforeach
</table>
<table align="center" border="0" cellspacing="2" cellpadding="2" class="detail-summary detail-summary--financial">
<tr>
<td align="left">{{ __('Bank') }}: {{ e((string) $bankName) }}</td>
<td align="left"><span class="titulo_r" id="id_sel">{{ __('Selected Total') }}: {{ $totalCount }}</span></td>
<td align="right"><div id="id_crs">{{ __('Total Value') }}: <b>{{ number_format($totalValue, 2, ',', '.') }}</b></div></td>
<td align="right">{{ __('Entries') }}</td>
</tr>
</table>
<div class="detail-actions detail-actions--financial">
<button type="button" class="detail-actions__button" onclick="exportDetailTable('tbf1', '{{ \Illuminate\Support\Str::slug((string) $bankName, '-') }}-financial-details')">{{ __('Export to Excel') }}</button>
</div>
<br>
</body>
</html>
