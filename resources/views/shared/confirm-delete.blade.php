<div class="admin-page admin-page--flat">
	<div class="admin-page__toolbar admin-page__toolbar--between">
		<div class="admin-page__eyebrow">{{ $pageTitle }}</div>
		<a href="{{ $backUrl }}" class="admin-button admin-button--secondary">{{ __('Back') }}</a>
	</div>

	<div class="admin-surface admin-surface--form admin-confirm-delete">
		<div class="admin-confirm-delete__header">
			<div class="admin-confirm-delete__title">{{ __('Confirm deletion') }}</div>
			<div class="admin-confirm-delete__copy">{{ $message }}</div>
		</div>

		<div class="admin-confirm-delete__summary">
			<div class="admin-confirm-delete__label">{{ __('Selected item') }}</div>
			<div class="admin-confirm-delete__value">{{ $itemName }}</div>
		</div>

		<form method="post" action="{{ $formAction }}" class="admin-form">
			@csrf
			@method('DELETE')
			<div class="admin-form-actions">
				<button type="submit" class="admin-button admin-button--danger">{{ __('Delete') }}</button>
				<a href="{{ $backUrl }}" class="admin-button admin-button--secondary">{{ __('Cancel') }}</a>
			</div>
		</form>
	</div>
</div>
