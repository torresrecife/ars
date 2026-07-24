@php
	$linkedUfs = old('regiao_ufs', implode(',', isset($region['ufs']) && is_array($region['ufs']) ? $region['ufs'] : array()));
	$linkedUfArray = [];
	foreach (explode(',', (string) $linkedUfs) as $uf) {
		$uf = trim($uf);
		if ($uf !== '') {
			$linkedUfArray[] = strtoupper($uf);
		}
	}
@endphp
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
			<input type="hidden" name="regiao_id" value="{{ (int) old('regiao_id', $region['regiao_id']) }}" />
			<input type="hidden" name="regiao_ufs" id="regiao_ufs" value="{{ e($linkedUfs) }}" />

			<div class="***REMOVED***-form-grid">
				<div class="***REMOVED***-form-group">
					<label for="regiao_nome">{{ __('Name') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="regiao_nome" id="regiao_nome" value="{{ old('regiao_nome', $region['regiao_nome']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="regiao_slug">Slug</label>
					<input type="text" class="***REMOVED***-form-input" name="regiao_slug" id="regiao_slug" value="{{ old('regiao_slug', $region['regiao_slug']) }}" />
				</div>
				<div class="***REMOVED***-form-group ***REMOVED***-form-group--full">
					<label>{{ __('Status') }}</label>
					<select class="***REMOVED***-form-input ***REMOVED***-form-select" name="regiao_status" id="regiao_status">
						@foreach ($statusOptions as $optionValue => $optionLabel)
							<option value="{{ $optionValue }}"{{ old('regiao_status', $region['regiao_status']) === $optionValue ? ' selected="selected"' : '' }}>{{ $optionLabel }}</option>
						@endforeach
					</select>
				</div>
				<div class="***REMOVED***-form-group ***REMOVED***-form-group--full">
					<label>UFs</label>
					<div class="regiao-ufs-box">
						<div id="regiao-ufs-vinculadas" class="regiao-ufs-lista"></div>
						<div id="regiao-ufs-vazio" class="regiao-ufs-vazio">{{ __('No linked states.') }}</div>
					</div>
					<div class="***REMOVED***-form-inline">
						<select class="***REMOVED***-form-input ***REMOVED***-form-select ***REMOVED***-form-select--wide" name="regiao_uf_pool" id="regiao_uf_pool" title="UF">
							<option value=""></option>
							@foreach ($ufs as $uf)
								<option value="{{ $uf }}">{{ $uf }}</option>
							@endforeach
						</select>
						<button type="button" class="***REMOVED***-button ***REMOVED***-button--secondary ***REMOVED***-button--compact" onclick="regiaoUfsAdicionar();">+</button>
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
	regiaoFormInit(@json($linkedUfArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
});
</script>
