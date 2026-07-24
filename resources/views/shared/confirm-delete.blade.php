<div class="***REMOVED***-page ***REMOVED***-page--flat">
	<div class="***REMOVED***-page__toolbar ***REMOVED***-page__toolbar--between">
		<div class="***REMOVED***-page__eyebrow">{{ $pageTitle }}</div>
		<a href="{{ $backUrl }}" class="***REMOVED***-button ***REMOVED***-button--secondary">{{ __('Back') }}</a>
	</div>

	<div class="***REMOVED***-surface ***REMOVED***-surface--form ***REMOVED***-confirm-delete">
		<div class="***REMOVED***-confirm-delete__header">
			<div class="***REMOVED***-confirm-delete__title">{{ __('Confirm deletion') }}</div>
			<div class="***REMOVED***-confirm-delete__copy">{{ $message }}</div>
		</div>

		<div class="***REMOVED***-confirm-delete__summary">
			<div class="***REMOVED***-confirm-delete__label">{{ __('Selected item') }}</div>
			<div class="***REMOVED***-confirm-delete__value">{{ $itemName }}</div>
		</div>

		<form method="post" action="{{ $formAction }}" class="***REMOVED***-form">
			@csrf
			@method('DELETE')
			<div class="***REMOVED***-form-actions">
				<button type="submit" class="***REMOVED***-button ***REMOVED***-button--danger">{{ __('Delete') }}</button>
				<a href="{{ $backUrl }}" class="***REMOVED***-button ***REMOVED***-button--secondary">{{ __('Cancel') }}</a>
			</div>
		</form>
	</div>
</div>
