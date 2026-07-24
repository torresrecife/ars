@php
	$adminLinks = [];

	if ($userLevel === 'ADM') {
		$adminLinks = [
			[
				'label' => __('Users'),
				'url' => url('usuarios'),
				'description' => __('Manage access, profiles, and permissions.'),
				'icon' => 'users',
			],
			[
				'label' => __('Sectors'),
				'url' => url('setores'),
				'description' => __('Organize the internal work areas.'),
				'icon' => 'grid',
			],
			[
				'label' => __('Clients'),
				'url' => url('clientes'),
				'description' => __('Maintain banks, wallets, and client context.'),
				'icon' => 'briefcase',
			],
			[
				'label' => __('Progress'),
				'url' => url('andamentos'),
				'description' => __('Configure production and financial progress items.'),
				'icon' => 'list',
			],
			[
				'label' => __('Regions'),
				'url' => url('regioes'),
				'description' => __('Manage regions and linked states.'),
				'icon' => 'layers',
			],
			[
				'label' => __('Weeks'),
				'url' => url('semanas'),
				'description' => __('Define the weekly ranges for each month.'),
				'icon' => 'calendar',
			],
			[
				'label' => __('Goals'),
				'url' => url('metas'),
				'description' => __('Set operational and financial goals by context.'),
				'icon' => 'target',
			],
			[
				'label' => __('Production'),
				'url' => url('producao'),
				'description' => __('Open the production and reporting workspace.'),
				'icon' => 'bar-chart',
			],
		];
	} elseif ($userLevel === 'GER') {
		$adminLinks = [
			[
				'label' => __('Goals'),
				'url' => url('metas'),
				'description' => __('Set operational and financial goals by context.'),
				'icon' => 'target',
			],
			[
				'label' => __('Production'),
				'url' => url('producao'),
				'description' => __('Open the production and reporting workspace.'),
				'icon' => 'bar-chart',
			],
		];
	}

	$adminIcons = [
		'users' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="3.5"/><path d="M17 11a3 3 0 1 0 0-6"/><path d="M21 21v-2a4 4 0 0 0-3-3.87"/></svg>',
		'grid' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h7v7H4z"/><path d="M13 4h7v7h-7z"/><path d="M4 13h7v7H4z"/><path d="M13 13h7v7h-7z"/></svg>',
		'briefcase' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16v12H4z"/><path d="M8 7V5h8v2"/><path d="M4 12h16"/></svg>',
		'list' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 6h12"/><path d="M8 12h12"/><path d="M8 18h12"/><path d="M4 6h.01"/><path d="M4 12h.01"/><path d="M4 18h.01"/></svg>',
		'layers' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 9 4.5-9 4.5-9-4.5z"/><path d="m3 12 9 4.5 9-4.5"/><path d="m3 16.5 9 4.5 9-4.5"/></svg>',
		'calendar' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3v4"/><path d="M17 3v4"/><path d="M4 9h16"/><rect x="4" y="5" width="16" height="16" rx="2"/></svg>',
		'target' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3a9 9 0 1 0 9 9"/><path d="M21 3 12 12"/><path d="M16 3h5v5"/></svg>',
		'bar-chart' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 13h4v7H4z"/><path d="M10 4h4v16h-4z"/><path d="M16 9h4v11h-4z"/></svg>',
	];
@endphp

<div class="admin-page admin-page--flat admin-dashboard-page">
	<div class="admin-page__toolbar admin-page__toolbar--between">
		<div>
			<div class="admin-page__eyebrow">{{ __('Administration') }}</div>
			<div class="admin-dashboard-copy">{{ __('Choose a module to manage the administrative data of the system.') }}</div>
		</div>
	</div>

	<div class="admin-dashboard-grid">
		@foreach ($adminLinks as $link)
			<a href="{{ $link['url'] }}" class="admin-dashboard-card">
				<span class="admin-dashboard-card__icon">{!! $adminIcons[$link['icon']] !!}</span>
				<span class="admin-dashboard-card__title">{{ $link['label'] }}</span>
				<span class="admin-dashboard-card__description">{{ $link['description'] }}</span>
			</a>
		@endforeach
	</div>
</div>
