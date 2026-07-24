<div class="admin-page admin-page--flat">
	@if (session('status'))
		<div class="admin-flash admin-flash--success">{{ session('status') }}</div>
	@endif

	@if (session('error'))
		<div class="admin-flash admin-flash--error">{{ session('error') }}</div>
	@endif

	<div class="admin-page__toolbar admin-page__toolbar--between">
		<form method="get" action="{{ route('andamentos') }}" class="admin-form-inline admin-search-form">
			<input type="text" name="q" value="{{ e($search ?? '') }}" class="admin-form-input admin-search-input" placeholder="{{ __('Search progress...') }}" />
			<button type="submit" class="admin-button admin-button--primary">{{ __('Search') }}</button>
			@if (!empty($search))
				<a href="{{ route('andamentos') }}" class="admin-button admin-button--secondary">{{ __('Clear') }}</a>
			@endif
		</form>
	</div>

	<div class="admin-surface admin-surface--table">
		<table class="adminlist adminlist--full adminlist--modern">
			<colgroup>
				<col class="admin-col admin-col--code" />
				<col class="admin-col admin-col--name" />
				<col class="admin-col admin-col--key" />
				<col class="admin-col admin-col--progress" />
				<col class="admin-col admin-col--type" />
				<col class="admin-col admin-col--panel" />
				<col class="admin-col admin-col--title" />
				<col class="admin-col admin-col--actions" />
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
						<td class="order"><span class="admin-type-pill {{ (int) $andamento['especie'] === 1 ? 'admin-type-pill--production' : 'admin-type-pill--financial' }}">{{ e($metaTipos[(int) $andamento['especie']]) }}</span></td>
						<td class="order">{{ e($andamento['painel']) }}</td>
						<td class="order">{{ e($andamento['titulo']) }}</td>
						<td class="order">
							<div class="admin-table-actions">
								<a href="{{ route('andamentos.edit', (int) $andamento['anda_id']) }}" class="admin-link-button">{{ __('Edit') }}</a>
								<form method="post" action="{{ route('andamentos.destroy', (int) $andamento['anda_id']) }}" onsubmit="return confirm({{ json_encode(__('Do you really want to delete the progress :name?', ['name' => $andamento['nome']]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }});">
									@csrf
									@method('DELETE')
									<button type="submit" class="admin-link-button admin-link-button--danger">{{ __('Delete') }}</button>
								</form>
							</div>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	@if (method_exists($andamentos, 'hasPages') && $andamentos->hasPages())
		<div class="admin-pagination">
			<div class="admin-pagination__summary">
				{{ __('Showing :from to :to of :total items', ['from' => $andamentos->firstItem(), 'to' => $andamentos->lastItem(), 'total' => $andamentos->total()]) }}
			</div>
			<div class="admin-pagination__links">
				@if ($andamentos->onFirstPage())
					<span class="admin-pagination__link is-disabled">{{ __('Previous') }}</span>
				@else
					<a href="{{ $andamentos->appends(request()->except('page'))->previousPageUrl() }}" class="admin-pagination__link">{{ __('Previous') }}</a>
				@endif

				@foreach ($andamentos->appends(request()->except('page'))->getUrlRange(max(1, $andamentos->currentPage() - 2), min($andamentos->lastPage(), $andamentos->currentPage() + 2)) as $page => $url)
					@if ($page === $andamentos->currentPage())
						<span class="admin-pagination__link is-active">{{ $page }}</span>
					@else
						<a href="{{ $url }}" class="admin-pagination__link">{{ $page }}</a>
					@endif
				@endforeach

				@if ($andamentos->hasMorePages())
					<a href="{{ $andamentos->appends(request()->except('page'))->nextPageUrl() }}" class="admin-pagination__link">{{ __('Next') }}</a>
				@else
					<span class="admin-pagination__link is-disabled">{{ __('Next') }}</span>
				@endif
			</div>
		</div>
	@endif
</div>
