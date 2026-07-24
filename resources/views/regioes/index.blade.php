<div class="admin-page admin-page--flat admin-module-offset">
<div class="admin-page__toolbar admin-page__toolbar--between">
	<form method="get" action="{{ route('regioes') }}" class="admin-form-inline admin-search-form">
		<input type="text" name="q" value="{{ e($search ?? '') }}" class="admin-form-input admin-search-input" placeholder="{{ __('Search regions...') }}" />
		<button type="submit" class="admin-button admin-button--primary">{{ __('Search') }}</button>
		@if (!empty($search))
			<a href="{{ route('regioes') }}" class="admin-button admin-button--secondary">{{ __('Clear') }}</a>
		@endif
	</form>
</div>
<div class="admin-surface admin-surface--table">
<table class="adminlist adminlist--full adminlist--modern">
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
			<div class="admin-table-actions">
				<a href="{{ route('regioes.edit', (int) $region['regiao_id']) }}" class="admin-link-button">{{ __('Edit') }}</a>
				<form method="post" action="{{ route('regioes.destroy.page', (int) $region['regiao_id']) }}" onsubmit="return confirm({{ json_encode(__('Do you really want to delete the region :name?', ['name' => $region['regiao_nome']]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }});">
					@csrf
					@method('DELETE')
					<button type="submit" class="admin-link-button admin-link-button--danger">{{ __('Delete') }}</button>
				</form>
			</div>
		</td>
	</tr>
@endforeach
</table>
</div>
@if (method_exists($regions, 'hasPages') && $regions->hasPages())
	<div class="admin-pagination">
		<div class="admin-pagination__summary">
			{{ __('Showing :from to :to of :total items', ['from' => $regions->firstItem(), 'to' => $regions->lastItem(), 'total' => $regions->total()]) }}
		</div>
		<div class="admin-pagination__links">
			@if ($regions->onFirstPage())
				<span class="admin-pagination__link is-disabled">{{ __('Previous') }}</span>
			@else
				<a href="{{ $regions->appends(request()->except('page'))->previousPageUrl() }}" class="admin-pagination__link">{{ __('Previous') }}</a>
			@endif
			@foreach ($regions->appends(request()->except('page'))->getUrlRange(max(1, $regions->currentPage() - 2), min($regions->lastPage(), $regions->currentPage() + 2)) as $page => $url)
				@if ($page === $regions->currentPage())
					<span class="admin-pagination__link is-active">{{ $page }}</span>
				@else
					<a href="{{ $url }}" class="admin-pagination__link">{{ $page }}</a>
				@endif
			@endforeach
			@if ($regions->hasMorePages())
				<a href="{{ $regions->appends(request()->except('page'))->nextPageUrl() }}" class="admin-pagination__link">{{ __('Next') }}</a>
			@else
				<span class="admin-pagination__link is-disabled">{{ __('Next') }}</span>
			@endif
		</div>
	</div>
@endif
</div>
