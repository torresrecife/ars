@php
	$walletIcons = [
		'bank' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 10h16"/><path d="M6 10V6h12v4"/><path d="M6 10v8"/><path d="M10 10v8"/><path d="M14 10v8"/><path d="M18 10v8"/><path d="M3 18h18"/></svg>',
	];
@endphp

<div class="admin-page admin-page--flat wallet-page">
	<div class="admin-page__toolbar admin-page__toolbar--between">
		<div>
			<div class="admin-page__eyebrow">{{ __('Wallet(s)') }}</div>
			<div class="admin-dashboard-copy">{{ __('Select the reference period and open the client panel.') }}</div>
		</div>
	</div>

	<div class="admin-surface admin-surface--form wallet-filter-surface">
		<div class="admin-form-grid wallet-filter-grid">
			<div class="admin-form-group">
				<label for="startDate">{{ __('Month/Year') }}</label>
				<input type="text" name="startDate" id="startDate" class="admin-form-input date-picker" readonly="readonly" value="{{ e($monthYearLabel) }}"/>
				<span id="obg_date" class="admin-form-hint"></span>
				<input type="hidden" name="mes" id="mes" value="{{ (int) $month }}"/>
				<input type="hidden" name="ano" id="ano" value="{{ (int) $year }}"/>
			</div>
			@if (!empty($showRegionSelector))
				<div class="admin-form-group">
					<label for="regiao_id">{{ __('Region') }}</label>
					<select name="regiao_id" id="regiao_id" class="admin-form-input admin-form-select">
						<option value="0">{{ __('All regions') }}</option>
						@foreach ($regions as $region)
							<option value="{{ (int) $region['regiao_id'] }}"{{ ((int) $selectedRegionId === (int) $region['regiao_id']) ? ' selected="selected"' : '' }}>{{ e($region['regiao_nome']) }}</option>
						@endforeach
					</select>
				</div>
			@else
				<input type="hidden" name="regiao_id" id="regiao_id" value="{{ (int) $selectedRegionId }}"/>
			@endif
		</div>
	</div>

	<div class="admin-dashboard-grid wallet-grid">
		@foreach ($banks as $bank)
			<a href="#" onclick="AbrirPainel('{{ e((string) $hidArea) }}','{{ $bank['banco_id'] }}'); return false;" class="admin-dashboard-card wallet-card">
				<span class="admin-dashboard-card__icon">{!! $walletIcons['bank'] !!}</span>
				<span class="admin-dashboard-card__title">{{ e($bank['banco_name']) }}</span>
				<span class="admin-dashboard-card__description">{{ e($bank['banco_class'] ? $bank['banco_class'] : __('Open panel')) }}</span>
			</a>
		@endforeach
	</div>
</div>
