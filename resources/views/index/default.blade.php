@php
	$homeIcons = [
		'sector' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h7v7H4z"/><path d="M13 4h7v7h-7z"/><path d="M4 13h7v7H4z"/><path d="M13 13h7v7h-7z"/></svg>',
	];
@endphp

<div class="admin-page admin-page--flat admin-dashboard-page">
	<div class="admin-page__toolbar admin-page__toolbar--between">
		<div>
			<div class="admin-page__eyebrow">{{ __('Workspace') }}</div>
			<div class="admin-dashboard-copy">{{ __('Choose a sector to open the wallets and results workspace.') }}</div>
		</div>
	</div>

	<div class="admin-dashboard-grid">
		@foreach ($areas as $area)
			<a href="#" onclick="AbrirCarteiras('{{ (int) $area['area_id'] }}'); return false;" class="admin-dashboard-card">
				<span class="admin-dashboard-card__icon">{!! $homeIcons['sector'] !!}</span>
				<span class="admin-dashboard-card__title">{{ e($area['area_nome']) }}</span>
				<span class="admin-dashboard-card__description">{{ __('Open the wallet list and navigate to the panel for this sector.') }}</span>
			</a>
		@endforeach
	</div>
</div>
