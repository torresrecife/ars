<div class="***REMOVED***-page ***REMOVED***-page--flat">
	@if (session('status'))
		<div class="***REMOVED***-flash ***REMOVED***-flash--success">{{ session('status') }}</div>
	@endif

	@if (session('error'))
		<div class="***REMOVED***-flash ***REMOVED***-flash--error">{{ session('error') }}</div>
	@endif

	<div class="***REMOVED***-page__toolbar ***REMOVED***-page__toolbar--between">
		<form method="get" action="{{ route('usuarios') }}" class="***REMOVED***-form-inline ***REMOVED***-search-form">
			<input type="text" name="q" value="{{ e($search ?? '') }}" class="***REMOVED***-form-input ***REMOVED***-search-input" placeholder="{{ __('Search users...') }}" />
			<button type="submit" class="***REMOVED***-button ***REMOVED***-button--primary">{{ __('Search') }}</button>
			@if (!empty($search))
				<a href="{{ route('usuarios') }}" class="***REMOVED***-button ***REMOVED***-button--secondary">{{ __('Clear') }}</a>
			@endif
		</form>
	</div>

	<div class="***REMOVED***-surface ***REMOVED***-surface--table">
		<table class="***REMOVED***list ***REMOVED***list--full ***REMOVED***list--modern">
			<colgroup>
				<col class="***REMOVED***-col ***REMOVED***-col--code" />
				<col class="***REMOVED***-col ***REMOVED***-col--name" />
				<col class="***REMOVED***-col ***REMOVED***-col--login" />
				<col class="***REMOVED***-col ***REMOVED***-col--level" />
				<col class="***REMOVED***-col ***REMOVED***-col--datetime" />
				<col class="***REMOVED***-col ***REMOVED***-col--datetime" />
				<col class="***REMOVED***-col ***REMOVED***-col--email" />
				<col class="***REMOVED***-col ***REMOVED***-col--status" />
				<col class="***REMOVED***-col ***REMOVED***-col--actions" />
			</colgroup>
			<thead>
				<tr>
					<th class="order">{{ __('Code') }}</th>
					<th class="order">{{ __('Name') }}</th>
					<th class="order">{{ __('User') }}</th>
					<th class="order">{{ __('Level') }}</th>
					<th class="order">{{ __('Last Access') }}</th>
					<th class="order">{{ __('Created At') }}</th>
					<th class="order">{{ __('E-mail') }}</th>
					<th class="order">{{ __('Status') }}</th>
					<th class="order">{{ __('Options') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($users as $user)
					@php
						$acesso = empty($user['acesso_usu']) || $user['acesso_usu'] === '0000-00-00 00:00:00' ? '' : strftime('%d/%m/%Y %H:%M:%S', strtotime($user['acesso_usu']));
					@endphp
					<tr>
						<td class="order">{{ (int) $user['id_usu'] }}</td>
						<td class="order">{{ e($user['nome_usu']) }}</td>
						<td class="order">{{ e($user['login_usu']) }}</td>
						<td class="order">{{ e($user['nivel_usu']) }}</td>
						<td class="order">{{ $acesso }}</td>
						<td class="order">{{ strftime('%d/%m/%Y %H:%M:%S', strtotime($user['data_cad'])) }}</td>
						<td class="order">{{ e($user['email_usu']) }}</td>
						<td class="order">{{ e($user['status_usu']) }}</td>
						<td class="order">
							<div class="***REMOVED***-table-actions">
								<a href="{{ route('usuarios.edit', (int) $user['id_usu']) }}" class="***REMOVED***-link-button">{{ __('Edit') }}</a>
								<a href="{{ route('usuarios.confirm-delete', (int) $user['id_usu']) }}" class="***REMOVED***-link-button ***REMOVED***-link-button--danger">{{ __('Delete') }}</a>
							</div>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	@if (method_exists($users, 'hasPages') && $users->hasPages())
		<div class="***REMOVED***-pagination">
			<div class="***REMOVED***-pagination__summary">
				{{ __('Showing :from to :to of :total items', ['from' => $users->firstItem(), 'to' => $users->lastItem(), 'total' => $users->total()]) }}
			</div>
			<div class="***REMOVED***-pagination__links">
				@if ($users->onFirstPage())
					<span class="***REMOVED***-pagination__link is-disabled">{{ __('Previous') }}</span>
				@else
					<a href="{{ $users->appends(request()->except('page'))->previousPageUrl() }}" class="***REMOVED***-pagination__link">{{ __('Previous') }}</a>
				@endif

				@foreach ($users->appends(request()->except('page'))->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
					@if ($page === $users->currentPage())
						<span class="***REMOVED***-pagination__link is-active">{{ $page }}</span>
					@else
						<a href="{{ $url }}" class="***REMOVED***-pagination__link">{{ $page }}</a>
					@endif
				@endforeach

				@if ($users->hasMorePages())
					<a href="{{ $users->appends(request()->except('page'))->nextPageUrl() }}" class="***REMOVED***-pagination__link">{{ __('Next') }}</a>
				@else
					<span class="***REMOVED***-pagination__link is-disabled">{{ __('Next') }}</span>
				@endif
			</div>
		</div>
	@endif
</div>
