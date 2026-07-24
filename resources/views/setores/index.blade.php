<div class="admin-page admin-page--flat">
	@if (session('status'))
		<div class="admin-flash admin-flash--success">{{ session('status') }}</div>
	@endif

	@if (session('error'))
		<div class="admin-flash admin-flash--error">{{ session('error') }}</div>
	@endif

	<div class="admin-page__toolbar admin-page__toolbar--between">
		<form method="get" action="{{ route('setores') }}" class="admin-form-inline admin-search-form">
			<input type="text" name="q" value="{{ e($search ?? '') }}" class="admin-form-input admin-search-input" placeholder="{{ __('Search sectors...') }}" />
			<button type="submit" class="admin-button admin-button--primary">{{ __('Search') }}</button>
			@if (!empty($search))
				<a href="{{ route('setores') }}" class="admin-button admin-button--secondary">{{ __('Clear') }}</a>
			@endif
		</form>
	</div>

	<div class="admin-surface admin-surface--table">
		<table class="adminlist adminlist--full adminlist--modern">
			<colgroup>
				<col class="admin-col admin-col--code" />
				<col class="admin-col admin-col--name" />
				<col class="admin-col admin-col--datetime" />
				<col class="admin-col admin-col--actions" />
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
							<div class="admin-table-actions">
								<a href="{{ route('setores.edit', (int) $area['area_id']) }}" class="admin-link-button">{{ __('Edit') }}</a>
								<a href="{{ route('setores.confirm-delete', (int) $area['area_id']) }}" class="admin-link-button admin-link-button--danger">{{ __('Delete') }}</a>
							</div>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	@if (method_exists($areas, 'hasPages') && $areas->hasPages())
		<div class="admin-pagination">
			<div class="admin-pagination__summary">
				{{ __('Showing :from to :to of :total items', ['from' => $areas->firstItem(), 'to' => $areas->lastItem(), 'total' => $areas->total()]) }}
			</div>
			<div class="admin-pagination__links">
				@if ($areas->onFirstPage())
					<span class="admin-pagination__link is-disabled">{{ __('Previous') }}</span>
				@else
					<a href="{{ $areas->appends(request()->except('page'))->previousPageUrl() }}" class="admin-pagination__link">{{ __('Previous') }}</a>
				@endif
				@foreach ($areas->appends(request()->except('page'))->getUrlRange(max(1, $areas->currentPage() - 2), min($areas->lastPage(), $areas->currentPage() + 2)) as $page => $url)
					@if ($page === $areas->currentPage())
						<span class="admin-pagination__link is-active">{{ $page }}</span>
					@else
						<a href="{{ $url }}" class="admin-pagination__link">{{ $page }}</a>
					@endif
				@endforeach
				@if ($areas->hasMorePages())
					<a href="{{ $areas->appends(request()->except('page'))->nextPageUrl() }}" class="admin-pagination__link">{{ __('Next') }}</a>
				@else
					<span class="admin-pagination__link is-disabled">{{ __('Next') }}</span>
				@endif
			</div>
		</div>
	@endif
</div>
