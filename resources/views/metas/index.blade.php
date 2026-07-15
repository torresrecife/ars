@php
	$bankCode = isset($bank['banco_cod']) ? $bank['banco_cod'] : '';
	$allowGlobalRegion = !empty($allowGlobalRegion);
@endphp
<div style="margin-top:80px">
<script>
window.arsMetaAjaxUrl = "{{ url('ajax/metas') }}";
</script>
<br><div style="font-family:arial;margin-left:40px;font-size:10pt;">Cliente: <b>{{ e((string) $bankCode) }}</b> | M&ecirc;s / Ano: <b>{{ e((string) $startDate) }}</b></div><br>
<label><h2><u>Metas</u></h2></label>
<div>
<table class="***REMOVED***list" style="width:72%">
	<tr height="30">
		<td class="order"><b>Cliente</b></td>
		<td class="order"><b>Regi&atilde;o</b></td>
		<td class="order"><b>Andamento</b></td>
		<td class="order"><b>Tipo</b></td>
		<td class="order"><b>Qtd/Valor</b></td>
		<td class="order"><b>Op&ccedil;&otilde;es</b></td>
	</tr>
@foreach ($metas as $arr)
	@php $metaValor = ((int) $arr['especie'] === 2) ? number_format((float) $arr['meta_valor'], 2, ',', '.') : number_format((float) $arr['meta_valor'], 0, '', ''); @endphp
	<tr>
		<td class="order">{{ e((string) $arr['banco_name']) }}</td>
		<td class="order">{{ e(isset($arr['regiao_nome']) && $arr['regiao_nome'] !== '' ? (string) $arr['regiao_nome'] : 'Todas as Regiões') }}</td>
		<td class="order">{{ e((string) $arr['nome']) }}</td>
		<td class="order" style="color:#ffffff;background:{{ ((int) $arr['especie'] === 1 ? '#1C86EE' : '#FFB90F') }}">{{ e((string) $metaTipos[$arr['especie']]) }}</td>
		<td class="order">{{ $metaValor }}</td>
		<td class="order" style="width:130px">{!! fc_botoes_metas($arr['meta_id'], 'block', $arr['nome']) !!}</td>
	</tr>
@endforeach
</table>
<script>
	function my_especie(valor){
		var espe = $("#meta_name_" + valor + " option:selected").attr("especie");
		if(espe==2){
			$("#meta_valor_"+valor).setMask("decimal");
			$(".sem_"+valor).setMask("decimal");
		}else{
			$("#meta_valor_"+valor).setMask("numbers");
			$(".sem_"+valor).setMask("numbers");
		}
	}
</script>
<br><div style="font-family:arial;margin-left:40px;font-size:10pt;">Total da meta financeira: <b>R$ {{ number_format((float) $totalFinanceiro, 2, ',', '.') }}</b></div><br>
<div id="dialog-edit-metas" title="Editar Meta" style="display:none; text-align:left;">
	<p class="validateMetas">Edite a Meta Abaixo</p>
	<fieldset>
		<div id="tb_dialog" style="min-height:70px; width:1030px;">
			<table align="left" style="width:1030px">
				<tr><td>
					<div style="width:250px;float:left">Selecionar as metas</div>
					<div style="width:170px;float:left">Regi&atilde;o</div>
					<div style="width:80px;float:left">Valor Total</div>
					<div style="width:90px;float:left">Def. manual |.</div>
					<div style="width:80px;float:left">Sem 1</div>
					<div style="width:80px;float:left">Sem 2</div>
					<div style="width:80px;float:left">Sem 3</div>
					<div style="width:80px;float:left">Sem 4</div>
					<div style="width:80px;float:left">Sem 5</div>
				</td></tr>
				<tr><td>
					<div id="metas_0">
						<div style="float:left">
							<select class="cls_metas2 input-default" name="meta_name_1" id="meta_name_1" obrigatorio="1" title="Meta" onchange="my_especie(1);" style="width:250px;height:22px;">
								<option value=""></option>
								@foreach ($andamentos as $andamento)
									<option value="{{ (int) $andamento['anda_id'] }}" especie="{{ (int) $andamento['especie'] }}">{{ e((string) $andamento['nome'] . ' (' . $metaTipos[$andamento['especie']] . ')') }}</option>
								@endforeach
							</select>
							<select class="cls_meta_regiao input-default" name="regiao_id_1" id="regiao_id_1" style="width:160px;height:22px;">
								@if ($allowGlobalRegion)
									<option value="">Todas as Regiões</option>
								@endif
								@foreach ($regions as $region)
									<option value="{{ (int) $region['regiao_id'] }}">{{ e((string) $region['regiao_nome']) }}</option>
								@endforeach
							</select>
							<input type="text" class="cls_meta" name="meta_valor_1" id="meta_valor_1" value="" obrigatorio="1" title="Meta total" style="width:120px;" alt=""/>
							<input type="checkbox" class="cls_meta" name="def_sem_1" id="def_sem_1" onclick="definir_sem(this,1);" value="" title="Definir manualmente" style="width:20px;">
							<input type="text" class="cls_meta sem_1" name="sem1_valor_1" id="sem1_valor_1" value="" title="Valor da 1ª semana" onkeypress="somarMeta(1)" onblur="somarMeta(1)" style="display:none;width:70px;">
							<input type="text" class="cls_meta sem_1" name="sem2_valor_1" id="sem2_valor_1" value="" title="Valor da 2ª semana" onkeypress="somarMeta(1)" onblur="somarMeta(1)" style="display:none;width:70px;">
							<input type="text" class="cls_meta sem_1" name="sem3_valor_1" id="sem3_valor_1" value="" title="Valor da 3ª semana" onkeypress="somarMeta(1)" onblur="somarMeta(1)" style="display:none;width:70px;">
							<input type="text" class="cls_meta sem_1" name="sem4_valor_1" id="sem4_valor_1" value="" title="Valor da 4ª semana" onkeypress="somarMeta(1)" onblur="somarMeta(1)" style="display:none;width:70px;">
							<input type="text" class="cls_meta sem_1" name="sem5_valor_1" id="sem5_valor_1" value="" title="Valor da 5ª semana" onkeypress="somarMeta(1)" onblur="somarMeta(1)" style="display:none;width:70px;">
							<button id="inp1_1" class="bts" onclick="inserir_metas($('#meta_name_1').html(),1);" style="float:left">+</button>
						</div>
					</div>
					<div id="metas_1"></div>
				</td></tr>
			</table>
		</div>
	</fieldset>
</div>
<input type="hidden" name="metas_num" id="metas_num" value="1" />
<input type="hidden" class="cls_meta" name="meta_id" id="meta_id" value="" />
<input type="hidden" class="cls_meta" name="banco_id" id="banco_id" value="{{ e((string) $startBanco) }}" />
<input type="hidden" class="cls_meta" name="meta_mes" id="meta_mes" value="{{ e((string) $mes) }}" />
<input type="hidden" class="cls_meta" name="meta_ano" id="meta_ano" value="{{ e((string) $ano) }}" />
</div>
</div>
