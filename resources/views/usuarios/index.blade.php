<div class="admin-page admin-page--flat">
	@if (session('status'))
		<div class="admin-flash admin-flash--success">{{ session('status') }}</div>
	@endif

	@if (session('error'))
		<div class="admin-flash admin-flash--error">{{ session('error') }}</div>
	@endif

	<div class="admin-page__toolbar admin-page__toolbar--between">
		<form method="get" action="{{ route('usuarios') }}" class="admin-form-inline admin-search-form">
			<input type="text" name="q" value="{{ e($search ?? '') }}" class="admin-form-input admin-search-input" placeholder="{{ __('Search users...') }}" />
			<button type="submit" class="admin-button admin-button--primary">{{ __('Search') }}</button>
			@if (!empty($search))
				<a href="{{ route('usuarios') }}" class="admin-button admin-button--secondary">{{ __('Clear') }}</a>
			@endif
		</form>
	</div>

	<div class="admin-surface admin-surface--table">
		<table class="adminlist adminlist--full adminlist--modern">
			<colgroup>
				<col class="admin-col admin-col--code" />
				<col class="admin-col admin-col--name" />
				<col class="admin-col admin-col--login" />
				<col class="admin-col admin-col--level" />
				<col class="admin-col admin-col--datetime" />
				<col class="admin-col admin-col--datetime" />
				<col class="admin-col admin-col--email" />
				<col class="admin-col admin-col--status" />
				<col class="admin-col admin-col--actions" />
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
							<div class="admin-table-actions">
								<a href="{{ route('usuarios.edit', (int) $user['id_usu']) }}" class="admin-link-button">{{ __('Edit') }}</a>
								<a href="{{ route('usuarios.confirm-delete', (int) $user['id_usu']) }}" class="admin-link-button admin-link-button--danger">{{ __('Delete') }}</a>
							</div>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	@if (method_exists($users, 'hasPages') && $users->hasPages())
		<div class="admin-pagination">
			<div class="admin-pagination__summary">
				{{ __('Showing :from to :to of :total items', ['from' => $users->firstItem(), 'to' => $users->lastItem(), 'total' => $users->total()]) }}
			</div>
			<div class="admin-pagination__links">
				@if ($users->onFirstPage())
					<span class="admin-pagination__link is-disabled">{{ __('Previous') }}</span>
				@else
					<a href="{{ $users->appends(request()->except('page'))->previousPageUrl() }}" class="admin-pagination__link">{{ __('Previous') }}</a>
				@endif

				@foreach ($users->appends(request()->except('page'))->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
					@if ($page === $users->currentPage())
						<span class="admin-pagination__link is-active">{{ $page }}</span>
					@else
						<a href="{{ $url }}" class="admin-pagination__link">{{ $page }}</a>
					@endif
				@endforeach

				@if ($users->hasMorePages())
					<a href="{{ $users->appends(request()->except('page'))->nextPageUrl() }}" class="admin-pagination__link">{{ __('Next') }}</a>
				@else
					<span class="admin-pagination__link is-disabled">{{ __('Next') }}</span>
				@endif
			</div>
		</div>
	@endif
</div>
