<div class="***REMOVED***-page ***REMOVED***-page--flat">
	@if (session('status'))
		<div class="***REMOVED***-flash ***REMOVED***-flash--success">{{ session('status') }}</div>
	@endif

	@if (session('error'))
		<div class="***REMOVED***-flash ***REMOVED***-flash--error">{{ session('error') }}</div>
	@endif

	<div class="***REMOVED***-page__toolbar ***REMOVED***-page__toolbar--between">
		<form method="get" action="{{ route('setores') }}" class="***REMOVED***-form-inline ***REMOVED***-search-form">
			<input type="text" name="q" value="{{ e($search ?? '') }}" class="***REMOVED***-form-input ***REMOVED***-search-input" placeholder="{{ __('Search sectors...') }}" />
			<button type="submit" class="***REMOVED***-button ***REMOVED***-button--primary">{{ __('Search') }}</button>
			@if (!empty($search))
				<a href="{{ route('setores') }}" class="***REMOVED***-button ***REMOVED***-button--secondary">{{ __('Clear') }}</a>
			@endif
		</form>
	</div>

	<div class="***REMOVED***-surface ***REMOVED***-surface--table">
		<table class="***REMOVED***list ***REMOVED***list--full ***REMOVED***list--modern">
			<colgroup>
				<col class="***REMOVED***-col ***REMOVED***-col--code" />
				<col class="***REMOVED***-col ***REMOVED***-col--name" />
				<col class="***REMOVED***-col ***REMOVED***-col--datetime" />
				<col class="***REMOVED***-col ***REMOVED***-col--actions" />
			</colgroup>
			<thead>
				<tr>
					<th class="order">{{ __('Code') }}</th>
					<th class="order">{{ __('Name') }}</th>
					<th class="order">{{ __('Created At') }}</th>
					<th class="order">{{ __('Options') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($areas as $area)
					<tr>
						<td class="order">{{ (int) $area['area_id'] }}</td>
						<td class="order">{{ e($area['area_nome']) }}</td>
						<td class="order">{{ e($area['area_date']) }}</td>
						<td class="order">
							<div class="***REMOVED***-table-actions">
								<a href="{{ route('setores.edit', (int) $area['area_id']) }}" class="***REMOVED***-link-button">{{ __('Edit') }}</a>
								<form method="post" action="{{ route('setores.destroy.page', (int) $area['area_id']) }}" onsubmit="return confirm({{ json_encode(__('Do you really want to delete the sector :name?', ['name' => $area['area_nome']]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }});">
									@csrf
									@method('DELETE')
									<button type="submit" class="***REMOVED***-link-button ***REMOVED***-link-button--danger">{{ __('Delete') }}</button>
								</form>
							</div>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	@if (method_exists($areas, 'hasPages') && $areas->hasPages())
		<div class="***REMOVED***-pagination">
			<div class="***REMOVED***-pagination__summary">
				{{ __('Showing :from to :to of :total items', ['from' => $areas->firstItem(), 'to' => $areas->lastItem(), 'total' => $areas->total()]) }}
			</div>
			<div class="***REMOVED***-pagination__links">
				@if ($areas->onFirstPage())
					<span class="***REMOVED***-pagination__link is-disabled">{{ __('Previous') }}</span>
				@else
					<a href="{{ $areas->appends(request()->except('page'))->previousPageUrl() }}" class="***REMOVED***-pagination__link">{{ __('Previous') }}</a>
				@endif
				@foreach ($areas->appends(request()->except('page'))->getUrlRange(max(1, $areas->currentPage() - 2), min($areas->lastPage(), $areas->currentPage() + 2)) as $page => $url)
					@if ($page === $areas->currentPage())
						<span class="***REMOVED***-pagination__link is-active">{{ $page }}</span>
					@else
						<a href="{{ $url }}" class="***REMOVED***-pagination__link">{{ $page }}</a>
					@endif
				@endforeach
				@if ($areas->hasMorePages())
					<a href="{{ $areas->appends(request()->except('page'))->nextPageUrl() }}" class="***REMOVED***-pagination__link">{{ __('Next') }}</a>
				@else
					<span class="***REMOVED***-pagination__link is-disabled">{{ __('Next') }}</span>
				@endif
			</div>
		</div>
	@endif
</div>
