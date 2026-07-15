<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
			<label><h2>ProduÃ§Ã£o</h2></label>
			<label for="startSetor">Setor:</label>
			<select name="startSetor" id="startSetor" class="input-default" style="height:20px;width:200px;">
				<option value="">Todas os Setores</option>
				@foreach ($areas as $area)
					<option value="{{ $area['area_id'] }}">{{ e($area['area_nome']) }}</option>
				@endforeach
			</select>
			@if (!empty($showRegionSelector))
				<label for="regiao_id">&nbsp;Regi&atilde;o:</label>
				<select name="regiao_id" id="regiao_id" class="input-default" style="height:20px;width:200px;">
					<option value="0">Todas as Regi&otilde;es</option>
					@foreach ($regions as $region)
						<option value="{{ (int) $region['regiao_id'] }}"{{ ((int) $selectedRegionId === (int) $region['regiao_id']) ? ' selected="selected"' : '' }}>{{ e($region['regiao_nome']) }}</option>
					@endforeach
				</select>
			@else
				<input type="hidden" name="regiao_id" id="regiao_id" value="{{ (int) $selectedRegionId }}"/>
			@endif
			<label for="startDate">&nbsp;M&ecirc;s / Ano:</label>
			<input type="text" name="startDate" id="startDate" class="date-picker" readonly="readonly" value="{{ e($monthYearLabel) }}"/>
			<span id="obg_date"></span>
			<input type="hidden" name="mes" id="mes" value="{{ date('m') }}"/>
			<input type="hidden" name="ano" id="ano" value="{{ date('Y') }}"/>
			<br><br><br><br><br>
			<div class="icon-wrapper">
				<div class="icon">
					<a href="#" id="frm" onclick="EnviarPagina('relatorio.php',true,'',''); return false;">
						<img src="css/images/header/icon-48-themes.png" alt="" /><span>RelatÃ³rio</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
