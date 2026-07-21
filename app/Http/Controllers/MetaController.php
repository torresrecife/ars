<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\MetaStoreRequest;
use App\Http\Requests\MetaUpdateRequest;
use App\Services\MetaService;
use App\Support\View;

class MetaController extends Controller
{
	/** @var MetaService */
	private $metaService;

	/** @var View */
	private $view;

	public function __construct(MetaService $metaService, View $view)
	{
		$this->metaService = $metaService;
		$this->view = $view;
	}

	public function index(array $input = array(), array $session = array())
	{
		$startDate = isset($input['startDate']) ? $input['startDate'] : date('M');
		$startBanco = isset($input['startBanco']) ? $input['startBanco'] : (isset($input['banco_id']) ? $input['banco_id'] : '');
		$mes = isset($input['mes']) ? $input['mes'] : (isset($input['meta_mes']) ? $input['meta_mes'] : date('m'));
		$ano = isset($input['ano']) ? $input['ano'] : (isset($input['meta_ano']) ? $input['meta_ano'] : date('Y'));

		$bank = $this->metaService->getBank($startBanco);
		$metas = $this->metaService->listByBankMonthYear($startBanco, $mes, $ano);
		$andamentos = $this->metaService->listAndamentos();
		$regionSelection = $this->metaService->regionSelectionData($session);

		return $this->view->render('metas/index', array(
			'startDate' => $startDate,
			'startBanco' => $startBanco,
			'mes' => $mes,
			'ano' => $ano,
			'bank' => $bank,
			'metas' => $metas,
			'andamentos' => $andamentos,
			'regions' => $regionSelection['regions'],
			'allowGlobalRegion' => $regionSelection['allowGlobal'],
			'totalFinanceiro' => $this->metaService->totalFinancialMeta($metas),
			'lin' => count($metas),
			'metaTipos' => array(1 => 'Produção', 2 => 'Financeira'),
		));
	}

	public function show($id)
	{
		$row = $this->metaService->findById((int) $id);
		if (!$row) {
			return $this->apiJsonResponse(false, 'not_found', 'Meta nao encontrada.', array(), 404);
		}

		return $this->apiJsonResponse(true, 'loaded', 'Meta carregada.', $row);
	}

	public function store(MetaStoreRequest $request)
	{
		return $this->mapWriteResultToJson($this->metaService->createManyFromRequest($request->all()), 'Meta criada com sucesso.');
	}

	public function update(MetaUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['meta_id'] = (int) $id;

		return $this->mapWriteResultToJson($this->metaService->updateManyFromRequest($input), 'Meta atualizada com sucesso.');
	}

	public function destroy($id)
	{
		return $this->metaService->delete((int) $id)
			? $this->apiJsonResponse(true, 'success', 'Meta excluida com sucesso.')
			: $this->apiJsonResponse(false, 'error', 'Falha na operacao.', array(), 500);
	}
}
