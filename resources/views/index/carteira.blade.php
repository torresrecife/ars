<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
			<label><h2>Carteira</h2></label>
			<label for="startDate">M&ecirc;s / Ano:</label>
			<input type="text" name="startDate" id="startDate" class="date-picker" readonly="readonly" value="{{ e($monthYearLabel) }}"/>
			<span id="obg_date"></span>
			<input type="hidden" name="mes" id="mes" value="{{ date('m') }}"/>
			<input type="hidden" name="ano" id="ano" value="{{ date('Y') }}"/>
			@if (!empty($showRegionSelector))
				<br><br>
				<label for="regiao_id">Regi&atilde;o:</label>
				<select name="regiao_id" id="regiao_id" class="input-default" style="height:20px;width:220px;">
					<option value="0">Todas as Regi&otilde;es</option>
					@foreach ($regions as $region)
						<option value="{{ (int) $region['regiao_id'] }}"{{ ((int) $selectedRegionId === (int) $region['regiao_id']) ? ' selected="selected"' : '' }}>{{ e($region['regiao_nome']) }}</option>
					@endforeach
				</select>
			@else
				<input type="hidden" name="regiao_id" id="regiao_id" value="{{ (int) $selectedRegionId }}"/>
			@endif
			<br><br><br>
			@foreach ($banks as $bank)
				<div class="icon-wrapper" style="height:100%">
					<div class="icon">
						<a href="#" onclick="AbrirPainel('{{ e((string) $hidArea) }}','{{ $bank['banco_id'] }}'); return false;" class="clspet" grupo="0">
							<img src="css/images/header/icon-48-module.png" alt="" />
							<span style="position:relative;margin-top:0px"> &nbsp; {{ e($bank['banco_name'] . ($bank['banco_class'] ? ' (' . $bank['banco_class'] . ')' : '')) }} &nbsp; </span>
						</a>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</div>
