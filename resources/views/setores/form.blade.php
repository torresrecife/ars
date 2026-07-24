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
			<input type="hidden" name="area_id" value="{{ (int) old('area_id', $sector['area_id']) }}" />

			<div class="***REMOVED***-form-grid">
				<div class="***REMOVED***-form-group ***REMOVED***-form-group--full">
					<label for="area_nome">{{ __('Sector Name') }}</label>
					<input type="text" class="***REMOVED***-form-input" name="area_nome" id="area_nome" value="{{ old('area_nome', $sector['area_nome']) }}" />
				</div>
			</div>

			<div class="***REMOVED***-form-actions">
				<button type="submit" class="***REMOVED***-button ***REMOVED***-button--primary">{{ $submitLabel }}</button>
				<a href="{{ $backUrl }}" class="***REMOVED***-button ***REMOVED***-button--secondary">{{ __('Exit') }}</a>
			</div>
		</form>
	</div>
</div>
