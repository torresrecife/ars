<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
			<label><h2>Administrar Metas</h2></label>
			<label for="startBanco">Banco:</label>
			<select name="startBanco" id="startBanco" class="input-default nav-select">
				<option></option>
				@foreach ($banks as $bank)
					<option value="{{ $bank['banco_id'] }}">{{ e($bank['banco_name'] . ' (' . $bank['banco_class'] . ')') }}</option>
				@endforeach
			</select>
			<label for="startDate">M&ecirc;s / Ano:</label>
			<input type="text" name="startDate" id="startDate" class="date-picker date-picker--legacy" readonly="readonly" value="{{ e($monthYearLabel) }}"/>
			<span id="obg_date"></span>
			<input type="hidden" name="mes" id="mes" value="{{ e((string) $month) }}"/>
			<input type="hidden" name="ano" id="ano" value="{{ e((string) $year) }}"/>
			<br><br><br><br><br>
			<div class="icon-wrapper">
				<div class="icon">
					<a href="#" id="frm" onclick="AbrirMetasSelecao(); return false;">
						<img src="css/images/header/icon-48-themes.png" alt="" /><span>Metas</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
