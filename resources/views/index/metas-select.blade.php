<div class="***REMOVED***-page ***REMOVED***-page--flat metas-select-page">
	@if (session('error'))
		<div class="***REMOVED***-flash ***REMOVED***-flash--error">{{ session('error') }}</div>
	@endif

	<div class="***REMOVED***-surface ***REMOVED***-surface--form metas-select-surface">
		<form method="get" action="{{ route('metas') }}" class="***REMOVED***-form metas-select-form">
			<div class="***REMOVED***-page__eyebrow">{{ __('Manage Goals') }}</div>
			<div class="metas-select-copy">{{ __('Choose the client and reference month before creating or editing goals.') }}</div>

			<div class="***REMOVED***-form-grid">
				<div class="***REMOVED***-form-group">
					<label for="startBanco">{{ __('Bank') }}</label>
					<select name="startBanco" id="startBanco" class="***REMOVED***-form-input ***REMOVED***-form-select nav-select">
						<option value=""></option>
						@foreach ($banks as $bank)
							<option value="{{ $bank['banco_id'] }}">{{ e($bank['banco_name'] . ' (' . $bank['banco_class'] . ')') }}</option>
						@endforeach
					</select>
				</div>
				<div class="***REMOVED***-form-group">
					<label for="startDate">{{ __('Month/Year') }}</label>
					<input type="text" name="startDate" id="startDate" class="***REMOVED***-form-input date-picker" readonly="readonly" value="{{ e($monthYearLabel) }}"/>
					<span id="obg_date" class="***REMOVED***-form-hint"></span>
					<input type="hidden" name="mes" id="mes" value="{{ e((string) $month) }}"/>
					<input type="hidden" name="ano" id="ano" value="{{ e((string) $year) }}"/>
				</div>
			</div>

			<div class="***REMOVED***-form-actions">
				<button type="submit" class="***REMOVED***-button ***REMOVED***-button--primary">{{ __('Continue') }}</button>
			</div>
		</form>
	</div>
</div>
