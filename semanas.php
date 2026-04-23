<div style="margin-top:80px" >
<label><h2><u>Semanas</u></h2></label>
<div>
<table class="adminlist">
	<tr height="30">
		<td class="order" ><b>Código		 </b></td>
		<td class="order" ><b>Mês			 </b></td>
		<td class="order" ><b>Ano			 </b></td>
		<td class="order" style="background:#436EEE;color:#ffffff"><b>1ª Semana		 </b></td>
		<td class="order" style="background:#436EEE;color:#ffffff"><b>2ª Semana		 </b></td>
		<td class="order" style="background:#436EEE;color:#ffffff"><b>3ª Semana		 </b></td>
		<td class="order" style="background:#436EEE;color:#ffffff"><b>4ª Semana		 </b></td>
		<td class="order" style="background:#1E90FF;color:#ffffff"><b>5ª Semana		 </b></td>
		<td class="order" ><b>Alterado em </b></td>
		<td class="order" ><b>Cadastrado em</b></td>
		<td class="order" >Configurações</td>
	</tr>
<?php
	
$query = mysqli_query($conexao4,"SELECT *, DATE_FORMAT( s.data_cad , '%d/%m/%Y %H:%i:%s' ) AS datacad, DATE_FORMAT( s.data_cad , '%d/%m/%Y %H:%i:%s' ) AS dataalt from semanas as s ORDER by s.semanas_id") or die(mysqli_error());
while ($arr = mysqli_fetch_array($query)){
	?>
	<tr >
		<td class="order"><?PHP echo $arr['semanas_id'];?></td>
		<td class="order"><?php echo $arrMonths[$arr['mes']]; ?></td>
		<td class="order"><?php echo $arr['ano']; ?></td>
		<td class="order" style="background:#436EEE;color:#ffffff"><?php echo $arr['ini_1'] . " à " . $arr['fim_1']; ?></td>
		<td class="order" style="background:#436EEE;color:#ffffff"><?php echo $arr['ini_2'] . " à " . $arr['fim_2']; ?></td>
		<td class="order" style="background:#436EEE;color:#ffffff"><?php echo $arr['ini_3'] . " à " . $arr['fim_3']; ?></td>
		<td class="order" style="background:#436EEE;color:#ffffff"><?php echo $arr['ini_4'] . " à " . $arr['fim_4']; ?></td>
		<td class="order" style="background:#1E90FF;color:#ffffff"><?php echo ($arr['ini_5']?$arr['ini_5'] . " à " . $arr['fim_5']:"-"); ?></td>
		<td class="order"><?php echo $arr['dataalt']; ?></td>
		<td class="order"><?php echo $arr['datacad']; ?></td>
		<td class="order" style="width:130px"><?php echo fc_botoes_sem($arr['semanas_id'],"block",$arr['mes']); ?></td>
	</tr>
	<?php
}
?>
</table>
<div id="dialog-edit-sem" title="Editar Semana" style="display:none;text-align:left;overflow-y: scroll;">
	<p class="validateTips">Edite a Semana Abaixo</p>
	<fieldset>
		<div>
			<table style="width:400px">
				<tr>
					<td><label>Código:</label></td>
					<td colspan="3"><input type="text" class="cls_sem" name="id_sem" id="id_sem" style="border:0;background:#fff;width:50px" title="Id da semana" readonly="readonly" /></td>
				</tr>
				<tr>
					<td style="width:50px"><label>Mês/Ano:</label></td>
					<td style="width:50px"><input type="text" class="cls_sem" name="mes_sem"  id="mes_sem" style="width:90%" title="Mês" alt="integer"/></td>
					<td align="center" style="width:50px">/</td>
					<td style="width:50px"><input type="text" class="cls_sem" name="ano_sem"  id="ano_sem" style="width:90%" title="Ano" alt="integer"/></td>
				<tr>
					<td><label>1ª Semana:</label></td>
					<td><input type="text" class="cls_sem" name="ini1_sem" id="ini1_sem" style="width:90%" title="1ª Semana início" alt="integer"/></td>
					<td align="center" style="width:50px">à</td>
					<td><input type="text" class="cls_sem" name="fim1_sem" id="fim1_sem" style="width:90%" title="1ª Semana fim"    alt="integer"/></td>
				</tr>
				<tr>
					<td><label>2ª Semana:</label></td>
					<td><input type="text" class="cls_sem" name="ini2_sem" id="ini2_sem" style="width:90%" title="2ª Semana início" alt="integer"/></td>
					<td align="center" style="width:50px">à</td>
					<td><input type="text" class="cls_sem" name="fim2_sem" id="fim2_sem" style="width:90%" title="3ª Semana fim"	alt="integer"/></td>
				</tr>
				<tr>
					<td><label>3ª Semana:</label></td>
					<td><input type="text" class="cls_sem" name="ini3_sem" id="ini3_sem" style="width:90%" title="3ª Semana início" alt="integer"/></td>
					<td align="center" style="width:50px">à</td>
					<td><input type="text" class="cls_sem" name="fim3_sem" id="fim3_sem" style="width:90%" title="3ª Semana fim"	alt="integer"/></td>
				</tr>
				<tr>
					<td><label>4ª Semana:</label></td>
					<td><input type="text" class="cls_sem" name="ini4_sem" id="ini4_sem" style="width:90%" title="4ª Semana início" alt="integer"/></td>
					<td align="center" style="width:50px">à</td>
					<td><input type="text" class="cls_sem" name="fim4_sem" id="fim4_sem" style="width:90%" title="4ª Semana fim"	alt="integer"/></td>
				</tr>
				<tr>
					<td><label>5ª Semana:</label></td>
					<td><input type="text" class="cls_sem" name="ini5_sem" id="ini5_sem" style="width:90%" title="5ª Semana início" alt="integer"/></td>
					<td align="center" style="width:50px">à</td>
					<td><input type="text" class="cls_sem" name="fim5_sem" id="fim5_sem" style="width:90%" title="6ª Semana fim"	alt="integer"/></td>
				</tr>
			</table>
		</div>
	</fieldset>
</div>