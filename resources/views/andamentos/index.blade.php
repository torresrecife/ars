<div class="***REMOVED***-page ***REMOVED***-page--flat">
	@if (session('status'))
		<div class="***REMOVED***-flash ***REMOVED***-flash--success">{{ session('status') }}</div>
	@endif

	@if (session('error'))
		<div class="***REMOVED***-flash ***REMOVED***-flash--error">{{ session('error') }}</div>
	@endif

	<div class="***REMOVED***-page__toolbar ***REMOVED***-page__toolbar--between">
		<form method="get" action="{{ route('andamentos') }}" class="***REMOVED***-form-inline ***REMOVED***-search-form">
			<input type="text" name="q" value="{{ e($search ?? '') }}" class="***REMOVED***-form-input ***REMOVED***-search-input" placeholder="{{ __('Search progress...') }}" />
			<button type="submit" class="***REMOVED***-button ***REMOVED***-button--primary">{{ __('Search') }}</button>
			@if (!empty($search))
				<a href="{{ route('andamentos') }}" class="***REMOVED***-button ***REMOVED***-button--secondary">{{ __('Clear') }}</a>
			@endif
		</form>
	</div>

	<div class="***REMOVED***-surface ***REMOVED***-surface--table">
		<table class="***REMOVED***list ***REMOVED***list--full ***REMOVED***list--modern">
			<colgroup>
				<col class="***REMOVED***-col ***REMOVED***-col--code" />
				<col class="***REMOVED***-col ***REMOVED***-col--name" />
				<col class="***REMOVED***-col ***REMOVED***-col--key" />
				<col class="***REMOVED***-col ***REMOVED***-col--progress" />
				<col class="***REMOVED***-col ***REMOVED***-col--type" />
				<col class="***REMOVED***-col ***REMOVED***-col--panel" />
				<col class="***REMOVED***-col ***REMOVED***-col--title" />
				<col class="***REMOVED***-col ***REMOVED***-col--actions" />
			</colgroup>
			<thead>
				<tr>
					<th class="order">{{ __('Code') }}</th>
					<th class="order">{{ __('Name') }}</th>
					<th class="order">{{ __('Code Name') }}</th>
					<th class="order">{{ __('Progress') }}</th>
					<th class="order">{{ __('Type') }}</th>
					<th class="order">{{ __('Panel') }}</th>
					<th class="order">{{ __('Panel Title') }}</th>
					<th class="order">{{ __('Options') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($andamentos as $andamento)
					<tr>
						<td class="order">{{ (int) $andamento['anda_id'] }}</td>
						<td class="order">{{ e($andamento['nome']) }}</td>
						<td class="order">{{ e($andamento['chave']) }}</td>
						<td class="order">{{ e($andamento['anda_neo']) }}</td>
						<td class="order"><span class="***REMOVED***-type-pill {{ (int) $andamento['especie'] === 1 ? '***REMOVED***-type-pill--production' : '***REMOVED***-type-pill--financial' }}">{{ e($metaTipos[(int) $andamento['especie']]) }}</span></td>
						<td class="order">{{ e($andamento['painel']) }}</td>
						<td class="order">{{ e($andamento['titulo']) }}</td>
						<td class="order">
							<div class="***REMOVED***-table-actions">
								<a href="{{ route('andamentos.edit', (int) $andamento['anda_id']) }}" class="***REMOVED***-link-button">{{ __('Edit') }}</a>
								<a href="{{ route('andamentos.confirm-delete', (int) $andamento['anda_id']) }}" class="***REMOVED***-link-button ***REMOVED***-link-button--danger">{{ __('Delete') }}</a>
							</div>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	@if (method_exists($andamentos, 'hasPages') && $andamentos->hasPages())
		<div class="***REMOVED***-pagination">
			<div class="***REMOVED***-pagination__summary">
				{{ __('Showing :from to :to of :total items', ['from' => $andamentos->firstItem(), 'to' => $andamentos->lastItem(), 'total' => $andamentos->total()]) }}
			</div>
			<div class="***REMOVED***-pagination__links">
				@if ($andamentos->onFirstPage())
					<span class="***REMOVED***-pagination__link is-disabled">{{ __('Previous') }}</span>
				@else
					<a href="{{ $andamentos->appends(request()->except('page'))->previousPageUrl() }}" class="***REMOVED***-pagination__link">{{ __('Previous') }}</a>
				@endif

				@foreach ($andamentos->appends(request()->except('page'))->getUrlRange(max(1, $andamentos->currentPage() - 2), min($andamentos->lastPage(), $andamentos->currentPage() + 2)) as $page => $url)
					@if ($page === $andamentos->currentPage())
						<span class="***REMOVED***-pagination__link is-active">{{ $page }}</span>
					@else
						<a href="{{ $url }}" class="***REMOVED***-pagination__link">{{ $page }}</a>
					@endif
				@endforeach

				@if ($andamentos->hasMorePages())
					<a href="{{ $andamentos->appends(request()->except('page'))->nextPageUrl() }}" class="***REMOVED***-pagination__link">{{ __('Next') }}</a>
				@else
					<span class="***REMOVED***-pagination__link is-disabled">{{ __('Next') }}</span>
				@endif
			</div>
		</div>
	@endif
</div>
