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
<div class="***REMOVED***-page ***REMOVED***-page--flat metas-page">
	@if (session('status'))
		<div class="***REMOVED***-flash ***REMOVED***-flash--success">{{ session('status') }}</div>
	@endif

	@if (session('error'))
		<div class="***REMOVED***-flash ***REMOVED***-flash--error">{{ session('error') }}</div>
	@endif

	<div class="***REMOVED***-page__toolbar ***REMOVED***-page__toolbar--between">
		<div class="***REMOVED***-page__eyebrow">{{ __('Goals') }}</div>
		<div class="***REMOVED***-form-inline">
			<a href="{{ route('metas') }}" class="***REMOVED***-button ***REMOVED***-button--secondary">{{ __('Change context') }}</a>
			<a href="{{ route('metas.create', ['startBanco' => $startBanco, 'startDate' => $startDate, 'mes' => $mes, 'ano' => $ano]) }}" class="***REMOVED***-button ***REMOVED***-button--primary">{{ __('New Goal') }}</a>
		</div>
	</div>

	<div class="metas-context-grid">
		<div class="***REMOVED***-card metas-context-card">
			<div class="metas-context-card__label">{{ __('Client') }}</div>
			<div class="metas-context-card__value">{{ e((string) $bankCode) }}</div>
		</div>
		<div class="***REMOVED***-card metas-context-card">
			<div class="metas-context-card__label">{{ __('Month/Year') }}</div>
			<div class="metas-context-card__value">{{ e((string) $startDate) }}</div>
		</div>
		<div class="***REMOVED***-card metas-context-card">
			<div class="metas-context-card__label">{{ __('Total financial goal') }}</div>
			<div class="metas-context-card__value">R$ {{ number_format((float) $totalFinanceiro, 2, ',', '.') }}</div>
		</div>
	</div>

	<div class="***REMOVED***-surface ***REMOVED***-surface--table">
		<table class="***REMOVED***list ***REMOVED***list--full ***REMOVED***list--modern metas-***REMOVED***-table">
			<colgroup>
				<col class="***REMOVED***-col ***REMOVED***-col--name" />
				<col class="***REMOVED***-col ***REMOVED***-col--region" />
				<col class="***REMOVED***-col ***REMOVED***-col--progress" />
				<col class="***REMOVED***-col ***REMOVED***-col--type" />
				<col class="***REMOVED***-col ***REMOVED***-col--value" />
				<col class="***REMOVED***-col ***REMOVED***-col--actions" />
			</colgroup>
			<thead>
				<tr>
					<th class="order">{{ __('Client') }}</th>
					<th class="order">{{ __('Region') }}</th>
					<th class="order">{{ __('Progress') }}</th>
					<th class="order">{{ __('Type') }}</th>
					<th class="order">{{ __('Quantity/Value') }}</th>
					<th class="order">{{ __('Options') }}</th>
				</tr>
			</thead>
			<tbody>
				@forelse ($metas as $arr)
					@php $metaValor = ((int) $arr['especie'] === 2) ? number_format((float) $arr['meta_valor'], 2, ',', '.') : number_format((float) $arr['meta_valor'], 0, '', ''); @endphp
					<tr>
						<td class="order">{{ e((string) $arr['banco_name']) }}</td>
						<td class="order">{{ e(isset($arr['regiao_nome']) && $arr['regiao_nome'] !== '' ? (string) $arr['regiao_nome'] : __('All regions')) }}</td>
						<td class="order">{{ e((string) $arr['nome']) }}</td>
						<td class="order"><span class="***REMOVED***-type-pill {{ (int) $arr['especie'] === 1 ? '***REMOVED***-type-pill--production' : '***REMOVED***-type-pill--financial' }}">{{ e((string) $metaTipos[$arr['especie']]) }}</span></td>
						<td class="order">{{ $metaValor }}</td>
						<td class="order">
							<div class="***REMOVED***-table-actions">
								<a href="{{ route('metas.edit', ['id' => (int) $arr['meta_id'], 'startBanco' => $startBanco, 'startDate' => $startDate, 'mes' => $mes, 'ano' => $ano]) }}" class="***REMOVED***-link-button">{{ __('Edit') }}</a>
								<a href="{{ route('metas.confirm-delete', ['id' => (int) $arr['meta_id'], 'startBanco' => $startBanco, 'startDate' => $startDate, 'mes' => $mes, 'ano' => $ano]) }}" class="***REMOVED***-link-button ***REMOVED***-link-button--danger">{{ __('Delete') }}</a>
							</div>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="6" class="order metas-empty">{{ __('No goals found for this context.') }}</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>
