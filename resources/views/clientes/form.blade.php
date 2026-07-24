@php
	$linkedWallets = old('dados_json', implode(',', isset($client['dados_codes']) && is_array($client['dados_codes']) ? $client['dados_codes'] : array()));
	$linkedWalletArray = [];
	foreach (explode(',', (string) $linkedWallets) as $walletCode) {
		$walletCode = trim($walletCode);
		if ($walletCode !== '') {
			$linkedWalletArray[] = $walletCode;
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
			<input type="hidden" name="banco_id" value="{{ (int) old('banco_id', $client['banco_id']) }}" />
			<input type="hidden" name="cartei_num" id="cartei_num" value="0" />
			<input type="hidden" name="dados_json" id="dados_json" value="{{ e($linkedWallets) }}" />

			<div class="***REMOVED***-form-grid">
				<div class="***REMOVED***-form-group">
					<label for="banco_name">{{ __('Client Name') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="banco_name" id="banco_name" value="{{ old('banco_name', $client['banco_name']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="banco_cod">{{ __('Code Text') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="banco_cod" id="banco_cod" value="{{ old('banco_cod', $client['banco_cod']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="banco_area">{{ __('Sector') }}</label>
					<select class="***REMOVED***-form-input ***REMOVED***-form-select" name="banco_area" id="banco_area">
						<option value=""></option>
						@foreach ($areas as $area)
							<option value="{{ (int) $area['area_id'] }}"{{ (string) old('banco_area', $client['banco_area']) === (string) $area['area_id'] ? ' selected="selected"' : '' }}>{{ e($area['area_nome']) }}</option>
						@endforeach
					</select>
				</div>
				<div class="***REMOVED***-form-group">
					<label for="banco_status">{{ __('Status') }}</label>
					<select class="***REMOVED***-form-input ***REMOVED***-form-select" name="banco_status" id="banco_status">
						<option value=""></option>
						@foreach ($statusOptions as $optionValue => $optionLabel)
							<option value="{{ $optionValue }}"{{ old('banco_status', $client['banco_status']) === $optionValue ? ' selected="selected"' : '' }}>{{ $optionLabel }}</option>
						@endforeach
					</select>
				</div>
				<div class="***REMOVED***-form-group">
					<label for="banco_class">{{ __('Classification') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="banco_class" id="banco_class" value="{{ old('banco_class', $client['banco_class']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="simulador">{{ __('Simulator/Decision Deadline') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="simulador" id="simulador" value="{{ old('simulador', $client['simulador']) }}" />
				</div>
				<div class="***REMOVED***-form-group ***REMOVED***-form-group--full">
					<label for="banco_curto">{{ __('Short Name') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="banco_curto" id="banco_curto" value="{{ old('banco_curto', $client['banco_curto']) }}" />
				</div>
				<div class="***REMOVED***-form-group ***REMOVED***-form-group--full">
					<label>{{ __('Linked wallets') }}</label>
					<div id="cliente-carteiras-vinculadas" class="cliente-carteiras-lista"></div>
					<div id="cliente-carteiras-inputs"></div>
					<div id="cliente-carteiras-vazio" class="cliente-carteiras-vazio">{{ __('No linked wallets.') }}</div>
					<div class="***REMOVED***-form-inline">
						<select class="***REMOVED***-form-input ***REMOVED***-form-select ***REMOVED***-form-select--wide" name="dados_name_pool" id="dados_name_pool" title="{{ __('Wallet') }}">
							<option value=""></option>
							@foreach ($carteiras as $carteira)
								<option value="{{ e($carteira['Carteira']) }}">{{ e($carteira['Carteira']) }}</option>
							@endforeach
						</select>
						<button type="button" class="***REMOVED***-button ***REMOVED***-button--secondary ***REMOVED***-button--compact" onclick="clienteCarteirasAdicionar();">+</button>
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
	clienteFormInit(@json($linkedWalletArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
});
</script>
