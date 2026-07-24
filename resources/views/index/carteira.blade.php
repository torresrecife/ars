@php
	$walletIcons = [
		'bank' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 10h16"/><path d="M6 10V6h12v4"/><path d="M6 10v8"/><path d="M10 10v8"/><path d="M14 10v8"/><path d="M18 10v8"/><path d="M3 18h18"/></svg>',
	];
@endphp

<div class="***REMOVED***-page ***REMOVED***-page--flat wallet-page">
	<div class="***REMOVED***-page__toolbar ***REMOVED***-page__toolbar--between">
		<div>
			<div class="***REMOVED***-page__eyebrow">{{ __('Wallet(s)') }}</div>
			<div class="***REMOVED***-dashboard-copy">{{ __('Select the reference period and open the client panel.') }}</div>
		</div>
	</div>

	<div class="***REMOVED***-surface ***REMOVED***-surface--form wallet-filter-surface">
		<div class="***REMOVED***-form-grid wallet-filter-grid">
			<div class="***REMOVED***-form-group">
				<label for="startDate">{{ __('Month/Year') }}</label>
				<input type="text" name="startDate" id="startDate" class="***REMOVED***-form-input date-picker" readonly="readonly" value="{{ e($monthYearLabel) }}"/>
				<span id="obg_date" class="***REMOVED***-form-hint"></span>
				<input type="hidden" name="mes" id="mes" value="{{ (int) $month }}"/>
				<input type="hidden" name="ano" id="ano" value="{{ (int) $year }}"/>
			</div>
			@if (!empty($showRegionSelector))
				<div class="***REMOVED***-form-group">
					<label for="regiao_id">{{ __('Region') }}</label>
					<select name="regiao_id" id="regiao_id" class="***REMOVED***-form-input ***REMOVED***-form-select">
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

	<div class="***REMOVED***-dashboard-grid wallet-grid">
		@foreach ($banks as $bank)
			<a href="#" onclick="AbrirPainel('{{ e((string) $hidArea) }}','{{ $bank['banco_id'] }}'); return false;" class="***REMOVED***-dashboard-card wallet-card">
				<span class="***REMOVED***-dashboard-card__icon">{!! $walletIcons['bank'] !!}</span>
				<span class="***REMOVED***-dashboard-card__title">{{ e($bank['banco_name']) }}</span>
				<span class="***REMOVED***-dashboard-card__description">{{ e($bank['banco_class'] ? $bank['banco_class'] : __('Open panel')) }}</span>
			</a>
		@endforeach
	</div>
</div>
