<div style="margin-top:80px" >
<label><h2><u>Andamentos</u></h2></label>
<div>
<table class="adminlist" style="width:100%">
	<tr height="30">
		<td class="order" ><b>Código	</b></td>
		<td class="order" ><b>Nome		</b></td>
		<td class="order" ><b>Nome COD	</b></td>
		<td class="order" ><b>Andamentos</b></td>
		<td class="order" ><b>Tipo	 	</b></td>
		<td class="order" ><b>Painel 	</b></td>
		<td class="order" ><b>Título do Painel</b></td>
		<td class="order" ><b>Opções    </b></td>
	</tr>
<?php
$Mtipo = array(1=>"Produção",2=>"Financeira");
$query = mysqli_query($conexao4,"SELECT * from andamentos as a ORDER by a.especie asc, a.nome asc ") or die(mysqli_error());
$lin=0;
while ($arr = mysqli_fetch_array($query)){
	$lin++;
	?>
	<tr >
		<td class="order"><?PHP echo $arr['anda_id'];		?>	</td>
		<td class="order"><?php echo $arr['nome'];	?>	</td>
		<td class="order"><?php echo $arr['chave'];	?>	</td>
		<td class="order"><?php echo $arr['anda_neo'];?>	</td>
		<td class="order" style="color:#ffffff;background:<?php echo ($arr['especie']==1?"#1C86EE":"#FFB90F"); ?>"><?php echo $Mtipo[$arr['especie']];	?>	</td>
		<td class="order"><?php echo $arr['painel'];?>	</td>
		<td class="order"><?php echo $arr['titulo'];?>	</td>
		<td class="order" style="width:130px"><?php echo fc_botoes_andamento($arr['anda_id'],"block",$arr['nome']); ?></td>
	</tr>
	<?php
}
?>
</table>
<div id="dialog-edit-andamento" title="Editar Andamento" style="display:none; text-align:left;">
	<p class="validateTips">Edite o Andamento Abaixo</p>
	<fieldset>
		<div id="tb_dialog" style="height:260px; width:400px">
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
							<option value="">  </option>
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
							<option value="">  </option>
							<option value="1">Produção</option>
							<option value="2">Financeiro</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2"><label id="sel_anda">Selecionar Andamentos:</label>
						<div id="andam_0">
						<select class="cls_andam input-default" name="andam_name_1" id="andam_name_1" obrigatorio="1" title="Setor" style="width:400px;height:22px;">
						</select>
						<button id="inp1_1" class="bts" onclick="inserir_anda($('#andam_name_1').html(),1);" >+</button>
						</div>
						<div id="andam_1"></div>
					</td>
				</tr>
			</table>
			<input type="hidden" class="cls_andamento" name="anda_id" id="anda_id" value="" />
			<input type="hidden" name="andam_num" id="andam_num" value="1" />
		</div>
	</fieldset>
</div>
<br>