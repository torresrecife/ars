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
			<input type="hidden" name="area_id" value="{{ (int) old('area_id', $sector['area_id']) }}" />

			<div class="admin-form-grid">
				<div class="admin-form-group admin-form-group--full">
					<label for="area_nome">{{ __('Sector Name') }}</label>
					<input type="text" class="admin-form-input" name="area_nome" id="area_nome" value="{{ old('area_nome', $sector['area_nome']) }}" />
				</div>
			</div>

			<div class="admin-form-actions">
				<button type="submit" class="admin-button admin-button--primary">{{ $submitLabel }}</button>
				<a href="{{ $backUrl }}" class="admin-button admin-button--secondary">{{ __('Exit') }}</a>
			</div>
		</form>
	</div>
</div>
