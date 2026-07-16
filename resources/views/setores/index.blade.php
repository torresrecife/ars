<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
		<label><h2><u>&Aacute;reas</u></h2></label>
			<div class="icon-wrapper">
				<table class="adminlist" width="60%" align="center">
					<tr height="30">
						<td class="order"><b>C&oacute;digo</b></td>
						<td class="order"><b>Nome</b></td>
						<td class="order"><b>Data Cadastro</b></td>
						<td class="order"><b>Op&ccedil;&otilde;es</b></td>
					</tr>
					@foreach ($areas as $area)
						<tr>
							<td class="order">{{ (int) $area['area_id'] }}</td>
							<td class="order">{{ e($area['area_nome']) }}</td>
							<td class="order">{{ e($area['area_date']) }}</td>
							<td class="order" style="width:130px">
								@include('partials.admin-action-buttons', [
									'display' => 'block',
									'editAction' => "fc_edit_setor(" . (int) $area['area_id'] . ",'U')",
									'deleteAction' => "fc_del_setor(" . (int) $area['area_id'] . "," . json_encode((string) $area['area_nome']) . ")",
									'editTitle' => 'Editar Setor',
									'deleteTitle' => 'Excluir Setor',
								])
							</td>
						</tr>
					@endforeach
				</table>
			</div>
		</div>
	</div>
</div>
<div id="dialog-edit-setor" title="Editar Setor" style="display:none; text-align:left;">
	<p class="validateTips">Edite a &Aacute;rea Abaixo</p>
	<fieldset>
		<div>
			<table>
				<tr>
					<td><label>Nome da &Aacute;rea</label></td>
					<td><input type="text" class="cls_setor" name="area_nome" id="nome_setor" value="" obrigatorio="1" title="Nome do Setor"/></td>
				</tr>
			</table>
			<input type="hidden" class="cls_setor" name="area_id" id="id_setor" value="" />
		</div>
	</fieldset>
</div>
<script>
window.arsSectorAjaxUrl = "{{ url('ajax/setores') }}";
</script>
