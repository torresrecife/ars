<div class="***REMOVED***-module-offset">
<script>
window.arsAndamentoResourceBaseUrl = "{{ url('***REMOVED***/andamentos') }}";
window.arsSelectAjaxUrl = "{{ url('ajax/select') }}";
</script>
<label><h2><u>Andamentos</u></h2></label>
<div>
<table class="***REMOVED***list ***REMOVED***list--full">
	<tr height="30">
		<td class="order"><b>C&oacute;digo</b></td>
		<td class="order"><b>Nome</b></td>
		<td class="order"><b>Nome COD</b></td>
		<td class="order"><b>Andamentos</b></td>
		<td class="order"><b>Tipo</b></td>
		<td class="order"><b>Painel</b></td>
		<td class="order"><b>T&iacute;tulo do Painel</b></td>
		<td class="order"><b>Op&ccedil;&otilde;es</b></td>
	</tr>
@foreach ($andamentos as $andamento)
	<tr>
		<td class="order">{{ (int) $andamento['anda_id'] }}</td>
		<td class="order">{{ e($andamento['nome']) }}</td>
		<td class="order">{{ e($andamento['chave']) }}</td>
		<td class="order">{{ e($andamento['anda_neo']) }}</td>
		<td class="order ***REMOVED***-type-badge {{ (int) $andamento['especie'] === 1 ? '***REMOVED***-type-badge--production' : '***REMOVED***-type-badge--financial' }}">{{ e($metaTipos[(int) $andamento['especie']]) }}</td>
		<td class="order">{{ e($andamento['painel']) }}</td>
		<td class="order">{{ e($andamento['titulo']) }}</td>
		<td class="order ***REMOVED***-action-cell">
			@include('partials.***REMOVED***-action-buttons', [
				'display' => 'block',
				'editAction' => "fc_edit_andamento(" . (int) $andamento['anda_id'] . ",'U')",
				'deleteAction' => "fc_del_andamento(" . (int) $andamento['anda_id'] . "," . json_encode((string) $andamento['nome']) . ")",
				'editTitle' => 'Editar Andamento',
				'deleteTitle' => 'Excluir Andamento',
			])
		</td>
	</tr>
@endforeach
</table>
<div id="dialog-edit-andamento" title="Editar Andamento" class="***REMOVED***-dialog is-hidden">
	<p class="validateTips">Edite o Andamento Abaixo</p>
	<fieldset>
		<div id="tb_dialog" class="***REMOVED***-dialog-panel">
			<table class="***REMOVED***-dialog-table">
				<tr>
					<td>Nome do Andamento:<br><input type="text" class="cls_andamento ***REMOVED***-field--standard" name="nome" id="nome" value="" obrigatorio="1" title="Nome do Andamento" /></td>
					<td>Nome Chave: <br><input type="text" class="cls_andamento ***REMOVED***-field--standard" name="chave" id="chave" value="" obrigatorio="1" title="Nome da Chave" /></td>
				</tr>
				<tr>
					<td>Painel: <br>
						<select class="cls_andamento input-default ***REMOVED***-field--standard ***REMOVED***-field--select" name="painel" id="painel" onchange="sel_tipo(0,this.value)" obrigatorio="1" title="Painel">
							<option value=""></option><option value="Y">Sim</option><option value="N">N&atilde;o</option>
						</select>
					</td>
					<td>T&iacute;tulo Painel: <br><input type="text" class="cls_andamento ***REMOVED***-field--standard" name="titulo" id="titulo" value="" obrigatorio="1" title="Nome do T&iacute;tulo" /></td>
				</tr>
				<tr>
					<td colspan="2">Tipo: <br>
						<select class="cls_andamento input-default ***REMOVED***-field--standard ***REMOVED***-field--select" name="especie" id="especie" onchange="sel_tipo(0,this.value)" obrigatorio="1" title="Setor">
							<option value=""></option><option value="1">Produ&ccedil;&atilde;o</option><option value="2">Financeiro</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2"><label id="sel_anda">Selecionar Andamentos:</label><br/>
						<div id="andamento-tipos-vinculados" class="andamento-tipos-lista"></div>
						<div id="andamento-tipos-inputs"></div>
						<div id="andamento-tipos-vazio" class="andamento-tipos-vazio">Nenhum andamento vinculado.</div>
						<div class="***REMOVED***-dialog-row-gap">
							<select class="input-default ***REMOVED***-field--large ***REMOVED***-field--select" name="andam_name_pool" id="andam_name_pool" obrigatorio="1" title="Setor"></select>
							<button id="inp1_1" class="bts" type="button" onclick="andamentoTiposAdicionar();">+</button>
						</div>
					</td>
				</tr>
			</table>
			<input type="hidden" class="cls_andamento" name="anda_id" id="anda_id" value="" />
		</div>
	</fieldset>
</div>
<br>
