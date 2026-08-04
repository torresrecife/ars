<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\MetaStoreRequest;
use App\Http\Requests\MetaUpdateRequest;
use App\Services\AuthService;
use App\Services\MainPageService;
use App\Services\MetaService;
use App\Support\MonthMap;
use App\Support\View;
use Illuminate\Http\Request;

class MetaController extends Controller
{
	/** @var MetaService */
	private $metaService;

	/** @var View */
	private $view;

	/** @var MainPageService */
	private $mainPageService;

	/** @var AuthService */
	private $authService;

	public function __construct(MetaService $metaService, View $view, MainPageService $mainPageService, AuthService $authService)
	{
		$this->metaService = $metaService;
		$this->view = $view;
		$this->mainPageService = $mainPageService;
		$this->authService = $authService;
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
			'metaTipos' => array(1 => __('Production'), 2 => __('Financial')),
		));
	}

	public function show($id)
	{
		$row = $this->metaService->findById((int) $id);
		if (!$row) {
			return $this->apiJsonResponse(false, 'not_found', __('Goal not found.'), array(), 404);
		}

		return $this->apiJsonResponse(true, 'loaded', __('Goal loaded.'), $row);
	}

	public function createPage(Request $request)
	{
		$context = $this->metaService->resolveContext($request->all());
		if ((int) $context['startBanco'] <= 0) {
			return redirect()->route('metas')->with('error', __('Select the client and month/year first.'));
		}

		return $this->renderShellPage($request, 'metas/form', array_merge(
			$this->metaService->formData($context),
			array(
				'pageTitle' => __('New Goal'),
				'submitLabel' => __('Save'),
				'backUrl' => url('metas?' . http_build_query(array(
					'startBanco' => $context['startBanco'],
					'startDate' => $context['startDate'],
					'mes' => $context['mes'],
					'ano' => $context['ano'],
				))),
				'formAction' => route('metas.store.page'),
				'formMethod' => 'POST',
				'isEditMode' => false,
			)
		));
	}

	public function editPage(Request $request, $id)
	{
		$payload = $this->metaService->findById((int) $id);
		if (!$payload) {
			abort(404, __('Goal not found.'));
		}

		$context = $this->metaService->resolveContext(array_merge($request->all(), array(
			'startBanco' => $payload['banco_id'],
			'banco_id' => $payload['banco_id'],
			'mes' => $payload['meta_mes'],
			'meta_mes' => $payload['meta_mes'],
			'ano' => $payload['meta_ano'],
			'meta_ano' => $payload['meta_ano'],
		)));

		return $this->renderShellPage($request, 'metas/form', array_merge(
			$this->metaService->formData($context, $payload),
			array(
				'pageTitle' => __('Edit Goal'),
				'submitLabel' => __('Save'),
				'backUrl' => url('metas?' . http_build_query(array(
					'startBanco' => $context['startBanco'],
					'startDate' => $context['startDate'],
					'mes' => $context['mes'],
					'ano' => $context['ano'],
				))),
				'formAction' => route('metas.update.page', (int) $id),
				'formMethod' => 'PATCH',
				'isEditMode' => true,
			)
		));
	}

	public function store(MetaStoreRequest $request)
	{
		return $this->mapWriteResultToJson($this->metaService->createManyFromRequest($request->all()), __('Goal(s) created successfully.'));
	}

	public function storePage(MetaStoreRequest $request)
	{
		$result = $this->metaService->createManyFromRequest($request->all());
		if ($result->isDuplicate()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Duplicate record.'),
			));
		}

		if (!$result->isSuccess()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Operation failed.'),
			));
		}

		return redirect()->to(url('metas?' . http_build_query(array(
			'startBanco' => $request->input('banco_id'),
			'startDate' => $this->monthYearLabel((int) $request->input('meta_mes'), (int) $request->input('meta_ano')),
			'mes' => $request->input('meta_mes'),
			'ano' => $request->input('meta_ano'),
		))))->with('status', __('Goal(s) created successfully.'));
	}

	public function update(MetaUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['meta_id'] = (int) $id;

		return $this->mapWriteResultToJson($this->metaService->updateManyFromRequest($input), __('Goal edited successfully.'));
	}

	public function updatePage(MetaUpdateRequest $request, $id)
	{
		$input = $request->all();
		$input['meta_id'] = (int) $id;
		$result = $this->metaService->updateManyFromRequest($input);
		if ($result->isDuplicate()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Duplicate record.'),
			));
		}

		if (!$result->isSuccess()) {
			return redirect()->back()->withInput()->withErrors(array(
				'form' => __('Operation failed.'),
			));
		}

		return redirect()->to(url('metas?' . http_build_query(array(
			'startBanco' => $request->input('banco_id'),
			'startDate' => $this->monthYearLabel((int) $request->input('meta_mes'), (int) $request->input('meta_ano')),
			'mes' => $request->input('meta_mes'),
			'ano' => $request->input('meta_ano'),
		))))->with('status', __('Goal edited successfully.'));
	}

	public function destroy($id)
	{
		return $this->mapWriteResultToJson($this->metaService->delete((int) $id), __('Goal deleted successfully.'));
	}

	public function destroyPage(Request $request, $id)
	{
		$result = $this->metaService->delete((int) $id);

		return redirect()->to(url('metas?' . http_build_query(array(
			'startBanco' => $request->query('startBanco', $request->input('banco_id', '')),
			'startDate' => $request->query('startDate', ''),
			'mes' => $request->query('mes', $request->input('meta_mes', '')),
			'ano' => $request->query('ano', $request->input('meta_ano', '')),
		))))->with(
			$result->isSuccess() ? 'status' : 'error',
			$result->isSuccess() ? __('Goal deleted successfully.') : __('Operation failed.')
		);
	}

	public function reorder(Request $request)
	{
		$result = $this->metaService->reorderByContext($request->all());

		return $this->mapWriteResultToJson($result, __('Goal order updated successfully.'));
	}

	public function confirmDeletePage(Request $request, $id)
	{
		$payload = $this->metaService->findById((int) $id);
		if (!$payload) {
			abort(404, __('Goal not found.'));
		}

		$backQuery = array(
			'startBanco' => $request->query('startBanco', $payload['banco_id']),
			'startDate' => $request->query('startDate', $this->monthYearLabel((int) $payload['meta_mes'], (int) $payload['meta_ano'])),
			'mes' => $request->query('mes', $payload['meta_mes']),
			'ano' => $request->query('ano', $payload['meta_ano']),
		);

		return $this->renderShellPage($request, 'shared/confirm-delete', array(
			'pageTitle' => __('Delete Goal'),
			'message' => __('Review the selected goal before confirming permanent deletion.'),
			'itemName' => (string) $payload['nome'],
			'formAction' => route('metas.destroy.page', (int) $id) . '?' . http_build_query($backQuery),
			'backUrl' => url('metas?' . http_build_query($backQuery)),
		));
	}

	private function renderShellPage(Request $request, $viewName, array $viewData)
	{
		$user = $this->authService->currentUser();
		if (empty($user)) {
			throw new \RuntimeException(__('User session not found.'));
		}

		$this->authService->syncSessionContext($user);
		$pageData = $this->mainPageService->build(array(
			'section' => 'metas-admin',
			'startDate' => isset($viewData['context']['startDate']) ? $viewData['context']['startDate'] : '',
			'mes' => isset($viewData['context']['mes']) ? $viewData['context']['mes'] : date('m'),
			'ano' => isset($viewData['context']['ano']) ? $viewData['context']['ano'] : date('Y'),
			'bank_id' => isset($viewData['context']['startBanco']) ? $viewData['context']['startBanco'] : '',
		), $request->session()->all());
		$exportPath = dirname(dirname(dirname(__DIR__))) . '/php2/exportar.php';

		return response($this->view->render('index/shell', array(
			'pageData' => $pageData,
			'contentHtml' => $this->view->render($viewName, $viewData),
			'exportPath' => $exportPath,
			'entryUrl' => url('index'),
		)));
	}

	private function monthYearLabel($month, $year)
	{
		$months = MonthMap::localized();
		$month = (int) $month;
		$year = (int) $year;

		return (isset($months[$month]) ? $months[$month] : $month) . ' / ' . $year;
	}
}
