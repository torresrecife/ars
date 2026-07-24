<div class="admin-page admin-page--flat admin-module-offset">
<div class="admin-page__toolbar admin-page__toolbar--between">
	<form method="get" action="{{ route('clientes') }}" class="admin-form-inline admin-search-form">
		<input type="text" name="q" value="{{ e($search ?? '') }}" class="admin-form-input admin-search-input" placeholder="{{ __('Search clients...') }}" />
		<button type="submit" class="admin-button admin-button--primary">{{ __('Search') }}</button>
		@if (!empty($search))
			<a href="{{ route('clientes') }}" class="admin-button admin-button--secondary">{{ __('Clear') }}</a>
		@endif
	</form>
</div>
<div class="admin-surface admin-surface--table">
<table class="adminlist adminlist--full adminlist--modern">
	<tr height="30">
		<td class="order"><b>{{ __('Code') }}</b></td>
		<td class="order"><b>{{ __('Name') }}</b></td>
		<td class="order"><b>{{ __('Code Name') }}</b></td>
		<td class="order"><b>{{ __('Wallet(s)') }}</b></td>
		<td class="order"><b>{{ __('Date') }}</b></td>
		<td class="order"><b>{{ __('Area') }}</b></td>
		<td class="order"><b>{{ __('Status') }}</b></td>
		<td class="order"><b>{{ __('Options') }}</b></td>
	</tr>
@foreach ($clients as $client)
	<tr>
		<td class="order">{{ $client['banco_id'] }}</td>
		<td class="order">{{ e($client['banco_name']) }}</td>
		<td class="order">{{ e($client['banco_cod']) }}</td>
		<td class="order">{!! $client['dados_html'] !!}</td>
		<td class="order">{{ $client['datacad'] }}</td>
		<td class="order">{{ e($client['area_nome']) }}</td>
		<td class="order">{{ isset($statusLabels[$client['banco_status']]) ? $statusLabels[$client['banco_status']] : $client['banco_status'] }}</td>
						<td class="order">
							<div class="admin-table-actions">
								<a href="{{ route('clientes.edit', (int) $client['banco_id']) }}" class="admin-link-button">{{ __('Edit') }}</a>
								<a href="{{ route('clientes.confirm-delete', (int) $client['banco_id']) }}" class="admin-link-button admin-link-button--danger">{{ __('Delete') }}</a>
							</div>
						</td>
	</tr>
@endforeach
</table>
</div>
@if (method_exists($clients, 'hasPages') && $clients->hasPages())
	<div class="admin-pagination">
		<div class="admin-pagination__summary">
			{{ __('Showing :from to :to of :total items', ['from' => $clients->firstItem(), 'to' => $clients->lastItem(), 'total' => $clients->total()]) }}
		</div>
		<div class="admin-pagination__links">
			@if ($clients->onFirstPage())
				<span class="admin-pagination__link is-disabled">{{ __('Previous') }}</span>
			@else
				<a href="{{ $clients->appends(request()->except('page'))->previousPageUrl() }}" class="admin-pagination__link">{{ __('Previous') }}</a>
			@endif
			@foreach ($clients->appends(request()->except('page'))->getUrlRange(max(1, $clients->currentPage() - 2), min($clients->lastPage(), $clients->currentPage() + 2)) as $page => $url)
				@if ($page === $clients->currentPage())
					<span class="admin-pagination__link is-active">{{ $page }}</span>
				@else
					<a href="{{ $url }}" class="admin-pagination__link">{{ $page }}</a>
				@endif
			@endforeach
			@if ($clients->hasMorePages())
				<a href="{{ $clients->appends(request()->except('page'))->nextPageUrl() }}" class="admin-pagination__link">{{ __('Next') }}</a>
			@else
				<span class="admin-pagination__link is-disabled">{{ __('Next') }}</span>
			@endif
		</div>
	</div>
@endif
</div>
