<div class="***REMOVED***-page ***REMOVED***-page--flat ***REMOVED***-module-offset">
@if (session('status'))
	<div class="***REMOVED***-flash ***REMOVED***-flash--success">{{ session('status') }}</div>
@endif

@if (session('error'))
	<div class="***REMOVED***-flash ***REMOVED***-flash--error">{{ session('error') }}</div>
@endif

<div class="***REMOVED***-page__toolbar ***REMOVED***-page__toolbar--between">
	<form method="get" action="{{ route('semanas') }}" class="***REMOVED***-form-inline ***REMOVED***-search-form">
		<input type="text" name="q" value="{{ e($search ?? '') }}" class="***REMOVED***-form-input ***REMOVED***-search-input" placeholder="{{ __('Search weeks...') }}" />
		<button type="submit" class="***REMOVED***-button ***REMOVED***-button--primary">{{ __('Search') }}</button>
		@if (!empty($search))
			<a href="{{ route('semanas') }}" class="***REMOVED***-button ***REMOVED***-button--secondary">{{ __('Clear') }}</a>
		@endif
	</form>
</div>
<div class="***REMOVED***-surface ***REMOVED***-surface--table">
<table class="***REMOVED***list ***REMOVED***list--full ***REMOVED***list--modern">
	<colgroup>
		<col class="***REMOVED***-col ***REMOVED***-col--code" />
		<col class="***REMOVED***-col ***REMOVED***-col--name" />
		<col class="***REMOVED***-col ***REMOVED***-col--code" />
		<col class="***REMOVED***-col ***REMOVED***-col--week" />
		<col class="***REMOVED***-col ***REMOVED***-col--week" />
		<col class="***REMOVED***-col ***REMOVED***-col--week" />
		<col class="***REMOVED***-col ***REMOVED***-col--week" />
		<col class="***REMOVED***-col ***REMOVED***-col--week" />
		<col class="***REMOVED***-col ***REMOVED***-col--datetime" />
		<col class="***REMOVED***-col ***REMOVED***-col--datetime" />
		<col class="***REMOVED***-col ***REMOVED***-col--actions" />
	</colgroup>
	<thead>
		<tr>
			<th class="order">{{ __('Code') }}</th>
			<th class="order">{{ __('Month') }}</th>
			<th class="order">{{ __('Year') }}</th>
			<th class="order">{{ __('1st Week') }}</th>
			<th class="order">{{ __('2nd Week') }}</th>
			<th class="order">{{ __('3rd Week') }}</th>
			<th class="order">{{ __('4th Week') }}</th>
			<th class="order">{{ __('5th Week') }}</th>
			<th class="order">{{ __('Updated At') }}</th>
			<th class="order">{{ __('Created At') }}</th>
			<th class="order">{{ __('Settings') }}</th>
		</tr>
	</thead>
	<tbody>
	@foreach ($weeks as $arr)
		<tr>
			<td class="order">{{ $arr['semanas_id'] }}</td>
			<td class="order">{{ isset($months[$arr['mes']]) ? $months[$arr['mes']] : $arr['mes'] }}</td>
			<td class="order">{{ $arr['ano'] }}</td>
			<td class="order">{!! $arr['ini_1'] . "&nbsp;&agrave;&nbsp;" . $arr['fim_1'] !!}</td>
			<td class="order">{!! $arr['ini_2'] . "&nbsp;&agrave;&nbsp;" . $arr['fim_2'] !!}</td>
			<td class="order">{!! $arr['ini_3'] . "&nbsp;&agrave;&nbsp;" . $arr['fim_3'] !!}</td>
			<td class="order">{!! $arr['ini_4'] . "&nbsp;&agrave;&nbsp;" . $arr['fim_4'] !!}</td>
			<td class="order">{!! ($arr['ini_5'] ? $arr['ini_5'] . "&nbsp;&agrave;&nbsp;" . $arr['fim_5'] : '-') !!}</td>
			<td class="order">{{ $arr['dataalt'] }}</td>
			<td class="order">{{ $arr['datacad'] }}</td>
			<td class="order">
				<div class="***REMOVED***-table-actions">
					<a href="{{ route('semanas.edit', (int) $arr['semanas_id']) }}" class="***REMOVED***-link-button">{{ __('Edit') }}</a>
					<form method="post" action="{{ route('semanas.destroy.page', (int) $arr['semanas_id']) }}" onsubmit="return confirm({{ json_encode(__('Do you really want to delete the week :name?', ['name' => (isset($months[$arr['mes']]) ? $months[$arr['mes']] : $arr['mes']) . ' / ' . $arr['ano']]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }});">
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
@if (method_exists($weeks, 'hasPages') && $weeks->hasPages())
	<div class="***REMOVED***-pagination">
		<div class="***REMOVED***-pagination__summary">
			{{ __('Showing :from to :to of :total items', ['from' => $weeks->firstItem(), 'to' => $weeks->lastItem(), 'total' => $weeks->total()]) }}
		</div>
		<div class="***REMOVED***-pagination__links">
			@if ($weeks->onFirstPage())
				<span class="***REMOVED***-pagination__link is-disabled">{{ __('Previous') }}</span>
			@else
				<a href="{{ $weeks->appends(request()->except('page'))->previousPageUrl() }}" class="***REMOVED***-pagination__link">{{ __('Previous') }}</a>
			@endif
			@foreach ($weeks->appends(request()->except('page'))->getUrlRange(max(1, $weeks->currentPage() - 2), min($weeks->lastPage(), $weeks->currentPage() + 2)) as $page => $url)
				@if ($page === $weeks->currentPage())
					<span class="***REMOVED***-pagination__link is-active">{{ $page }}</span>
				@else
					<a href="{{ $url }}" class="***REMOVED***-pagination__link">{{ $page }}</a>
				@endif
			@endforeach
			@if ($weeks->hasMorePages())
				<a href="{{ $weeks->appends(request()->except('page'))->nextPageUrl() }}" class="***REMOVED***-pagination__link">{{ __('Next') }}</a>
			@else
				<span class="***REMOVED***-pagination__link is-disabled">{{ __('Next') }}</span>
			@endif
		</div>
	</div>
@endif
</div>
