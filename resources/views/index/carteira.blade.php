<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
			<label><h2>{{ __('Wallet') }}</h2></label>
			<label for="startDate">{{ __('Month/Year') }}:</label>
			<input type="text" name="startDate" id="startDate" class="date-picker" readonly="readonly" value="{{ e($monthYearLabel) }}"/>
			<span id="obg_date"></span>
			<input type="hidden" name="mes" id="mes" value="{{ (int) $month }}"/>
			<input type="hidden" name="ano" id="ano" value="{{ (int) $year }}"/>
			@if (!empty($showRegionSelector))
				<br><br>
				<label for="regiao_id">{{ __('Region') }}:</label>
				<select name="regiao_id" id="regiao_id" class="input-default nav-select nav-select--region">
					<option value="0">{{ __('All regions') }}</option>
					@foreach ($regions as $region)
						<option value="{{ (int) $region['regiao_id'] }}"{{ ((int) $selectedRegionId === (int) $region['regiao_id']) ? ' selected="selected"' : '' }}>{{ e($region['regiao_nome']) }}</option>
					@endforeach
				</select>
			@else
				<input type="hidden" name="regiao_id" id="regiao_id" value="{{ (int) $selectedRegionId }}"/>
			@endif
			<br><br><br>
			@foreach ($banks as $bank)
				<div class="icon-wrapper icon-wrapper--full-height">
					<div class="icon">
						<a href="#" onclick="AbrirPainel('{{ e((string) $hidArea) }}','{{ $bank['banco_id'] }}'); return false;" class="clspet" grupo="0">
							<img src="css/images/header/icon-48-module.png" alt="" />
							<span class="icon-label"> &nbsp; {{ e($bank['banco_name'] . ($bank['banco_class'] ? ' (' . $bank['banco_class'] . ')' : '')) }} &nbsp; </span>
						</a>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</div>
