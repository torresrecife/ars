<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\MainPageRepository;
use App\ViewModels\MainPageContent;
use App\ViewModels\MainPageState;
use App\ViewModels\MainPageTopAction;
use App\ViewModels\MainPageUserContext;
use App\ViewModels\MainPageViewData;

class MainPageService
{
	/** @var MainPageRepository */
	private $repository;

	/** @var RegionService */
	private $regionService;

	/** @var array */
	private $months;

	public function __construct(MainPageRepository $repository, RegionService $regionService, array $months)
	{
		$this->repository = $repository;
		$this->regionService = $regionService;
		$this->months = $months;
	}

	public function build(array $input, array $session)
	{
		$userContext = $this->buildUserContext($session);
		$state = $this->resolveState($input);
		$monthYearLabel = $state->startDate();

		return new MainPageViewData(
			$userContext,
			$state,
			$monthYearLabel,
			$this->resolveContent($state, $userContext, $monthYearLabel),
			$this->resolveTopAction($state->section())
		);
	}

	private function buildUserContext(array $session)
	{
		$userId = isset($session['usuarioID']) ? (int) $session['usuarioID'] : 0;
		$regionMode = isset($session['usuarioRegiaoModo']) ? (string) $session['usuarioRegiaoModo'] : 'N';
		$regions = $userId > 0 ? $this->regionService->listUserRegions($userId) : array();

		return new MainPageUserContext(
			isset($session['usuarioSetor']) ? $session['usuarioSetor'] : 0,
			isset($session['usuarioCliente']) ? $session['usuarioCliente'] : '',
			$regionMode,
			isset($session['usuarioRegiaoIds']) ? $session['usuarioRegiaoIds'] : '',
			isset($session['usuarioRegiaoUfs']) ? $session['usuarioRegiaoUfs'] : '',
			isset($session['usuarioNivel']) ? $session['usuarioNivel'] : '',
			$userId,
			$regions,
			$this->shouldShowRegionSelector(
				isset($session['usuarioNivel']) ? (string) $session['usuarioNivel'] : '',
				$regionMode,
				$regions
			)
		);
	}

	private function resolveState(array $input)
	{
		$section = isset($input['section']) ? (string) $input['section'] : '';
		if ($section === '') {
			$section = (isset($input['geral']) && (int) $input['geral'] === 1) ? 'relatorio-semanal' : 'inicio';
		}

		$month = isset($input['mes']) ? (int) $input['mes'] : (int) date('m');
		if ($month <= 0 || $month > 12) {
			$month = (int) date('m');
		}

		$year = isset($input['ano']) ? (int) $input['ano'] : (int) date('Y');
		if ($year <= 0) {
			$year = (int) date('Y');
		}

		$startDate = isset($input['startDate']) && trim((string) $input['startDate']) !== ''
			? (string) $input['startDate']
			: $this->months[$month] . ' / ' . $year;

		return new MainPageState(
			$section,
			isset($input['area_id']) ? $input['area_id'] : '',
			isset($input['bank_id']) ? $input['bank_id'] : '',
			isset($input['geral']) ? $input['geral'] : 0,
			isset($input['regiao_id']) ? $input['regiao_id'] : 0,
			$month,
			$year,
			$startDate,
			isset($input['startSetor']) ? $input['startSetor'] : ''
		);
	}

	private function resolveTopAction($currentSection)
	{
		$actions = array(
			'usuarios' => new MainPageTopAction('newuser', __('New User'), '', route('usuarios.create')),
			'setores' => new MainPageTopAction('newsetor', __('New Sector'), '', route('setores.create')),
			'clientes' => new MainPageTopAction('newsetor', __('New Client'), '', route('clientes.create')),
			'andamentos' => new MainPageTopAction('newsetor', __('New Progress'), '', route('andamentos.create')),
			'semanas' => new MainPageTopAction('newsetor', __('New Week'), '', route('semanas.create')),
			'regioes' => new MainPageTopAction('newsetor', __('New Region'), '', route('regioes.create')),
		);

		return isset($actions[$currentSection]) ? $actions[$currentSection] : null;
	}

	private function resolveContent(MainPageState $state, MainPageUserContext $user, $monthYearLabel)
	{
		switch ($state->section()) {
			case 'carteiras':
				return MainPageContent::forView('index/carteira', array(
					'banks' => $this->repository->listBanksByArea($state->areaId(), $user->clientIds()),
					'hidArea' => $state->areaId(),
					'monthYearLabel' => $monthYearLabel,
					'month' => $state->mes(),
					'year' => $state->ano(),
					'regions' => $user->regions(),
					'showRegionSelector' => $user->showRegionSelector(),
					'selectedRegionId' => $state->regiaoId(),
				));
			case 'painel':
				return MainPageContent::forController('dashboard-panel', true);
			case 'producao':
				return MainPageContent::forView('index/producao', array(
					'areas' => $this->repository->listAreasForProduction($user->level(), $user->sectorId()),
					'monthYearLabel' => $monthYearLabel,
					'month' => $state->mes(),
					'year' => $state->ano(),
					'startSector' => $state->startSetor(),
					'regions' => $user->regions(),
					'showRegionSelector' => $user->showRegionSelector(),
					'selectedRegionId' => $state->regiaoId(),
					'userLevel' => $user->level(),
				));
			case 'relatorio-semanal':
				return MainPageContent::forController('general-production-weekly', true);
			case 'relatorio-mensal':
				return MainPageContent::forController('general-production-monthly', true);
			case 'admin':
				return MainPageContent::forView('admin/index', array(
					'userLevel' => $user->level(),
					'hidArea' => $state->areaId(),
					'banks' => $this->repository->listAdminBanks($user->sectorId(), $user->clientIds()),
				));
			case 'usuarios':
				return MainPageContent::forController('user-admin');
			case 'setores':
				return MainPageContent::forController('sector-admin');
			case 'clientes':
				return MainPageContent::forController('client-admin');
			case 'andamentos':
				return MainPageContent::forController('andamento-admin');
			case 'metas-select':
				return MainPageContent::forView('index/metas-select', array(
					'banks' => $this->repository->listBanksForMetas($user->sectorId(), $user->clientIds()),
					'monthYearLabel' => $monthYearLabel,
					'month' => $state->mes(),
					'year' => $state->ano(),
				));
			case 'metas-admin':
				return MainPageContent::forController('meta-admin');
			case 'semanas':
				return MainPageContent::forController('week-admin');
			case 'regioes':
				return MainPageContent::forController('region-admin');
			case 'inicio':
			default:
				return MainPageContent::forView('index/default', array(
					'areas' => $this->repository->listAreas($user->sectorId()),
				));
		}
	}

	private function shouldShowRegionSelector($level, $regionMode, array $regions)
	{
		if ((string) $level !== 'GER') {
			return false;
		}

		if (empty($regions)) {
			return false;
		}

		return in_array((string) $regionMode, array('R', 'T'), true);
	}
}
