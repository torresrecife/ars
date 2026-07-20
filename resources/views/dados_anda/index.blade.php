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
<table align="center" id="tbf1" border="1" cellspacing="5" cellpadding="5" bordercolor="#ccc" class="detail-table detail-table--andamento">
<tr class="detail-table__header">
<th align="center" class="comFiltro"><b>N.</b></th>
<th align="center" class="comFiltro"><b>{{ __('Code') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Adverse Party') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Filing') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Case') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Account') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('County') }}</b></th>
<th align="center" class="comFiltro"><b>UF</b></th>
<th align="center" class="comFiltro"><b>{{ __('Progress') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Event Date') }}</b></th>
<th align="center" class="comFiltro"><b>{{ __('Created Date') }}</b></th></tr>
@foreach ($rows as $row)
<tr>
<td align="center" class="cls_td">{{ $index++ }}</td>
<td align="center" class="cls_real detail-table__row--interactive" onclick="enviar_neo({{ (int) $row['Codigo'] }})">{{ $row['Codigo'] }}</td>
<td align="center">{{ $row['Adverso'] }}</td>
<td align="center">{{ $row['Ajuizamento'] }}</td>
<td align="center">{{ $row['Processo'] === '' ? '-' : $row['Processo'] }}</td>
<td align="center">{{ $row['ContaContratoNeoCobranca'] }}</td>
<td align="center">{{ $row['comarca_exibicao'] }}</td>
<td align="center">{{ $row['estado_exibicao'] }}</td>
<td align="center">{{ $row['Andamento'] }}</td>
<td align="right">{{ $row['DataEvento'] }}</td>
<td align="right">{{ $row['DataCadastro'] }}</td>
</tr>
@endforeach
</table>
<table align="center" border="0" cellspacing="2" cellpadding="2" class="detail-summary detail-summary--andamento">
<tr>
<td align="left">{{ __('Bank') }}: {{ $bankName }}</td>
<td align="left"><span class="titulo_r" id="id_sel">{{ __('Selected Total') }}: {{ $totalCount }}</span></td>
<td align="right">{{ __('Progress') }}</td>
</tr>
</table>
<br>
</body>
</html>
