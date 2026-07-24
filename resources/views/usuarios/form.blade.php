@php
	$linkedClients = old('banco_neo', isset($user['id_cliente']) ? (string) $user['id_cliente'] : '');
	$linkedClientIds = array();
	foreach (explode(',', (string) $linkedClients) as $clientId) {
		$clientId = trim($clientId);
		if ($clientId !== '') {
			$linkedClientIds[] = $clientId;
		}
	}
	$linkedRegions = old('regiao_neo', implode(',', isset($user['region_ids']) && is_array($user['region_ids']) ? $user['region_ids'] : array()));
	$linkedRegionIds = array();
	foreach (explode(',', (string) $linkedRegions) as $regionId) {
		$regionId = trim($regionId);
		if ($regionId !== '') {
			$linkedRegionIds[] = $regionId;
		}
	}
@endphp
<script>
window.arsSelectAjaxUrl = "{{ url('ajax/select') }}";
</script>
<div class="***REMOVED***-page ***REMOVED***-page--flat">
	<div class="***REMOVED***-page__toolbar ***REMOVED***-page__toolbar--between">
		<div class="***REMOVED***-page__eyebrow">{{ $pageTitle }}</div>
		<a href="{{ $backUrl }}" class="***REMOVED***-button ***REMOVED***-button--secondary">{{ __('Back') }}</a>
	</div>

	@if ($errors->any())
		<div class="***REMOVED***-flash ***REMOVED***-flash--error">
			@foreach ($errors->all() as $error)
				<div>{{ $error }}</div>
			@endforeach
		</div>
	@endif

	<div class="***REMOVED***-surface ***REMOVED***-surface--form">
		<form method="post" action="{{ $formAction }}" class="***REMOVED***-form">
			@csrf
			@if ($formMethod !== 'POST')
				@method($formMethod)
			@endif
			<input type="hidden" name="id_usu" value="{{ (int) old('id_usu', $user['id_usu']) }}" />
			<input type="hidden" name="banco_neo" id="banco_neo" value="{{ e($linkedClients) }}" />
			<input type="hidden" name="regiao_neo" id="regiao_neo" value="{{ e($linkedRegions) }}" />

			<div class="***REMOVED***-form-grid">
				<div class="***REMOVED***-form-group">
					<label for="nome_usu">{{ __('Full Name') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="nome_usu" id="nome_usu" value="{{ old('nome_usu', $user['nome_usu']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="login_usu">{{ __('User') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="login_usu" id="login_usu" value="{{ old('login_usu', $user['login_usu']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="email_usu">{{ __('E-mail') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="email_usu" id="email_usu" value="{{ old('email_usu', $user['email_usu']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="nivel_usu">{{ __('Level') }}</label>
					<select class="***REMOVED***-form-input ***REMOVED***-form-select" name="nivel_usu" id="nivel_usu">
						<option value=""></option>
						@foreach ($levelOptions as $optionValue => $optionLabel)
							<option value="{{ $optionValue }}"{{ old('nivel_usu', $user['nivel_usu']) === $optionValue ? ' selected="selected"' : '' }}>{{ $optionLabel }}</option>
						@endforeach
					</select>
				</div>
				<div class="***REMOVED***-form-group">
					<label for="setor_usu">{{ __('Sector') }}</label>
					<select class="***REMOVED***-form-input ***REMOVED***-form-select" name="setor_usu" id="setor_usu">
						<option value="0">{{ __('All') }}</option>
						@foreach ($areas as $area)
							<option value="{{ (int) $area['area_id'] }}"{{ (string) old('setor_usu', $user['id_setor']) === (string) $area['area_id'] ? ' selected="selected"' : '' }}>{{ e($area['area_nome']) }}</option>
						@endforeach
					</select>
				</div>
				<div class="***REMOVED***-form-group">
					<label for="status_usu">{{ __('Status') }}</label>
					<select class="***REMOVED***-form-input ***REMOVED***-form-select" name="status_usu" id="status_usu">
						<option value=""></option>
						@foreach ($statusOptions as $optionValue => $optionLabel)
							<option value="{{ $optionValue }}"{{ old('status_usu', $user['status_usu']) === $optionValue ? ' selected="selected"' : '' }}>{{ $optionLabel }}</option>
						@endforeach
					</select>
				</div>
				<div class="***REMOVED***-form-group ***REMOVED***-form-group--full">
					<label id="sel_banco">{{ __('Clients') }}</label>
					<div class="usuario-clientes-box">
						<div id="usuario-clientes-vinculados" class="usuario-clientes-lista"></div>
						<div id="usuario-clientes-inputs"></div>
						<div id="usuario-clientes-vazio" class="usuario-clientes-vazio">{{ __('No linked clients.') }}</div>
					</div>
					<div class="***REMOVED***-form-inline">
						<select class="***REMOVED***-form-input ***REMOVED***-form-select ***REMOVED***-form-select--wide" name="banco_usu_pool" id="banco_usu_pool" title="{{ __('Clients') }}"></select>
						<button type="button" class="***REMOVED***-button ***REMOVED***-button--secondary ***REMOVED***-button--compact" onclick="usuarioClientesAdicionar();">+</button>
					</div>
				</div>
				<div class="***REMOVED***-form-group">
					<label for="regiao_modo">{{ __('Region Mode') }}</label>
					<select class="***REMOVED***-form-input ***REMOVED***-form-select" name="regiao_modo" id="regiao_modo">
						@foreach ($regionModeOptions as $optionValue => $optionLabel)
							<option value="{{ $optionValue }}"{{ old('regiao_modo', $user['regiao_modo']) === $optionValue ? ' selected="selected"' : '' }}>{{ $optionLabel }}</option>
						@endforeach
					</select>
				</div>
				<div class="***REMOVED***-form-group ***REMOVED***-form-group--full" id="usuario-regioes-row">
					<label>{{ __('Regions') }}</label>
					<div class="usuario-regioes-box">
						<div id="usuario-regioes-vinculadas" class="usuario-regioes-lista"></div>
						<div id="usuario-regioes-inputs"></div>
						<div id="usuario-regioes-vazio" class="usuario-regioes-vazio">{{ __('No linked regions.') }}</div>
					</div>
					<div class="***REMOVED***-form-inline">
						<select class="***REMOVED***-form-input ***REMOVED***-form-select ***REMOVED***-form-select--wide" name="regiao_usu_pool" id="regiao_usu_pool" title="{{ __('Regions') }}">
							<option value=""></option>
							@foreach ($regions as $region)
								<option value="{{ (int) $region['regiao_id'] }}">{{ e($region['regiao_nome']) }}</option>
							@endforeach
						</select>
						<button type="button" class="***REMOVED***-button ***REMOVED***-button--secondary ***REMOVED***-button--compact" onclick="usuarioRegioesAdicionar();">+</button>
					</div>
				</div>
				<div class="***REMOVED***-form-group ***REMOVED***-form-group--full">
					<div class="***REMOVED***-form-subgrid">
						<div class="***REMOVED***-form-group">
							<label for="senha_usu1">{{ __('Password') }}</label>
							<input type="password" class="***REMOVED***-form-input" name="senha_usu1" id="senha_usu1" value="" />
						</div>
						<div class="***REMOVED***-form-group">
							<label for="senha_usu2">{{ __('Repeat Password') }}</label>
							<input type="password" class="***REMOVED***-form-input" name="senha_usu2" id="senha_usu2" value="" />
						</div>
					</div>
				</div>
			</div>

			<div class="***REMOVED***-form-actions">
				<button type="submit" class="***REMOVED***-button ***REMOVED***-button--primary">{{ $submitLabel }}</button>
				<a href="{{ $backUrl }}" class="***REMOVED***-button ***REMOVED***-button--secondary">{{ __('Exit') }}</a>
			</div>
		</form>
	</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
	userFormInit(
		$('#setor_usu').val(),
		@json($linkedClientIds, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
		@json(isset($user['clients']) ? $user['clients'] : array(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
		@json($linkedRegionIds, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
		@json(isset($user['regions']) ? $user['regions'] : array(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
	);
});
</script>
