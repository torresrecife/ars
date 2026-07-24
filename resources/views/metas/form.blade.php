@php
	$bankCode = isset($bank['banco_cod']) ? $bank['banco_cod'] : '';
	$regionsList = isset($regions) ? $regions : [];
@endphp
<div class="***REMOVED***-page ***REMOVED***-page--flat metas-page">
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

	<div class="metas-context-grid">
		<div class="***REMOVED***-card metas-context-card">
			<div class="metas-context-card__label">{{ __('Client') }}</div>
			<div class="metas-context-card__value">{{ e((string) $bankCode) }}</div>
		</div>
		<div class="***REMOVED***-card metas-context-card">
			<div class="metas-context-card__label">{{ __('Month/Year') }}</div>
			<div class="metas-context-card__value">{{ e((string) $context['startDate']) }}</div>
		</div>
		<div class="***REMOVED***-card metas-context-card">
			<div class="metas-context-card__label">{{ __('Mode') }}</div>
			<div class="metas-context-card__value">{{ $isEditMode ? __('Edit Goal') : __('Batch Goal Creation') }}</div>
		</div>
	</div>

	<div class="***REMOVED***-surface ***REMOVED***-surface--form">
		<form method="post" action="{{ $formAction }}" class="***REMOVED***-form" id="meta-form">
			@csrf
			@if ($formMethod !== 'POST')
				@method($formMethod)
			@endif
			<input type="hidden" name="meta_id" id="meta_id" value="{{ (int) old('meta_id', $metaContext['meta_id']) }}" />
			<input type="hidden" name="banco_id" id="banco_id" value="{{ (int) old('banco_id', $metaContext['banco_id']) }}" />
			<input type="hidden" name="meta_mes" id="meta_mes" value="{{ (int) old('meta_mes', $metaContext['meta_mes']) }}" />
			<input type="hidden" name="meta_ano" id="meta_ano" value="{{ (int) old('meta_ano', $metaContext['meta_ano']) }}" />
			<input type="hidden" name="numes" id="numes" value="1" />

			<div class="metas-form-panel">
				<div class="metas-form-panel__header">
					<div class="metas-form-panel__title">{{ __('Goal lines') }}</div>
					@if (!$isEditMode)
						<button type="button" class="***REMOVED***-button ***REMOVED***-button--secondary" id="meta-add-row">{{ __('Add line') }}</button>
					@endif
				</div>

				<div id="metas-rows" class="metas-rows">
					<div class="metas-row" data-row="1">
						<div class="metas-row__primary">
							<div class="***REMOVED***-form-group metas-row__field metas-row__field--progress">
								<label for="meta_name_1">{{ __('Goal') }}</label>
								<select class="***REMOVED***-form-input ***REMOVED***-form-select js-meta-type-source" name="meta_name_1" id="meta_name_1" data-row="1">
									<option value=""></option>
									@foreach ($andamentos as $andamento)
										<option value="{{ (int) $andamento['anda_id'] }}" data-especie="{{ (int) $andamento['especie'] }}"{{ (string) old('meta_name_1', $goalRow['meta_name_1']) === (string) $andamento['anda_id'] ? ' selected="selected"' : '' }}>{{ e((string) $andamento['nome'] . ' (' . $metaTipos[$andamento['especie']] . ')') }}</option>
									@endforeach
								</select>
							</div>
							<div class="***REMOVED***-form-group metas-row__field metas-row__field--region">
								<label for="regiao_id_1">{{ __('Region') }}</label>
								<select class="***REMOVED***-form-input ***REMOVED***-form-select" name="regiao_id_1" id="regiao_id_1">
									@if ($allowGlobalRegion)
										<option value="">{{ __('All regions') }}</option>
									@endif
									@foreach ($regionsList as $region)
										<option value="{{ (int) $region['regiao_id'] }}"{{ (string) old('regiao_id_1', $goalRow['regiao_id_1']) === (string) $region['regiao_id'] ? ' selected="selected"' : '' }}>{{ e((string) $region['regiao_nome']) }}</option>
									@endforeach
								</select>
							</div>
							<div class="***REMOVED***-form-group metas-row__field metas-row__field--value">
								<label for="meta_valor_1">{{ __('Total goal') }}</label>
								<input type="text" class="***REMOVED***-form-input js-meta-total" name="meta_valor_1" id="meta_valor_1" value="{{ old('meta_valor_1', $goalRow['meta_valor_1']) }}" data-row="1" />
							</div>
							<div class="***REMOVED***-form-group metas-row__field metas-row__field--manual">
								<label for="def_sem_1">{{ __('Manual definition') }}</label>
								<div class="metas-toggle-wrap">
									<input type="checkbox" class="js-meta-manual" name="def_sem_1" id="def_sem_1" value="Y" data-row="1"{{ old('def_sem_1', $goalRow['def_sem_1']) === 'Y' ? ' checked="checked"' : '' }} />
								</div>
							</div>
						</div>
						<div class="metas-row__secondary">
							<div class="metas-row__weeks">
							<div class="***REMOVED***-form-group metas-row__field metas-row__field--week js-meta-week-field" data-week="1">
								<label for="sem1_valor_1">{{ __('Week 1') }}</label>
								<input type="text" class="***REMOVED***-form-input js-meta-week" name="sem1_valor_1" id="sem1_valor_1" value="{{ old('sem1_valor_1', $goalRow['sem1_valor_1']) }}" data-row="1" />
							</div>
							<div class="***REMOVED***-form-group metas-row__field metas-row__field--week js-meta-week-field" data-week="2">
								<label for="sem2_valor_1">{{ __('Week 2') }}</label>
								<input type="text" class="***REMOVED***-form-input js-meta-week" name="sem2_valor_1" id="sem2_valor_1" value="{{ old('sem2_valor_1', $goalRow['sem2_valor_1']) }}" data-row="1" />
							</div>
							<div class="***REMOVED***-form-group metas-row__field metas-row__field--week js-meta-week-field" data-week="3">
								<label for="sem3_valor_1">{{ __('Week 3') }}</label>
								<input type="text" class="***REMOVED***-form-input js-meta-week" name="sem3_valor_1" id="sem3_valor_1" value="{{ old('sem3_valor_1', $goalRow['sem3_valor_1']) }}" data-row="1" />
							</div>
							<div class="***REMOVED***-form-group metas-row__field metas-row__field--week js-meta-week-field" data-week="4">
								<label for="sem4_valor_1">{{ __('Week 4') }}</label>
								<input type="text" class="***REMOVED***-form-input js-meta-week" name="sem4_valor_1" id="sem4_valor_1" value="{{ old('sem4_valor_1', $goalRow['sem4_valor_1']) }}" data-row="1" />
							</div>
							<div class="***REMOVED***-form-group metas-row__field metas-row__field--week js-meta-week-field" data-week="5">
								<label for="sem5_valor_1">{{ __('Week 5') }}</label>
								<input type="text" class="***REMOVED***-form-input js-meta-week" name="sem5_valor_1" id="sem5_valor_1" value="{{ old('sem5_valor_1', $goalRow['sem5_valor_1']) }}" data-row="1" />
							</div>
							</div>
							@if (!$isEditMode)
								<div class="metas-row__actions">
									<button type="button" class="***REMOVED***-button ***REMOVED***-button--secondary js-meta-remove-row is-hidden">{{ __('Remove') }}</button>
								</div>
							@endif
						</div>
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
<script type="text/template" id="meta-row-template">
<div class="metas-row" data-row="__INDEX__">
	<div class="metas-row__primary">
		<div class="***REMOVED***-form-group metas-row__field metas-row__field--progress">
			<label for="meta_name___INDEX__">{{ __('Goal') }}</label>
			<select class="***REMOVED***-form-input ***REMOVED***-form-select js-meta-type-source" name="meta_name___INDEX__" id="meta_name___INDEX__" data-row="__INDEX__">
				<option value=""></option>
				@foreach ($andamentos as $andamento)
					<option value="{{ (int) $andamento['anda_id'] }}" data-especie="{{ (int) $andamento['especie'] }}">{{ e((string) $andamento['nome'] . ' (' . $metaTipos[$andamento['especie']] . ')') }}</option>
				@endforeach
			</select>
		</div>
		<div class="***REMOVED***-form-group metas-row__field metas-row__field--region">
			<label for="regiao_id___INDEX__">{{ __('Region') }}</label>
			<select class="***REMOVED***-form-input ***REMOVED***-form-select" name="regiao_id___INDEX__" id="regiao_id___INDEX__">
				@if ($allowGlobalRegion)
					<option value="">{{ __('All regions') }}</option>
				@endif
				@foreach ($regionsList as $region)
					<option value="{{ (int) $region['regiao_id'] }}">{{ e((string) $region['regiao_nome']) }}</option>
				@endforeach
			</select>
		</div>
		<div class="***REMOVED***-form-group metas-row__field metas-row__field--value">
			<label for="meta_valor___INDEX__">{{ __('Total goal') }}</label>
			<input type="text" class="***REMOVED***-form-input js-meta-total" name="meta_valor___INDEX__" id="meta_valor___INDEX__" value="" data-row="__INDEX__" />
		</div>
		<div class="***REMOVED***-form-group metas-row__field metas-row__field--manual">
			<label for="def_sem___INDEX__">{{ __('Manual definition') }}</label>
			<div class="metas-toggle-wrap">
				<input type="checkbox" class="js-meta-manual" name="def_sem___INDEX__" id="def_sem___INDEX__" value="Y" data-row="__INDEX__" />
			</div>
		</div>
	</div>
	<div class="metas-row__secondary">
		<div class="metas-row__weeks">
		<div class="***REMOVED***-form-group metas-row__field metas-row__field--week js-meta-week-field" data-week="1">
			<label for="sem1_valor___INDEX__">{{ __('Week 1') }}</label>
			<input type="text" class="***REMOVED***-form-input js-meta-week" name="sem1_valor___INDEX__" id="sem1_valor___INDEX__" value="" data-row="__INDEX__" />
		</div>
		<div class="***REMOVED***-form-group metas-row__field metas-row__field--week js-meta-week-field" data-week="2">
			<label for="sem2_valor___INDEX__">{{ __('Week 2') }}</label>
			<input type="text" class="***REMOVED***-form-input js-meta-week" name="sem2_valor___INDEX__" id="sem2_valor___INDEX__" value="" data-row="__INDEX__" />
		</div>
		<div class="***REMOVED***-form-group metas-row__field metas-row__field--week js-meta-week-field" data-week="3">
			<label for="sem3_valor___INDEX__">{{ __('Week 3') }}</label>
			<input type="text" class="***REMOVED***-form-input js-meta-week" name="sem3_valor___INDEX__" id="sem3_valor___INDEX__" value="" data-row="__INDEX__" />
		</div>
		<div class="***REMOVED***-form-group metas-row__field metas-row__field--week js-meta-week-field" data-week="4">
			<label for="sem4_valor___INDEX__">{{ __('Week 4') }}</label>
			<input type="text" class="***REMOVED***-form-input js-meta-week" name="sem4_valor___INDEX__" id="sem4_valor___INDEX__" value="" data-row="__INDEX__" />
		</div>
		<div class="***REMOVED***-form-group metas-row__field metas-row__field--week js-meta-week-field" data-week="5">
			<label for="sem5_valor___INDEX__">{{ __('Week 5') }}</label>
			<input type="text" class="***REMOVED***-form-input js-meta-week" name="sem5_valor___INDEX__" id="sem5_valor___INDEX__" value="" data-row="__INDEX__" />
		</div>
		</div>
		<div class="metas-row__actions">
			<button type="button" class="***REMOVED***-button ***REMOVED***-button--secondary js-meta-remove-row">{{ __('Remove') }}</button>
		</div>
	</div>
</div>
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
	metaFormInit({
		isEditMode: {{ $isEditMode ? 'true' : 'false' }},
		initialRows: 1
	});
});
</script>
