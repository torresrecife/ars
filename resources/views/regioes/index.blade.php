<div style="margin-top:80px">
<script>
window.arsRegionAjaxUrl = "{{ url('ajax/regioes') }}";
</script>
<label><h2><u>Regi&otilde;es</u></h2></label>
<div>
<table class="***REMOVED***list">
	<tr height="30">
		<td class="order"><b>C&oacute;digo</b></td>
		<td class="order"><b>Nome</b></td>
		<td class="order"><b>Slug</b></td>
		<td class="order"><b>UFs</b></td>
		<td class="order"><b>Usu&aacute;rios</b></td>
		<td class="order"><b>Status</b></td>
		<td class="order"><b>Op&ccedil;&otilde;es</b></td>
	</tr>
@foreach ($regions as $region)
	<tr>
		<td class="order">{{ (int) $region['regiao_id'] }}</td>
		<td class="order">{{ e($region['regiao_nome']) }}</td>
		<td class="order">{{ e($region['regiao_slug']) }}</td>
		<td class="order">{{ e($region['ufs']) }}</td>
		<td class="order">{{ (int) $region['total_usuarios'] }}</td>
		<td class="order">{{ ((string) $region['regiao_status'] === 'Y') ? 'Ativa' : 'Inativa' }}</td>
		<td class="order" style="width:130px">{!! fc_botoes_regiao($region['regiao_id'], 'block', $region['regiao_nome']) !!}</td>
	</tr>
@endforeach
</table>
<div id="dialog-edit-regiao" title="Editar Regi&atilde;o" style="display:none;text-align:left;">
	<p class="validateRegiao">Edite a Regi&atilde;o abaixo</p>
	<fieldset>
		<div>
			<table style="width:520px">
				<tr>
					<td width="25%"><label>Nome:</label></td>
					<td width="75%"><input type="text" class="cls_regiao" name="regiao_nome" id="regiao_nome" value="" obrigatorio="1" title="Nome da Regiao"/></td>
				</tr>
				<tr>
					<td><label>Slug:</label></td>
					<td><input type="text" class="cls_regiao" name="regiao_slug" id="regiao_slug" value="" obrigatorio="1" title="Slug da Regiao"/></td>
				</tr>
				<tr>
					<td><label>UFs:</label></td>
					<td>
						<div class="regiao-ufs-box">
							<div id="regiao-ufs-vinculadas" class="regiao-ufs-lista"></div>
							<div id="regiao-ufs-vazio" class="regiao-ufs-vazio">Nenhuma UF vinculada.</div>
						</div>
						<div class="regiao-ufs-adicionar">
							<select class="input-default" name="regiao_uf_pool" id="regiao_uf_pool" title="UF">
								<option value=""></option>
								@foreach ($ufs as $uf)
									<option value="{{ $uf }}">{{ $uf }}</option>
								@endforeach
							</select>
							<button class="bts" type="button" onclick="regiaoUfsAdicionar();">+</button>
						</div>
						<input type="hidden" class="cls_regiao" name="regiao_ufs" id="regiao_ufs" value="" />
					</td>
				</tr>
				<tr>
					<td><label>Status</label></td>
					<td>
						<select class="cls_regiao" name="regiao_status" id="regiao_status" obrigatorio="1" title="Status">
							<option value="Y">Ativa</option>
							<option value="N">Inativa</option>
						</select>
					</td>
				</tr>
			</table>
			<input type="hidden" class="cls_regiao" name="regiao_id" id="regiao_id_edit" value="" />
		</div>
	</fieldset>
</div>
</div>
</div>
<style>
.regiao-ufs-box{margin-bottom:8px;}
.regiao-ufs-lista{display:flex;flex-direction:column;gap:6px;}
.regiao-ufs-item{display:flex;align-items:center;justify-content:space-between;border:1px solid #ccc;padding:6px 8px;background:#f8f8f8;}
.regiao-ufs-nome{flex:1;padding-right:10px;text-align:left;}
.regiao-ufs-vazio{color:#666;font-size:11px;padding:4px 0;}
.regiao-ufs-adicionar{display:flex;align-items:center;gap:6px;}
.regiao-ufs-adicionar select{width:120px;height:22px;}
</style>
