@php
	$tiposSelecionados = old('anda_neo', isset($andamento['anda_neo']) ? (string) $andamento['anda_neo'] : '');
	$tiposArray = [];
	foreach (explode(',', (string) $tiposSelecionados) as $tipoSelecionado) {
		$tipoSelecionado = trim($tipoSelecionado);
		if ($tipoSelecionado !== '') {
			$tiposArray[] = $tipoSelecionado;
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
			<input type="hidden" name="anda_id" value="{{ (int) old('anda_id', $andamento['anda_id']) }}" />
			<input type="hidden" name="anda_neo" id="anda_neo" value="{{ e($tiposSelecionados) }}" />

			<div class="***REMOVED***-form-grid">
				<div class="***REMOVED***-form-group">
					<label for="nome">{{ __('Progress Name') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="nome" id="nome" value="{{ old('nome', $andamento['nome']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="chave">{{ __('Key Name') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="chave" id="chave" value="{{ old('chave', $andamento['chave']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="painel">{{ __('Panel') }}</label>
					<select class="***REMOVED***-form-input ***REMOVED***-form-select" name="painel" id="painel">
						@foreach ($yesNoOptions as $optionValue => $optionLabel)
							<option value="{{ $optionValue }}"{{ old('painel', $andamento['painel']) === $optionValue ? ' selected="selected"' : '' }}>{{ $optionLabel }}</option>
						@endforeach
					</select>
				</div>
				<div class="***REMOVED***-form-group">
					<label for="titulo">{{ __('Panel Title') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="titulo" id="titulo" value="{{ old('titulo', $andamento['titulo']) }}" />
				</div>
				<div class="***REMOVED***-form-group ***REMOVED***-form-group--full">
					<label for="especie">{{ __('Type') }}</label>
					<select class="***REMOVED***-form-input ***REMOVED***-form-select" name="especie" id="especie">
						<option value=""></option>
						@foreach ($metaTipos as $optionValue => $optionLabel)
							<option value="{{ $optionValue }}"{{ (string) old('especie', $andamento['especie']) === (string) $optionValue ? ' selected="selected"' : '' }}>{{ $optionLabel }}</option>
						@endforeach
					</select>
				</div>
				<div class="***REMOVED***-form-group ***REMOVED***-form-group--full">
					<label id="sel_anda">{{ __('Select progress items') }}</label>
					<div id="andamento-tipos-vinculados" class="andamento-tipos-lista"></div>
					<div id="andamento-tipos-inputs"></div>
					<div id="andamento-tipos-vazio" class="andamento-tipos-vazio">{{ __('No linked progress items.') }}</div>
					<div class="***REMOVED***-form-inline">
						<select class="***REMOVED***-form-input ***REMOVED***-form-select ***REMOVED***-form-select--wide" name="andam_name_pool" id="andam_name_pool" title="{{ __('Progress') }}"></select>
						<button type="button" class="***REMOVED***-button ***REMOVED***-button--secondary ***REMOVED***-button--compact" onclick="andamentoTiposAdicionar();">+</button>
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
	andamentoFormInit($('#especie').val(), @json($tiposArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
});
</script>
