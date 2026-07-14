<div style="margin-top:80px">
<script>
window.arsAndamentoAjaxUrl = "/ars2/ajax_andamento.php";
</script>
<label><h2><u>Andamentos</u></h2></label>
<div>
<table class="adminlist" style="width:100%">
	<tr height="30">
		<td class="order"><b>Código</b></td>
		<td class="order"><b>Nome</b></td>
		<td class="order"><b>Nome COD</b></td>
		<td class="order"><b>Andamentos</b></td>
		<td class="order"><b>Tipo</b></td>
		<td class="order"><b>Painel</b></td>
		<td class="order"><b>Título do Painel</b></td>
		<td class="order"><b>Opções</b></td>
	</tr>
<?php foreach ($andamentos as $andamento): ?>
	<tr>
		<td class="order"><?php echo (int) $andamento['anda_id']; ?></td>
		<td class="order"><?php echo htmlspecialchars($andamento['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
		<td class="order"><?php echo htmlspecialchars($andamento['chave'], ENT_QUOTES, 'UTF-8'); ?></td>
		<td class="order"><?php echo htmlspecialchars($andamento['anda_neo'], ENT_QUOTES, 'UTF-8'); ?></td>
		<td class="order" style="color:#ffffff;background:<?php echo ((int) $andamento['especie'] === 1 ? '#1C86EE' : '#FFB90F'); ?>"><?php echo htmlspecialchars($metaTipos[(int) $andamento['especie']], ENT_QUOTES, 'UTF-8'); ?></td>
		<td class="order"><?php echo htmlspecialchars($andamento['painel'], ENT_QUOTES, 'UTF-8'); ?></td>
		<td class="order"><?php echo htmlspecialchars($andamento['titulo'], ENT_QUOTES, 'UTF-8'); ?></td>
		<td class="order" style="width:130px"><?php echo fc_botoes_andamento($andamento['anda_id'], 'block', $andamento['nome']); ?></td>
	</tr>
<?php endforeach; ?>
</table>
<style>
.andamento-tipos-lista{margin:8px 0 0;width:100%;text-align:left;}
.andamento-tipos-item{display:flex;align-items:flex-start;justify-content:space-between;width:100%;box-sizing:border-box;margin-bottom:6px;padding:6px 8px;border:1px solid #d6d6d6;background:#f7f7f7;}
.andamento-tipos-nome{display:block;flex:1 1 auto;max-width:360px;font-size:9pt;line-height:1.3;text-align:left;}
.andamento-tipos-vazio{margin-top:8px;font-size:9pt;color:#666;}
.andamento-tipos-remover{margin-left:8px;padding:2px 8px;font-size:9pt;cursor:pointer;}
</style>
<div id="dialog-edit-andamento" title="Editar Andamento" style="display:none; text-align:left;">
	<p class="validateTips">Edite o Andamento Abaixo</p>
	<fieldset>
		<div id="tb_dialog" style="width:520px">
			<table style="width:500px">
				<tr>
					<td>Nome do Andamento:<br>
						<input type="text" class="cls_andamento" name="nome" id="nome" value="" obrigatorio="1" title="Nome do Andamento" style="width:200px" />
					</td>
					<td>Nome Chave: <br>
						<input type="text" class="cls_andamento" name="chave" id="chave" value="" obrigatorio="1" title="Nome da Chave" style="width:200px" />
					</td>
				</tr>
				<tr>
					<td>Painel: <br>
						<select class="cls_andamento input-default" name="painel" id="painel" onchange="sel_tipo(0,this.value)" obrigatorio="1" title="Painel" style="width:200px;height:22px;">
							<option value=""></option>
							<option value="Y">Sim</option>
							<option value="N">Não</option>
						</select>
					</td>
					<td>Título Painel: <br>
						<input type="text" class="cls_andamento" name="titulo" id="titulo" value="" obrigatorio="1" title="Nome do Título" style="width:200px" />
					</td>
				</tr>
				<tr>
					<td colspan="2">Tipo: <br>
						<select class="cls_andamento input-default" name="especie" id="especie" onchange="sel_tipo(0,this.value)" obrigatorio="1" title="Setor" style="width:200px;height:22px;">
							<option value=""></option>
							<option value="1">Produção</option>
							<option value="2">Financeiro</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2"><label id="sel_anda">Selecionar Andamentos:</label>
						<br/>
						<div id="andamento-tipos-vinculados" class="andamento-tipos-lista"></div>
						<div id="andamento-tipos-inputs"></div>
						<div id="andamento-tipos-vazio" class="andamento-tipos-vazio">Nenhum andamento vinculado.</div>
						<div style="margin-top:8px">
							<select class="input-default" name="andam_name_pool" id="andam_name_pool" obrigatorio="1" title="Setor" style="width:400px;height:22px;"></select>
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
