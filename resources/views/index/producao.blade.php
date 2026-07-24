@php
	$productionIcons = [
		'report' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v6h6"/><path d="M10 13h6"/><path d="M10 17h6"/></svg>',
	];
@endphp

<div class="admin-page admin-page--flat production-page">
	<div class="admin-page__toolbar admin-page__toolbar--between">
		<div>
			<div class="admin-page__eyebrow">{{ __('Production') }}</div>
			<div class="admin-dashboard-copy">{{ __('Choose the sector, region, and reference period to open the production report.') }}</div>
		</div>
	</div>

	<div class="admin-surface admin-surface--form production-filter-surface">
		<div class="admin-form-grid production-filter-grid">
			<div class="admin-form-group">
				<label for="startSetor">{{ __('Sector') }}</label>
				<select name="startSetor" id="startSetor" class="admin-form-input admin-form-select">
					<option value="">{{ __('All sectors') }}</option>
					@foreach ($areas as $area)
						<option value="{{ $area['area_id'] }}"{{ ((string) $startSector === (string) $area['area_id']) ? ' selected="selected"' : '' }}>{{ e($area['area_nome']) }}</option>
					@endforeach
				</select>
			</div>

			@if (!empty($showRegionSelector))
				<div class="admin-form-group">
					<label for="regiao_id">{{ __('Region') }}</label>
					<select name="regiao_id" id="regiao_id" class="admin-form-input admin-form-select">
						<option value="0">{{ __('All regions') }}</option>
						@foreach ($regions as $region)
							<option value="{{ (int) $region['regiao_id'] }}"{{ ((int) $selectedRegionId === (int) $region['regiao_id']) ? ' selected="selected"' : '' }}>{{ e($region['regiao_nome']) }}</option>
						@endforeach
					</select>
				</div>
			@else
				<input type="hidden" name="regiao_id" id="regiao_id" value="{{ (int) $selectedRegionId }}"/>
			@endif

			<div class="admin-form-group">
				<label for="startDate">{{ __('Month/Year') }}</label>
				<input type="text" name="startDate" id="startDate" class="admin-form-input date-picker" readonly="readonly" value="{{ e($monthYearLabel) }}"/>
				<span id="obg_date" class="admin-form-hint"></span>
				<input type="hidden" name="mes" id="mes" value="{{ (int) $month }}"/>
				<input type="hidden" name="ano" id="ano" value="{{ (int) $year }}"/>
			</div>
		</div>
	</div>

	<div class="production-action-grid">
		<a href="#" onclick="EnviarPagina('relatorio',true,'',''); return false;" class="admin-dashboard-card production-action-card">
			<span class="admin-dashboard-card__icon">{!! $productionIcons['report'] !!}</span>
			<span class="admin-dashboard-card__title">{{ __('Report') }}</span>
			<span class="admin-dashboard-card__description">{{ __('Open the production analysis for the selected filters.') }}</span>
		</a>
	</div>
</div>
