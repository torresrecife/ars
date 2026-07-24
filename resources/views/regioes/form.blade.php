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
<div class="admin-page admin-page--flat">
	<div class="admin-page__toolbar admin-page__toolbar--between">
		<div class="admin-page__eyebrow">{{ $pageTitle }}</div>
		<a href="{{ $backUrl }}" class="admin-button admin-button--secondary">{{ __('Back') }}</a>
	</div>

	@if ($errors->any())
		<div class="admin-flash admin-flash--error">
			@foreach ($errors->all() as $error)
				<div>{{ $error }}</div>
			@endforeach
		</div>
	@endif

	<div class="admin-surface admin-surface--form">
		<form method="post" action="{{ $formAction }}" class="admin-form">
			@csrf
			@if ($formMethod !== 'POST')
				@method($formMethod)
			@endif
			<input type="hidden" name="regiao_id" value="{{ (int) old('regiao_id', $region['regiao_id']) }}" />
			<input type="hidden" name="regiao_ufs" id="regiao_ufs" value="{{ e($linkedUfs) }}" />

			<div class="admin-form-grid">
				<div class="admin-form-group">
					<label for="regiao_nome">{{ __('Name') }}</label>
					<input type="text" class="admin-form-input" name="regiao_nome" id="regiao_nome" value="{{ old('regiao_nome', $region['regiao_nome']) }}" />
				</div>
				<div class="admin-form-group">
					<label for="regiao_slug">Slug</label>
					<input type="text" class="admin-form-input" name="regiao_slug" id="regiao_slug" value="{{ old('regiao_slug', $region['regiao_slug']) }}" />
				</div>
				<div class="admin-form-group admin-form-group--full">
					<label>{{ __('Status') }}</label>
					<select class="admin-form-input admin-form-select" name="regiao_status" id="regiao_status">
						@foreach ($statusOptions as $optionValue => $optionLabel)
							<option value="{{ $optionValue }}"{{ old('regiao_status', $region['regiao_status']) === $optionValue ? ' selected="selected"' : '' }}>{{ $optionLabel }}</option>
						@endforeach
					</select>
				</div>
				<div class="admin-form-group admin-form-group--full">
					<label>UFs</label>
					<div class="regiao-ufs-box">
						<div id="regiao-ufs-vinculadas" class="regiao-ufs-lista"></div>
						<div id="regiao-ufs-vazio" class="regiao-ufs-vazio">{{ __('No linked states.') }}</div>
					</div>
					<div class="admin-form-inline">
						<select class="admin-form-input admin-form-select admin-form-select--wide" name="regiao_uf_pool" id="regiao_uf_pool" title="UF">
							<option value=""></option>
							@foreach ($ufs as $uf)
								<option value="{{ $uf }}">{{ $uf }}</option>
							@endforeach
						</select>
						<button type="button" class="admin-button admin-button--secondary admin-button--compact" onclick="regiaoUfsAdicionar();">+</button>
					</div>
				</div>
			</div>

			<div class="admin-form-actions">
				<button type="submit" class="admin-button admin-button--primary">{{ $submitLabel }}</button>
				<a href="{{ $backUrl }}" class="admin-button admin-button--secondary">{{ __('Exit') }}</a>
			</div>
		</form>
	</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
	regiaoFormInit(@json($linkedUfArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
});
</script>
