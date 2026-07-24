<div class="***REMOVED***-page ***REMOVED***-page--flat ***REMOVED***-module-offset">
<div class="***REMOVED***-page__toolbar ***REMOVED***-page__toolbar--between">
	<form method="get" action="{{ route('regioes') }}" class="***REMOVED***-form-inline ***REMOVED***-search-form">
		<input type="text" name="q" value="{{ e($search ?? '') }}" class="***REMOVED***-form-input ***REMOVED***-search-input" placeholder="{{ __('Search regions...') }}" />
		<button type="submit" class="***REMOVED***-button ***REMOVED***-button--primary">{{ __('Search') }}</button>
		@if (!empty($search))
			<a href="{{ route('regioes') }}" class="***REMOVED***-button ***REMOVED***-button--secondary">{{ __('Clear') }}</a>
		@endif
	</form>
</div>
<div class="***REMOVED***-surface ***REMOVED***-surface--table">
<table class="***REMOVED***list ***REMOVED***list--full ***REMOVED***list--modern">
	<tr height="30">
		<td class="order"><b>{{ __('Code') }}</b></td>
		<td class="order"><b>{{ __('Name') }}</b></td>
		<td class="order"><b>Slug</b></td>
		<td class="order"><b>UFs</b></td>
		<td class="order"><b>{{ __('Users') }}</b></td>
		<td class="order"><b>{{ __('Status') }}</b></td>
		<td class="order"><b>{{ __('Options') }}</b></td>
	</tr>
@foreach ($regions as $region)
	<tr>
		<td class="order">{{ (int) $region['regiao_id'] }}</td>
		<td class="order">{{ e($region['regiao_nome']) }}</td>
		<td class="order">{{ e($region['regiao_slug']) }}</td>
		<td class="order">{{ e($region['ufs']) }}</td>
		<td class="order">{{ (int) $region['total_usuarios'] }}</td>
		<td class="order">{{ ((string) $region['regiao_status'] === 'Y') ? __('Active') : __('Inactive') }}</td>
						<td class="order">
							<div class="***REMOVED***-table-actions">
								<a href="{{ route('regioes.edit', (int) $region['regiao_id']) }}" class="***REMOVED***-link-button">{{ __('Edit') }}</a>
								<a href="{{ route('regioes.confirm-delete', (int) $region['regiao_id']) }}" class="***REMOVED***-link-button ***REMOVED***-link-button--danger">{{ __('Delete') }}</a>
							</div>
						</td>
	</tr>
@endforeach
</table>
</div>
@if (method_exists($regions, 'hasPages') && $regions->hasPages())
	<div class="***REMOVED***-pagination">
		<div class="***REMOVED***-pagination__summary">
			{{ __('Showing :from to :to of :total items', ['from' => $regions->firstItem(), 'to' => $regions->lastItem(), 'total' => $regions->total()]) }}
		</div>
		<div class="***REMOVED***-pagination__links">
			@if ($regions->onFirstPage())
				<span class="***REMOVED***-pagination__link is-disabled">{{ __('Previous') }}</span>
			@else
				<a href="{{ $regions->appends(request()->except('page'))->previousPageUrl() }}" class="***REMOVED***-pagination__link">{{ __('Previous') }}</a>
			@endif
			@foreach ($regions->appends(request()->except('page'))->getUrlRange(max(1, $regions->currentPage() - 2), min($regions->lastPage(), $regions->currentPage() + 2)) as $page => $url)
				@if ($page === $regions->currentPage())
					<span class="***REMOVED***-pagination__link is-active">{{ $page }}</span>
				@else
					<a href="{{ $url }}" class="***REMOVED***-pagination__link">{{ $page }}</a>
				@endif
			@endforeach
			@if ($regions->hasMorePages())
				<a href="{{ $regions->appends(request()->except('page'))->nextPageUrl() }}" class="***REMOVED***-pagination__link">{{ __('Next') }}</a>
			@else
				<span class="***REMOVED***-pagination__link is-disabled">{{ __('Next') }}</span>
			@endif
		</div>
	</div>
@endif
</div>
