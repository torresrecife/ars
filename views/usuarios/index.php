<div style="margin-top:80px">
<label><h2><u>Usuários</u></h2></label>
<div>
<table class="***REMOVED***list">
	<tr height="30">
		<td class="order"><b>Código</b></td>
		<td class="order"><b>Nome</b></td>
		<td class="order"><b>Usuário</b></td>
		<td class="order"><b>Nível</b></td>
		<td class="order"><b>Último Acesso</b></td>
		<td class="order"><b>Data Cadastro</b></td>
		<td class="order"><b>E-mail</b></td>
		<td class="order"><b>Status</b></td>
		<td class="order"><b>Opções</b></td>
	</tr>
<?php foreach ($users as $user): ?>
	<?php $acesso = $user['acesso_usu'] === '0000-00-00 00:00:00' ? '' : strftime('%d/%m/%Y %H:%M:%S', strtotime($user['acesso_usu'])); ?>
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
<div id="dialog-edit-usu" title="Editar Usuário" style="display:none;text-align:left;overflow-y: scroll;">
	<p class="validateTips">Edite o Usuário Abaixo</p>
	<fieldset>
		<div>
			<table style="width:400px">
				<tr>
					<td width="25%"><label>Nome:</label></td>
					<td width="75%"><input type="text" class="cls_usu" name="nome_usu" id="nome_usu" value="" obrigatorio="1" title="Nome e Sobrenome"/></td>
				</tr>
				<tr>
					<td><label>Usuário:</label></td>
					<td><input type="text" class="cls_usu" name="login_usu" id="login_usu" value="" obrigatorio="1" title="Usuário"/></td>
				</tr>
				<tr>
					<td><label>E-mail:</label></td>
					<td><input type="text" class="cls_usu" name="email_usu" id="email_usu" value="" obrigatorio="1" title="E-mail"/></td>
				</tr>
				<tr>
					<td><label>Nível:</label></td>
					<td>
						<select class="cls_usu" name="nivel_usu" id="nivel_usu" obrigatorio="1" title="Nivel">
							<option value=""></option>
							<option value="ADM">Admin</option>
							<option value="GER">Gerente</option>
							<option value="USU">Usuário</option>
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
					<td><label id="sel_banco">Cliente:</label></td>
					<td>
						<div id="banco_0">
							<select class="cls_usu input-default cls_usu2" name="banco_usu_1" id="banco_usu_1" obrigatorio="1" title="Cliente"></select>
							<button id="inp1_1" class="bts" onclick="inserir_banco($('#banco_usu_1').html(),1);">+</button>
						</div>
						<div id="banco_1"></div>
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
			<input type="hidden" name="banco_num" id="banco_num" value="1" />
		</div>
	</fieldset>
</div>
</div>
</div>
