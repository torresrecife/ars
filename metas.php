<div style="margin-top:80px">
<?php 

//$banco = $_POST['hid_flag'];
$startDate = isset($_POST['startDate'])?$_POST['startDate']:date('M');
$startBanco = isset($_POST['startBanco'])?$_POST['startBanco']:(isset($_POST['banco_id'])?$_POST['banco_id']:'');
$mes = isset($_POST['mes'])?$_POST['mes']:(isset($_POST['meta_mes'])?$_POST['meta_mes']:date('m'));
$ano = isset($_POST['ano'])?$_POST['ano']:(isset($_POST['meta_ano'])?$_POST['meta_ano']:date('Y'));
$Sb = "select b.banco_id,b.banco_cod from bancos as b where b.banco_id='" . $startBanco . "' ";
$Qb = mysqli_query($conexao4,$Sb);
$Wb = mysqli_fetch_array($Qb);
echo "<br><div style='font-family:arial;margin-left:40px;font-size:10pt;'>Cliente: <b>" . $Wb['banco_cod'] . "</b> | Mês / Ano: <b>$startDate</b></div><br>";

?>
<label><h2><u>Metas</u></h2></label>
<div>
<table class="adminlist" style="width:60%">
	<tr height="30">
		<td class="order" ><b>Cliente	</b></td>
		<td class="order" ><b>Andamento </b></td>
		<td class="order" ><b>Tipo 		</b></td>
		<td class="order" ><b>Qtd/Valor	</b></td>
		<td class="order" ><b>Opções    </b></td>
	</tr>
<?php

$Mtot=0;
$lin=0;
$Mtipo = array(1=>"Produção",2=>"Financeira");
$query = mysqli_query($conexao4,"SELECT * FROM metas_andamentos AS m JOIN andamentos AS a ON a.anda_id=m.anda_id WHERE m.banco_id='".$startBanco."' AND m.meta_mes='$mes' AND m.meta_ano='$ano' GROUP BY m.meta_id ORDER BY a.especie asc " ) or die(mysqli_error());
while ($arr = mysqli_fetch_array($query)){
	$lin++;
	?>
	<tr >
		<?php 
			$Qcli = mysqli_query($conexao4,"SELECT * from bancos as b where b.banco_id=".$arr['banco_id']." ORDER by b.banco_id asc " ) or die(mysqli_error());
			$Wcli = mysqli_fetch_array($Qcli);
			if($arr['especie']==2){
				$Mtot += $arr['meta_valor'];
				$Mval  = number_format($arr['meta_valor'],2,',','.');
			}else{
				$Mval  = number_format($arr['meta_valor'],0,"","");
			}
		?>
		<td class="order"><?php echo $Wcli['banco_name'];  ?></td>
		<td class="order"><?php echo $arr['nome'];			?></td>
		<td class="order" style="color:#ffffff;background:<?php echo ($arr['especie']==1?"#1C86EE":"#FFB90F"); ?>"><?php echo $Mtipo[$arr['especie']]; ?></td>
		<td class="order"><?php echo $Mval;	?></td>
		<td class="order" style="width:130px"><?php echo fc_botoes_metas($arr['meta_id'],"block",$arr['nome']); ?></td>
	</tr>
	<?php
}
?>
</table>
<script>
	function my_especie(valor){
		var espe = $("#meta_name_1 option:selected").attr("especie");
		if(espe==2){
			$("#meta_valor_"+valor).setMask("decimal");
			$(".sem_"+valor).setMask("decimal");
		}else{
			$("#meta_valor_"+valor).setMask("numbers");
			$(".sem_"+valor).setMask("numbers");
		}
	}
</script>
<style>
#content-box{
	height:<?php echo ($lin*30)+300; ?>px;
}
</style>
<?php 
	echo "<br><div style='font-family:arial;margin-left:40px;font-size:10pt;'>Total da meta financeira: <b>R$ " . number_format($Mtot,2,',','.') . "</b></div><br>";
?>
<div id="dialog-edit-metas" title="Editar Usuário" style="display:none; text-align:left;">
	<p class="validateMetas">Edite a Meta Abaixo</p>
	<fieldset>
		<div id="tb_dialog" style="min-height:70px; width:790px;">
			<table align="left" style="width:890px">
					<tr>
						<td>
							<div style="width:265px;float:left">Selecionar as metas</div>
							<div style="width:70px;float:left">Valor Total</div>
							<div style="width:90px;float:left">Def. manual |.</div>
							<div style="width:80px;float:left">Sem 1</div>
							<div style="width:80px;float:left">Sem 2</div>
							<div style="width:80px;float:left">Sem 3</div>
							<div style="width:80px;float:left">Sem 4</div>
							<div style="width:80px;float:left">Sem 5</div>
						</td>
					</tr>
				<tr>
					<td>
						<div id="metas_0">
							<div style="float:left">
								<select class="cls_metas2 input-default" name="meta_name_1" id="meta_name_1" obrigatorio="1" title="Setor" onchange="my_especie(1);" style="width:260px;height:22px;">
									<?php 
									$andam = mysqli_query($conexao4,"SELECT * FROM andamentos AS a ORDER BY a.especie asc, a.nome asc " ) or die(mysqli_error());
									echo "<option value=''></option>"; 
									while ($Wandam = mysqli_fetch_array($andam)){
										echo "<option value='".$Wandam['anda_id']."' especie='".$Wandam['especie']."' >".$Wandam['nome']." (".$Mtipo[$Wandam['especie']].")</option>";
									}
									?>
								</select>
								<input type="text" class="cls_meta" name="meta_valor_1" id="meta_valor_1" value="" obrigatorio="1" title="Banco de dados" style="width:120px;" alt=""/>
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
					</td>
					<!--td>Qtd/Valor:<br></td-->
				</tr>
			</table>
		</div>
	</fieldset>
</div>
<input type="hidden" name="metas_num" id="metas_num" value="1" />
<input type="hidden" class="cls_meta" name="meta_id"  id="meta_id"  value="" />
<input type="hidden" class="cls_meta" name="banco_id" id="banco_id" value="<?php echo $startBanco; ?>" />
<input type="hidden" class="cls_meta" name="meta_mes" id="meta_mes" value="<?php echo $mes; ?>" />
<input type="hidden" class="cls_meta" name="meta_ano" id="meta_ano" value="<?php echo $ano; ?>" />