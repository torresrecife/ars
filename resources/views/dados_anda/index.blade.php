<html height="100%">
<link rel="stylesheet" href="{{ asset('css/ars-modern.css') }}">
<script type="text/javascript" src="{{ asset('js/jquery-1.8.0.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jFilterXCel2003.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/modules/details.js') }}"></script>
<body>
@php $index = 1; @endphp
<table align="center" id="tbf1" border="1" cellspacing="5" cellpadding="5" bordercolor="#ccc" class="detail-table detail-table--andamento">
<tr class="detail-table__header">
<th align="center" class="comFiltro"><b>N.</b></th>
<th align="center" class="comFiltro"><b>Código</b></th>
<th align="center" class="comFiltro"><b>Adverso</b></th>
<th align="center" class="comFiltro"><b>Ajuizamento</b></th>
<th align="center" class="comFiltro"><b>Processo</b></th>
<th align="center" class="comFiltro"><b>Conta</b></th>
<th align="center" class="comFiltro"><b>Comarca</b></th>
<th align="center" class="comFiltro"><b>UF</b></th>
<th align="center" class="comFiltro"><b>Andamento</b></th>
<th align="center" class="comFiltro"><b>D.Evento</b></th>
<th align="center" class="comFiltro"><b>D.Cadastro</b></th>
</tr>
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
<td align="left">Banco: {{ $bankName }}</td>
<td align="left"><span class="titulo_r" id="id_sel">Total Selecionado: {{ $totalCount }}</span></td>
<td align="right">Andamentos</td>
</tr>
</table>
<br>
</body>
</html>
