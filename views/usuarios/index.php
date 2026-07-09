<div style="margin-top:80px">
<script>
window.arsUserAjaxUrl = "/ars/ajax_usu.php";
</script>
<label><h2><u>Usu&aacute;rios</u></h2></label>
<div>
<table class="***REMOVED***list">
	<tr height="30">
		<td class="order"><b>C&oacute;digo</b></td>
		<td class="order"><b>Nome</b></td>
		<td class="order"><b>Usu&aacute;rio</b></td>
		<td class="order"><b>N&iacute;vel</b></td>
		<td class="order"><b>&Uacute;ltimo Acesso</b></td>
		<td class="order"><b>Data Cadastro</b></td>
		<td class="order"><b>E-mail</b></td>
		<td class="order"><b>Status</b></td>
		<td class="order"><b>Op&ccedil;&otilde;es</b></td>
	</tr>
<?php foreach ($users as $user): ?>
	<?php $acesso = empty($user['acesso_usu']) || $user['acesso_usu'] === '0000-00-00 00:00:00' ? '' : strftime('%d/%m/%Y %H:%M:%S', strtotime($user['acesso_usu'])); ?>
	<tr>
		<td class="order"><?php echo $user['id_usu']; ?></td>
		<td class="order"><?php echo htmlspecialchars($user['nome_usu'], ENT_QUOTES, 'UTF-8'); ?></td>
		<td class="order"><?php echo htmlspecialchars($user['login_usu'], ENT_QUOTES, 'UTF-8'); ?></td>
		<td class="order"><?php echo htmlspecialchars($user['nivel_usu'], ENT_QUOTES, 'UTF-8'); ?></td>
		<td class="order"><?php echo $acesso; ?></td>
		<td class="order"><?php echo strftime('%d/%m/%Y %H:%M:%S', strtotime($user['data_cad'])); ?></td>
		<td class="order"><?php echo htmlspecialchars($user['email_usu'], ENT_QUOTES, 'UTF-8'); ?></td>
		<td class="order"><?php echo htmlspecialchars($user['status_usu'], ENT_QUOTES, 'UTF-8'); ?></td>
		<td class="order" style="width:130px"><?php echo fc_botoes_usu($user['id_usu'], 'block', $user['login_usu']); ?></td>
	</tr>
