<div class="admin-page admin-page--flat metas-select-page">
	@if (session('error'))
		<div class="admin-flash admin-flash--error">{{ session('error') }}</div>
	@endif

	<div class="admin-surface admin-surface--form metas-select-surface">
		<form method="get" action="{{ route('metas') }}" class="admin-form metas-select-form">
			<div class="admin-page__eyebrow">{{ __('Manage Goals') }}</div>
			<div class="metas-select-copy">{{ __('Choose the client and reference month before creating or editing goals.') }}</div>

			<div class="admin-form-grid">
				<div class="admin-form-group">
					<label for="startBanco">{{ __('Bank') }}</label>
					<select name="startBanco" id="startBanco" class="admin-form-input admin-form-select nav-select">
						<option value=""></option>
						@foreach ($banks as $bank)
							<option value="{{ $bank['banco_id'] }}">{{ e($bank['banco_name'] . ' (' . $bank['banco_class'] . ')') }}</option>
						@endforeach
					</select>
				</div>
				<div class="admin-form-group">
					<label for="startDate">{{ __('Month/Year') }}</label>
					<input type="text" name="startDate" id="startDate" class="admin-form-input date-picker" readonly="readonly" value="{{ e($monthYearLabel) }}"/>
					<span id="obg_date" class="admin-form-hint"></span>
					<input type="hidden" name="mes" id="mes" value="{{ e((string) $month) }}"/>
					<input type="hidden" name="ano" id="ano" value="{{ e((string) $year) }}"/>
				</div>
			</div>

			<div class="admin-form-actions">
				<button type="submit" class="admin-button admin-button--primary">{{ __('Continue') }}</button>
			</div>
		</form>
	</div>
</div>
