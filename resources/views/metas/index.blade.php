@php
	$bankCode = isset($bank['banco_cod']) ? $bank['banco_cod'] : '';
	$allowGlobalRegion = !empty($allowGlobalRegion);
@endphp
<div class="***REMOVED***-module-offset">
<script>
window.arsMetaResourceBaseUrl = "{{ url('***REMOVED***/metas') }}";
</script>
<br><div class="***REMOVED***-context-title">Cliente: <b>{{ e((string) $bankCode) }}</b> | M&ecirc;s / Ano: <b>{{ e((string) $startDate) }}</b></div><br>
<label><h2><u>Metas</u></h2></label>
<div>
<table class="***REMOVED***list ***REMOVED***list--metas">
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
		<td class="order ***REMOVED***-type-badge {{ (int) $arr['especie'] === 1 ? '***REMOVED***-type-badge--production' : '***REMOVED***-type-badge--financial' }}">{{ e((string) $metaTipos[$arr['especie']]) }}</td>
		<td class="order">{{ $metaValor }}</td>
		<td class="order ***REMOVED***-action-cell">
			@include('partials.***REMOVED***-action-buttons', [
				'display' => 'block',
				'editAction' => "fc_edit_metas(" . (int) $arr['meta_id'] . ",'U')",
				'deleteAction' => "fc_del_metas(" . (int) $arr['meta_id'] . "," . json_encode((string) $arr['nome']) . ")",
				'editTitle' => 'Editar Meta',
				'deleteTitle' => 'Excluir Meta',
			])
		</td>
	</tr>
@endforeach
</table>
<br><div class="***REMOVED***-context-title">Total da meta financeira: <b>R$ {{ number_format((float) $totalFinanceiro, 2, ',', '.') }}</b></div><br>
<div id="dialog-edit-metas" title="Editar Meta" class="***REMOVED***-dialog is-hidden">
	<p class="validateMetas">Edite a Meta Abaixo</p>
	<fieldset>
		<div id="tb_dialog" class="***REMOVED***-dialog-panel ***REMOVED***-dialog-panel--metas">
			<table align="left" class="***REMOVED***-dialog-table ***REMOVED***-dialog-table--metas">
				<tr><td>
					<div class="metas-form-label metas-form-label--meta">Selecionar as metas</div>
					<div class="metas-form-label metas-form-label--region">Regi&atilde;o</div>
					<div class="metas-form-label metas-form-label--value">Valor Total</div>
					<div class="metas-form-label metas-form-label--manual">Def. manual |.</div>
					<div class="metas-form-label metas-form-label--week">Sem 1</div>
					<div class="metas-form-label metas-form-label--week">Sem 2</div>
					<div class="metas-form-label metas-form-label--week">Sem 3</div>
					<div class="metas-form-label metas-form-label--week">Sem 4</div>
					<div class="metas-form-label metas-form-label--week">Sem 5</div>
				</td></tr>
				<tr><td>
					<div id="metas_0">
						<div class="metas-form-row">
							<select class="cls_metas2 input-default metas-field--meta" name="meta_name_1" id="meta_name_1" obrigatorio="1" title="Meta" onchange="my_especie(1);">
								<option value=""></option>
								@foreach ($andamentos as $andamento)
									<option value="{{ (int) $andamento['anda_id'] }}" especie="{{ (int) $andamento['especie'] }}">{{ e((string) $andamento['nome'] . ' (' . $metaTipos[$andamento['especie']] . ')') }}</option>
								@endforeach
							</select>
							<select class="cls_meta_regiao input-default metas-field--region" name="regiao_id_1" id="regiao_id_1">
								@if ($allowGlobalRegion)
									<option value="">Todas as Regiões</option>
								@endif
								@foreach ($regions as $region)
									<option value="{{ (int) $region['regiao_id'] }}">{{ e((string) $region['regiao_nome']) }}</option>
								@endforeach
							</select>
							<input type="text" class="cls_meta metas-field--value" name="meta_valor_1" id="meta_valor_1" value="" obrigatorio="1" title="Meta total" alt=""/>
							<input type="checkbox" class="cls_meta metas-field--manual" name="def_sem_1" id="def_sem_1" onclick="definir_sem(this,1);" value="" title="Definir manualmente">
							<input type="text" class="cls_meta sem_1 metas-field--week is-hidden" name="sem1_valor_1" id="sem1_valor_1" value="" title="Valor da 1ª semana" onkeypress="somarMeta(1)" onblur="somarMeta(1)">
							<input type="text" class="cls_meta sem_1 metas-field--week is-hidden" name="sem2_valor_1" id="sem2_valor_1" value="" title="Valor da 2ª semana" onkeypress="somarMeta(1)" onblur="somarMeta(1)">
							<input type="text" class="cls_meta sem_1 metas-field--week is-hidden" name="sem3_valor_1" id="sem3_valor_1" value="" title="Valor da 3ª semana" onkeypress="somarMeta(1)" onblur="somarMeta(1)">
							<input type="text" class="cls_meta sem_1 metas-field--week is-hidden" name="sem4_valor_1" id="sem4_valor_1" value="" title="Valor da 4ª semana" onkeypress="somarMeta(1)" onblur="somarMeta(1)">
							<input type="text" class="cls_meta sem_1 metas-field--week is-hidden" name="sem5_valor_1" id="sem5_valor_1" value="" title="Valor da 5ª semana" onkeypress="somarMeta(1)" onblur="somarMeta(1)">
							<button id="inp1_1" class="bts metas-add-button" onclick="inserir_metas($('#meta_name_1').html(),1);">+</button>
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
