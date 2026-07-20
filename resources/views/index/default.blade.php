<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
			<label><h2>Setores</h2></label>
			@foreach ($areas as $area)
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" onclick="AbrirCarteiras('{{ $area['area_id'] }}'); return false;" class="clspet" grupo="0">
							<img src="css/images/header/icon-48-section.png" alt="" />
							<span class="icon-label"> &nbsp; {{ e($area['area_nome']) }} &nbsp; </span>
						</a>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</div>
