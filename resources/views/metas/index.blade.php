@php
	$bankCode = isset($bank['banco_cod']) ? $bank['banco_cod'] : '';
	$allowGlobalRegion = !empty($allowGlobalRegion);
	$contextQuery = http_build_query([
		'startBanco' => $startBanco,
		'startDate' => $startDate,
		'mes' => $mes,
		'ano' => $ano,
	]);
@endphp
<div class="admin-page admin-page--flat metas-page">
	@if (session('status'))
		<div class="admin-flash admin-flash--success">{{ session('status') }}</div>
	@endif

	@if (session('error'))
		<div class="admin-flash admin-flash--error">{{ session('error') }}</div>
	@endif

	<div class="admin-page__toolbar admin-page__toolbar--between">
		<div class="admin-page__eyebrow">{{ __('Goals') }}</div>
		<div class="admin-form-inline">
			<a href="{{ route('metas') }}" class="admin-button admin-button--secondary">{{ __('Change context') }}</a>
			<a href="{{ route('metas.create', ['startBanco' => $startBanco, 'startDate' => $startDate, 'mes' => $mes, 'ano' => $ano]) }}" class="admin-button admin-button--primary">{{ __('New Goal') }}</a>
		</div>
	</div>

	<div class="metas-context-grid">
		<div class="admin-card metas-context-card">
			<div class="metas-context-card__label">{{ __('Client') }}</div>
			<div class="metas-context-card__value">{{ e((string) $bankCode) }}</div>
		</div>
		<div class="admin-card metas-context-card">
			<div class="metas-context-card__label">{{ __('Month/Year') }}</div>
			<div class="metas-context-card__value">{{ e((string) $startDate) }}</div>
		</div>
		<div class="admin-card metas-context-card">
			<div class="metas-context-card__label">{{ __('Total financial goal') }}</div>
			<div class="metas-context-card__value">R$ {{ number_format((float) $totalFinanceiro, 2, ',', '.') }}</div>
		</div>
	</div>

	<div class="admin-surface admin-surface--table">
		<table class="adminlist adminlist--full adminlist--modern metas-admin-table">
			<colgroup>
				<col class="admin-col admin-col--drag" />
				<col class="admin-col admin-col--name" />
				<col class="admin-col admin-col--region" />
				<col class="admin-col admin-col--progress" />
				<col class="admin-col admin-col--type" />
				<col class="admin-col admin-col--value" />
				<col class="admin-col admin-col--actions" />
			</colgroup>
			<thead>
				<tr>
					<th class="order metas-order-header">{{ __('Order') }}</th>
					<th class="order">{{ __('Client') }}</th>
					<th class="order">{{ __('Region') }}</th>
					<th class="order">{{ __('Progress') }}</th>
					<th class="order">{{ __('Type') }}</th>
					<th class="order">{{ __('Quantity/Value') }}</th>
					<th class="order">{{ __('Options') }}</th>
				</tr>
			</thead>
			<tbody
				id="metas-sortable"
				data-reorder-url="{{ route('metas.reorder.page') }}"
				data-bank-id="{{ (int) $startBanco }}"
				data-month="{{ (int) $mes }}"
				data-year="{{ (int) $ano }}"
			>
				@forelse ($metas as $arr)
					@php $metaValor = ((int) $arr['especie'] === 2) ? number_format((float) $arr['meta_valor'], 2, ',', '.') : number_format((float) $arr['meta_valor'], 0, '', ''); @endphp
					<tr data-meta-id="{{ (int) $arr['meta_id'] }}">
						<td class="order metas-order-cell">
							<button type="button" class="metas-drag-handle" title="{{ __('Drag to reorder') }}" aria-label="{{ __('Drag to reorder') }}">
								<span></span><span></span><span></span>
							</button>
						</td>
						<td class="order">{{ e((string) $arr['banco_name']) }}</td>
						<td class="order">{{ e(isset($arr['regiao_nome']) && $arr['regiao_nome'] !== '' ? (string) $arr['regiao_nome'] : __('All regions')) }}</td>
						<td class="order">{{ e((string) $arr['nome']) }}</td>
						<td class="order"><span class="admin-type-pill {{ (int) $arr['especie'] === 1 ? 'admin-type-pill--production' : 'admin-type-pill--financial' }}">{{ e((string) $metaTipos[$arr['especie']]) }}</span></td>
						<td class="order">{{ $metaValor }}</td>
						<td class="order">
							<div class="admin-table-actions">
								<a href="{{ route('metas.edit', ['id' => (int) $arr['meta_id'], 'startBanco' => $startBanco, 'startDate' => $startDate, 'mes' => $mes, 'ano' => $ano]) }}" class="admin-link-button">{{ __('Edit') }}</a>
								<a href="{{ route('metas.confirm-delete', ['id' => (int) $arr['meta_id'], 'startBanco' => $startBanco, 'startDate' => $startDate, 'mes' => $mes, 'ano' => $ano]) }}" class="admin-link-button admin-link-button--danger">{{ __('Delete') }}</a>
							</div>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="7" class="order metas-empty">{{ __('No goals found for this context.') }}</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
	<p class="metas-order-help">{{ __('Drag and drop the rows to define the display order of the goals in this context.') }}</p>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
	metaListInit();
});
</script>
