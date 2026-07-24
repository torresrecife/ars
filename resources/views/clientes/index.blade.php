<div class="***REMOVED***-page ***REMOVED***-page--flat ***REMOVED***-module-offset">
<div class="***REMOVED***-page__toolbar ***REMOVED***-page__toolbar--between">
	<form method="get" action="{{ route('clientes') }}" class="***REMOVED***-form-inline ***REMOVED***-search-form">
		<input type="text" name="q" value="{{ e($search ?? '') }}" class="***REMOVED***-form-input ***REMOVED***-search-input" placeholder="{{ __('Search clients...') }}" />
		<button type="submit" class="***REMOVED***-button ***REMOVED***-button--primary">{{ __('Search') }}</button>
		@if (!empty($search))
			<a href="{{ route('clientes') }}" class="***REMOVED***-button ***REMOVED***-button--secondary">{{ __('Clear') }}</a>
		@endif
	</form>
</div>
<div class="***REMOVED***-surface ***REMOVED***-surface--table">
<table class="***REMOVED***list ***REMOVED***list--full ***REMOVED***list--modern">
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
			<div class="***REMOVED***-table-actions">
				<a href="{{ route('clientes.edit', (int) $client['banco_id']) }}" class="***REMOVED***-link-button">{{ __('Edit') }}</a>
				<form method="post" action="{{ route('clientes.destroy.page', (int) $client['banco_id']) }}" onsubmit="return confirm({{ json_encode(__('Do you really want to delete the client :name?', ['name' => $client['banco_name']]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }});">
					@csrf
					@method('DELETE')
					<button type="submit" class="***REMOVED***-link-button ***REMOVED***-link-button--danger">{{ __('Delete') }}</button>
				</form>
			</div>
		</td>
	</tr>
@endforeach
</table>
</div>
@if (method_exists($clients, 'hasPages') && $clients->hasPages())
	<div class="***REMOVED***-pagination">
		<div class="***REMOVED***-pagination__summary">
			{{ __('Showing :from to :to of :total items', ['from' => $clients->firstItem(), 'to' => $clients->lastItem(), 'total' => $clients->total()]) }}
		</div>
		<div class="***REMOVED***-pagination__links">
			@if ($clients->onFirstPage())
				<span class="***REMOVED***-pagination__link is-disabled">{{ __('Previous') }}</span>
			@else
				<a href="{{ $clients->appends(request()->except('page'))->previousPageUrl() }}" class="***REMOVED***-pagination__link">{{ __('Previous') }}</a>
			@endif
			@foreach ($clients->appends(request()->except('page'))->getUrlRange(max(1, $clients->currentPage() - 2), min($clients->lastPage(), $clients->currentPage() + 2)) as $page => $url)
				@if ($page === $clients->currentPage())
					<span class="***REMOVED***-pagination__link is-active">{{ $page }}</span>
				@else
					<a href="{{ $url }}" class="***REMOVED***-pagination__link">{{ $page }}</a>
				@endif
			@endforeach
			@if ($clients->hasMorePages())
				<a href="{{ $clients->appends(request()->except('page'))->nextPageUrl() }}" class="***REMOVED***-pagination__link">{{ __('Next') }}</a>
			@else
				<span class="***REMOVED***-pagination__link is-disabled">{{ __('Next') }}</span>
			@endif
		</div>
	</div>
@endif
</div>
