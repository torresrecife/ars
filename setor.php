<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
		<label><h2><u>Áreas</u></h2></label>
			<div class="icon-wrapper">
				<table class="adminlist" width="60%" align="center">
					<tr height="30">
						<td class="order" ><b>Código		 </b></td>
						<td class="order" ><b>Nome			 </b></td>
						<td class="order" ><b>Data Cadastro	 </b></td>
						<td class="order" ><b>Opções         </b></td>
					</tr>
					<?php
					$query = mysqli_query($conexao4,"SELECT * from area as s ORDER by s.area_id") or die(mysqli_error());
					while ($arr = mysqli_fetch_array($query))
					{
						?>
						<tr >
							<td class="order"><?PHP echo $arr['area_id'];	 ?></td>
							<td class="order"><?php echo $arr['area_nome']; ?></td>
							<td class="order"><?php echo $arr['area_date']; 	 ?></td>
							<td class="order" style="width:130px"><?php echo fc_botoes_setor($arr['area_id'],"block",$arr['area_nome']); ?></td>
						</tr>
						<?php
					}
				?>
				</table>
			</div>
		</div>
	</div>
</div>
<div id="dialog-edit-setor" title="Editar Setor" style="display:none; text-align:left;">
	<p class="validateTips">Edite a Área Abaixo</p>
	<fieldset>
		<div>
			<table>
				<tr>
					<td><label>Nome do Área</label></td>
					<td><input type="text" class="cls_setor" name="nome_setor" id="nome_setor" value="" obrigatorio="1" title="Nome do Setor"/></td>
				</tr>
			</table>
			<input type="hidden" class="cls_setor" name="area_id" id="area_id" value="" />
		</div>
	</fieldset>
</div>