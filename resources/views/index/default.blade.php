@php
	$homeIcons = [
		'sector' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h7v7H4z"/><path d="M13 4h7v7h-7z"/><path d="M4 13h7v7H4z"/><path d="M13 13h7v7h-7z"/></svg>',
	];
@endphp

<div class="***REMOVED***-page ***REMOVED***-page--flat ***REMOVED***-dashboard-page">
	<div class="***REMOVED***-page__toolbar ***REMOVED***-page__toolbar--between">
		<div>
			<div class="***REMOVED***-page__eyebrow">{{ __('Workspace') }}</div>
			<div class="***REMOVED***-dashboard-copy">{{ __('Choose a sector to open the wallets and results workspace.') }}</div>
		</div>
	</div>

	<div class="***REMOVED***-dashboard-grid">
		@foreach ($areas as $area)
			<a href="#" onclick="AbrirCarteiras('{{ (int) $area['area_id'] }}'); return false;" class="***REMOVED***-dashboard-card">
				<span class="***REMOVED***-dashboard-card__icon">{!! $homeIcons['sector'] !!}</span>
				<span class="***REMOVED***-dashboard-card__title">{{ e($area['area_nome']) }}</span>
				<span class="***REMOVED***-dashboard-card__description">{{ __('Open the wallet list and navigate to the panel for this sector.') }}</span>
			</a>
		@endforeach
	</div>
</div>
