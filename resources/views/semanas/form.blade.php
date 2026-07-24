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
			<input type="hidden" name="id_sem" value="{{ (int) old('id_sem', $week['semanas_id']) }}" />

			<div class="***REMOVED***-form-grid">
				<div class="***REMOVED***-form-group">
					<label for="mes_sem">{{ __('Month') }}</label>
					<select class="***REMOVED***-form-input ***REMOVED***-form-select" name="mes_sem" id="mes_sem">
						<option value=""></option>
						@foreach ($months as $monthNumber => $monthLabel)
							<option value="{{ (int) $monthNumber }}"{{ (string) old('mes_sem', $week['mes']) === (string) $monthNumber ? ' selected="selected"' : '' }}>{{ $monthLabel }}</option>
						@endforeach
					</select>
				</div>
				<div class="***REMOVED***-form-group">
					<label for="ano_sem">{{ __('Year') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="ano_sem" id="ano_sem" maxlength="4" value="{{ old('ano_sem', $week['ano']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="ini1_sem">{{ __('1st Week start') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="ini1_sem" id="ini1_sem" value="{{ old('ini1_sem', $week['ini_1']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="fim1_sem">{{ __('1st Week end') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="fim1_sem" id="fim1_sem" value="{{ old('fim1_sem', $week['fim_1']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="ini2_sem">{{ __('2nd Week start') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="ini2_sem" id="ini2_sem" value="{{ old('ini2_sem', $week['ini_2']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="fim2_sem">{{ __('2nd Week end') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="fim2_sem" id="fim2_sem" value="{{ old('fim2_sem', $week['fim_2']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="ini3_sem">{{ __('3rd Week start') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="ini3_sem" id="ini3_sem" value="{{ old('ini3_sem', $week['ini_3']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="fim3_sem">{{ __('3rd Week end') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="fim3_sem" id="fim3_sem" value="{{ old('fim3_sem', $week['fim_3']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="ini4_sem">{{ __('4th Week start') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="ini4_sem" id="ini4_sem" value="{{ old('ini4_sem', $week['ini_4']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="fim4_sem">{{ __('4th Week end') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="fim4_sem" id="fim4_sem" value="{{ old('fim4_sem', $week['fim_4']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="ini5_sem">{{ __('5th Week start') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="ini5_sem" id="ini5_sem" value="{{ old('ini5_sem', $week['ini_5']) }}" />
				</div>
				<div class="***REMOVED***-form-group">
					<label for="fim5_sem">{{ __('5th Week end') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="fim5_sem" id="fim5_sem" value="{{ old('fim5_sem', $week['fim_5']) }}" />
				</div>
			</div>

			<div class="***REMOVED***-form-actions">
				<button type="submit" class="***REMOVED***-button ***REMOVED***-button--primary">{{ $submitLabel }}</button>
				<a href="{{ $backUrl }}" class="***REMOVED***-button ***REMOVED***-button--secondary">{{ __('Exit') }}</a>
			</div>
		</form>
	</div>
</div>
