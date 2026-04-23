<div style="margin-top:80px" >
<label><h2><u>Clientes</u></h2></label>
<div>
<table class="adminlist">
	<tr height="30">
		<td class="order" ><b>Código		</b></td>
		<td class="order" ><b>Nome			</b></td>
		<td class="order" ><b>Nome COD		</b></td>
		<td class="order" ><b>Carteira(s)	</b></td>
		<td class="order" ><b>DATA		 	</b></td>
		<td class="order" ><b>ÁREA	 		</b></td>
		<td class="order" ><b>STATUS		</b></td>
		<td class="order" ><b>Opções        </b></td>
	</tr>
<?php
$stt = array("Y"=>"Ativo","N"=>"Inativo");
$query = mysqli_query($conexao4,"SELECT *, date_format(b.banco_creator,'%d/%m/%Y') as datacad  from bancos as b join area as a on a.area_id=b.banco_area group by b.banco_id ORDER by b.banco_id") or die(mysqli_error());
$lin=0;
while ($arr = mysqli_fetch_array($query)){
	$lin++;
	?>
	<tr >
		<td class="order"><?PHP echo $arr['banco_id'];			?></td>
		<td class="order"><?php echo $arr['banco_name'];		?></td>
		<td class="order"><?php echo $arr['banco_cod'];		?></td>
		<?php 
			$dad = "";
			$qdados = mysqli_query($conexao4,"SELECT * FROM dados as d where d.banco_id=" . $arr['banco_id'] . " order by d.dados_id");
			while($wdados = mysqli_fetch_array($qdados)){
				$dad .= $wdados['dados_cod'] . "<br>";
			}
		?>
		<td class="order"><?php echo $dad;	?></td>
		<td class="order"><?php echo $arr['datacad'];	?></td>
		<td class="order"><?php echo $arr['area_nome'];		?>	</td>
		<td class="order"><?php echo $stt[$arr['banco_status']];?></td>
		<td class="order" style="width:130px"><?php echo fc_botoes_cliente($arr['banco_id'],"block",$arr['banco_name']); ?></td>
	</tr>
	<?php
}
?>
</table>
<style>
.bts{
	font-size:14pt;
	underlinetext-decoration: underline;
	cursor:pointer;
	height:22px;
}
</style>
<div id="dialog-edit-cliente" title="Editar Cliente" style="display:none; text-align:left;">
	<p class="validateTips">Edite o Cliente Abaixo</p>
	<fieldset>
		<div id="tb_dialog" style="height:260px; width:480px">
			<table>
				<tr>
					<td>Nome do Cliente:<br>
						<input type="text" class="cls_cliente" name="banco_name" id="banco_name" value="" obrigatorio="1" title="Nome do banco" style="width:200px" />
					</td>
					<td>Texto COD <br>
						<input type="text" class="cls_cliente" name="banco_cod" id="banco_cod" value="" obrigatorio="1" title="Nome do banco" style="width:200px" />
					</td>
				</tr>
				<tr>
					<td>Setor:<br>
						<select class="cls_cliente input-default" name="banco_area" id="banco_area" obrigatorio="1" title="Setor" style="width:200px;height:22px;">
							<option value="">  </option>                                       
							<?php 
							$qsetor = mysqli_query($conexao4,"SELECT * FROM area order by area_id");
							while($wsetor = mysqli_fetch_array($qsetor)){
								?>
								<option value="<?php echo $wsetor[0]; ?>"> <?php echo $wsetor[1]; ?></option>
								<?php 
							}
							?>
						</select>
					</td>
					<td>Status:<br>
						<select class="cls_cliente input-default" name="banco_status" id="banco_status" obrigatorio="1" title="Setor" style="width:200px;height:22px;">
							<option value="">  </option>
							<option value="Y">Ativo</option>
							<option value="N">Inativo</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Classificação:<br>
						<input type="text" class="cls_cliente" name="banco_class" id="banco_class" value="" obrigatorio="1" title="Classificação" style="width:200px" />
					</td>
					<td>Prazo do Simulador/Decisor:<br>
						<input type="text" class="cls_cliente" name="simulador" id="simulador" value="" obrigatorio="1" title="Simulador/Decisor" style="width:200px" />
					</td>
				</tr>
				<tr>
					<td>Nome Curto:<br>
						<input type="text" class="cls_cliente" name="banco_curto" id="banco_curto" value="" obrigatorio="1" title="Nome Curto" style="width:200px" />
					</td>
				</tr>
				<tr >
					<td colspan="2">Selecionar Carteira:<br>
						<div id="dados_0">
						<select class="cls_cliente2 input-default" name="dados_name_1" id="dados_name_1" obrigatorio="1" title="Setor" style="width:360px;height:22px;">
							<option value="">  </option>
							<?php 
							//$Qlan = sqlsrv_query($conexao1,"SELECT l.TipoLancamento FROM Lancamento_Processo AS l WHERE l.ClassificaoLancamento='".utf8_decode('Honorário')."' GROUP BY l.TipoLancamento ORDER BY l.TipoLancamento ASC");
							$Qlan = sqlsrv_query($conexao1,"SELECT p.Carteira FROM Processo AS p WITH (NOLOCK) GROUP BY p.Carteira ORDER BY p.Carteira");
							while($Wlan = sqlsrv_fetch_array($Qlan, SQLSRV_FETCH_ASSOC)){
								?>
								<option value="<?php echo $Wlan['Carteira']; ?>"> <?php echo $Wlan['Carteira']; ?></option>
								<?php 
							}
							?>
						</select>
						<button id="bt1_1" class="bts" onclick="inserir_cli($('#dados_name_1').html(),1);" >+</button>
						</div>
						<div id="dados_1"></div>
					</td>
				</tr>
			</table>
			<input type="hidden" class="cls_cliente" name="banco_id" 	id="banco_id" 	value=""  />
			<input type="hidden" class="cls_cliente" name="cartei_num" 	id="cartei_num" value="1" />
		</div>
	</fieldset>
</div>
<br>