<?php endforeach; ?>
</table>
<div id="dialog-edit-usu" title="Editar Usu&aacute;rio" style="display:none;text-align:left;overflow-y: scroll;">
	<p class="validateTips">Edite o Usu&aacute;rio Abaixo</p>
	<fieldset>
		<div>
			<table style="width:460px">
				<tr>
					<td width="25%"><label>Nome:</label></td>
					<td width="75%"><input type="text" class="cls_usu" name="nome_usu" id="nome_usu" value="" obrigatorio="1" title="Nome e Sobrenome"/></td>
				</tr>
				<tr>
					<td><label>Usu&aacute;rio:</label></td>
					<td><input type="text" class="cls_usu" name="login_usu" id="login_usu" value="" obrigatorio="1" title="Usu&aacute;rio"/></td>
				</tr>
				<tr>
					<td><label>E-mail:</label></td>
					<td><input type="text" class="cls_usu" name="email_usu" id="email_usu" value="" obrigatorio="1" title="E-mail"/></td>
				</tr>
				<tr>
					<td><label>N&iacute;vel:</label></td>
					<td>
						<select class="cls_usu" name="nivel_usu" id="nivel_usu" obrigatorio="1" title="Nivel">
							<option value=""></option>
							<option value="ADM">Admin</option>
							<option value="GER">Gerente</option>
							<option value="USU">Usu&aacute;rio</option>
						</select>
					</td>
				</tr>
				<tr>
					<td><label>Setor:</label></td>
					<td>
						<select class="cls_usu" name="setor_usu" id="setor_usu" onchange="sel_tipo(1,this.value)" obrigatorio="1" title="Setor">
							<option value="0">Todos</option>
							<?php foreach ($areas as $area): ?>
								<option value="<?php echo $area['area_id']; ?>"><?php echo htmlspecialchars($area['area_nome'], ENT_QUOTES, 'UTF-8'); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label id="sel_banco">Clientes:</label></td>
					<td>
						<div class="usuario-clientes-box">
							<div id="usuario-clientes-vinculados" class="usuario-clientes-lista"></div>
							<div id="usuario-clientes-inputs"></div>
							<div id="usuario-clientes-vazio" class="usuario-clientes-vazio">Nenhum cliente vinculado.</div>
						</div>
						<div class="usuario-clientes-adicionar">
							<select class="input-default" name="banco_usu_pool" id="banco_usu_pool" title="Cliente"></select>
							<button id="bt-add-cliente-usu" class="bts" type="button" onclick="usuarioClientesAdicionar();">+</button>
						</div>
					</td>
				</tr>
				<tr>
					<td><label>Modo Regi&atilde;o:</label></td>
					<td>
						<select class="cls_usu" name="regiao_modo" id="regiao_modo" title="Modo Regiao">
							<option value="N">Sem filtro regional</option>
							<option value="R">Regi&otilde;es vinculadas</option>
							<option value="T">Todas as regi&otilde;es</option>
						</select>
					</td>
				</tr>
				<tr id="usuario-regioes-row">
					<td><label>Regi&otilde;es:</label></td>
					<td>
						<div class="usuario-regioes-box">
							<div id="usuario-regioes-vinculadas" class="usuario-regioes-lista"></div>
							<div id="usuario-regioes-inputs"></div>
							<div id="usuario-regioes-vazio" class="usuario-regioes-vazio">Nenhuma regi&atilde;o vinculada.</div>
						</div>
						<div class="usuario-regioes-adicionar">
							<select class="input-default" name="regiao_usu_pool" id="regiao_usu_pool" title="Regiao">
								<option value=""></option>
								<?php foreach ($regions as $region): ?>
									<option value="<?php echo $region['regiao_id']; ?>"><?php echo htmlspecialchars($region['regiao_nome'], ENT_QUOTES, 'UTF-8'); ?></option>
								<?php endforeach; ?>
							</select>
							<button id="bt-add-regiao-usu" class="bts" type="button" onclick="usuarioRegioesAdicionar();">+</button>
						</div>
					</td>
				</tr>
				<tr>
					<td><label>Status</label></td>
					<td>
						<select class="cls_usu" name="status_usu" id="status_usu" obrigatorio="1" title="Status">
							<option value=""></option>
							<option value="ATI">Ativo</option>
							<option value="INA">Inativo</option>
						</select>
					</td>
				</tr>
				<tr>
					<td><label>Senha</label></td>
					<td><input type="password" class="cls_usu" name="senha_usu1" id="senha_usu1" value="" /></td>
				</tr>
				<tr>
					<td><label>Repete a Senha</label></td>
					<td><input type="password" class="cls_usu" name="senha_usu2" id="senha_usu2" value="" /></td>
				</tr>
			</table>
			<input type="hidden" class="cls_usu" name="id_usu" id="id_usu" value="" />
		</div>
	</fieldset>
</div>
</div>
</div>
<style>
.usuario-clientes-box{
	margin-bottom:8px;
}
.usuario-clientes-lista{
	display:flex;
	flex-direction:column;
	gap:6px;
}
.usuario-clientes-item{
	display:flex;
	align-items:center;
	justify-content:space-between;
	border:1px solid #ccc;
	padding:6px 8px;
	background:#f8f8f8;
}
.usuario-clientes-nome{
	flex:1;
	padding-right:10px;
}
.usuario-clientes-vazio{
	color:#666;
	font-size:11px;
	padding:4px 0;
}
.usuario-clientes-remover{
	margin-left:8px;
}
.usuario-clientes-adicionar{
	display:flex;
	align-items:center;
	gap:6px;
}
.usuario-clientes-adicionar select{
	width:320px;
	height:22px;
}
.usuario-regioes-box{
	margin-bottom:8px;
}
.usuario-regioes-lista{
	display:flex;
	flex-direction:column;
	gap:6px;
}
.usuario-regioes-item{
	display:flex;
	align-items:center;
	justify-content:space-between;
	border:1px solid #ccc;
	padding:6px 8px;
	background:#f8f8f8;
}
.usuario-regioes-nome{
	flex:1;
	padding-right:10px;
	text-align:left;
}
.usuario-regioes-vazio{
	color:#666;
	font-size:11px;
	padding:4px 0;
}
.usuario-regioes-remover{
	margin-left:8px;
}
.usuario-regioes-adicionar{
	display:flex;
	align-items:center;
	gap:6px;
}
.usuario-regioes-adicionar select{
	width:320px;
	height:22px;
}
</style>
