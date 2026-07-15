<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesLegacyFormRequest;
use App\Http\Requests\MetaStoreRequest;
use App\Http\Requests\MetaUpdateRequest;
use App\Services\MetaService;
use App\Support\View;
use Illuminate\Http\Request;

class MetaController extends Controller
{
	use ValidatesLegacyFormRequest;

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

	public function ajax(array $input = array())
	{
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';

		if ($flag === 'E') {
			$row = $this->metaService->findById(isset($input['meta_id']) ? (int) $input['meta_id'] : 0);
			if (!$row) {
				return '';
			}

			$ordered = array(
				isset($row['meta_id']) ? $row['meta_id'] : '',
				isset($row['banco_id']) ? $row['banco_id'] : '',
				isset($row['meta_mes']) ? $row['meta_mes'] : '',
				isset($row['meta_ano']) ? $row['meta_ano'] : '',
				isset($row['anda_id']) ? $row['anda_id'] : '',
				isset($row['meta_valor']) ? $row['meta_valor'] : '',
				isset($row['def_sem']) ? $row['def_sem'] : '',
				isset($row['sem_1']) ? $row['sem_1'] : '',
				isset($row['sem_2']) ? $row['sem_2'] : '',
				isset($row['sem_3']) ? $row['sem_3'] : '',
				isset($row['sem_4']) ? $row['sem_4'] : '',
				isset($row['sem_5']) ? $row['sem_5'] : '',
				isset($row['regiao_id']) ? $row['regiao_id'] : '',
			);

			return implode('-|-', $ordered) . '-|-';
		}

		if ($flag === 'I') {
			return $this->metaService->createManyFromRequest($input);
		}

		if ($flag === 'U') {
			return $this->metaService->updateManyFromRequest($input);
		}

		if ($flag === 'D') {
			return $this->metaService->delete(isset($input['meta_id']) ? (int) $input['meta_id'] : 0) ? '1' : '0';
		}

		return '0';
	}

	public function webIndex(Request $request)
	{
		return response($this->index($request->all(), $request->session()->all()));
	}

	public function webAjax(Request $request)
	{
		$input = $request->all();
		$flag = isset($input['flag']) ? (string) $input['flag'] : '';
		$jsonMode = (string) $request->input('response_format', '') === 'json';

		if ($flag === 'I' && !$this->validateLegacyFormRequest($request, MetaStoreRequest::class)) {
			return $jsonMode ? $this->apiJsonResponse(false, 'validation_error', 'Dados invalidos.', array(), 422) : $this->legacyTextResponse('0');
		}
		if ($flag === 'U' && !$this->validateLegacyFormRequest($request, MetaUpdateRequest::class)) {
			return $jsonMode ? $this->apiJsonResponse(false, 'validation_error', 'Dados invalidos.', array(), 422) : $this->legacyTextResponse('0');
		}

		if ($jsonMode) {
			return $this->webAjaxJson($input, $flag);
		}

		if ($flag === 'E') {
			return $this->legacyHtmlResponse($this->ajax($input));
		}

		return $this->legacyTextResponse($this->ajax($input));
	}

	private function webAjaxJson(array $input, $flag)
	{
		if ($flag === 'E') {
			$row = $this->metaService->findById(isset($input['meta_id']) ? (int) $input['meta_id'] : 0);
			if (!$row) {
				return $this->apiJsonResponse(false, 'not_found', 'Meta nao encontrada.', array(), 404);
			}

			return $this->apiJsonResponse(true, 'loaded', 'Meta carregada.', $row);
		}

		if ($flag === 'I') {
			return $this->mapWriteResultToJson($this->metaService->createManyFromRequest($input), 'Meta criada com sucesso.');
		}

		if ($flag === 'U') {
			return $this->mapWriteResultToJson($this->metaService->updateManyFromRequest($input), 'Meta atualizada com sucesso.');
		}

		if ($flag === 'D') {
			return $this->metaService->delete(isset($input['meta_id']) ? (int) $input['meta_id'] : 0)
				? $this->apiJsonResponse(true, 'success', 'Meta excluida com sucesso.')
				: $this->apiJsonResponse(false, 'error', 'Falha na operacao.', array(), 500);
		}

		return $this->apiJsonResponse(false, 'invalid_flag', 'Operacao invalida.', array(), 400);
	}

	private function mapWriteResultToJson($result, $successMessage)
	{
		if ((string) $result === '1') {
			return $this->apiJsonResponse(true, 'success', $successMessage);
		}
		if ((string) $result === '2') {
			return $this->apiJsonResponse(false, 'duplicate', 'Registro duplicado.', array(), 409);
		}

		return $this->apiJsonResponse(false, 'error', 'Falha na operacao.', array(), 500);
	}
}
