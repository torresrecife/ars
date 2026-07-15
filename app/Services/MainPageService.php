<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\MainPageRepository;

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
		$monthYearLabel = $this->months[(int) date('m')] . ' / ' . date('Y');

		return array(
			'user' => $userContext,
			'state' => $state,
			'monthYearLabel' => $monthYearLabel,
			'topAction' => $this->resolveTopAction($state['hid_send']),
			'canAdmin' => in_array($userContext['level'], array('ADM', 'GER'), true),
			'content' => $this->resolveContent($state, $userContext, $monthYearLabel),
		);
	}

	private function buildUserContext(array $session)
	{
		$userId = isset($session['usuarioID']) ? (int) $session['usuarioID'] : 0;
		$regionMode = isset($session['usuarioRegiaoModo']) ? (string) $session['usuarioRegiaoModo'] : 'N';
		$regions = $userId > 0 ? $this->regionService->listUserRegions($userId) : array();

		return array(
			'sectorId' => isset($session['usuarioSetor']) ? (int) $session['usuarioSetor'] : 0,
			'clientIds' => isset($session['usuarioCliente']) ? (string) $session['usuarioCliente'] : '',
			'regionMode' => $regionMode,
			'regionIds' => isset($session['usuarioRegiaoIds']) ? (string) $session['usuarioRegiaoIds'] : '',
			'regionUfs' => isset($session['usuarioRegiaoUfs']) ? (string) $session['usuarioRegiaoUfs'] : '',
			'level' => isset($session['usuarioNivel']) ? (string) $session['usuarioNivel'] : '',
			'id' => $userId,
			'regions' => $regions,
			'showRegionSelector' => $this->shouldShowRegionSelector(
				isset($session['usuarioNivel']) ? (string) $session['usuarioNivel'] : '',
				$regionMode,
				$regions
			),
		);
	}

	private function resolveState(array $input)
	{
		$areaId = isset($input['area_id']) ? (string) $input['area_id'] : (isset($input['hid_area']) ? (string) $input['hid_area'] : '');
		$bankId = isset($input['bank_id']) ? (string) $input['bank_id'] : (isset($input['hid_flag']) ? (string) $input['hid_flag'] : '');

		return array(
			'hid_send' => isset($input['hid_send']) ? (int) $input['hid_send'] : 0,
			'hid_area' => $areaId,
			'hid_flag' => $bankId,
			'area_id' => $areaId,
			'bank_id' => $bankId,
			'geral' => isset($input['geral']) ? (int) $input['geral'] : 0,
			'regiao_id' => isset($input['regiao_id']) ? (int) $input['regiao_id'] : 0,
		);
	}

	private function resolveTopAction($hidSend)
	{
		$actions = array(
			8 => array('class' => 'newuser', 'label' => 'Novo Usuário', 'js' => 'fc_edit_usu("", "I");'),
			9 => array('class' => 'newsetor', 'label' => 'Novo Setor', 'js' => 'fc_edit_setor("", "I");'),
			11 => array('class' => 'newsetor', 'label' => 'Novo Cliente', 'js' => 'fc_edit_cliente("", "I");'),
			12 => array('class' => 'newsetor', 'label' => 'Novo Andamento', 'js' => 'fc_edit_andamento("", "I");'),
			14 => array('class' => 'newsetor', 'label' => 'Nova Meta', 'js' => 'fc_edit_metas("", "I");'),
			15 => array('class' => 'newsetor', 'label' => 'Nova Semana', 'js' => 'fc_edit_sem("", "I");'),
			16 => array('class' => 'newsetor', 'label' => 'Nova Região', 'js' => 'fc_edit_regiao("", "I");'),
		);

		return isset($actions[$hidSend]) ? $actions[$hidSend] : null;
	}

	private function resolveContent(array $state, array $user, $monthYearLabel)
	{
		switch ($state['hid_send']) {
			case 1:
				return array(
					'type' => 'view',
						'view' => 'index/carteira',
						'data' => array(
						'banks' => $this->repository->listBanksByArea($state['area_id'], $user['clientIds']),
						'hidArea' => $state['area_id'],
						'monthYearLabel' => $monthYearLabel,
						'regions' => $user['regions'],
						'showRegionSelector' => $user['showRegionSelector'],
						'selectedRegionId' => $state['regiao_id'],
					),
				);
			case 2:
				return array('type' => 'controller', 'controller' => 'dashboard-panel', 'spacer' => true);
			case 3:
				return array(
					'type' => 'view',
					'view' => 'index/producao',
					'data' => array(
						'areas' => $this->repository->listAreasForProduction($user['level'], $user['sectorId']),
						'monthYearLabel' => $monthYearLabel,
						'regions' => $user['regions'],
						'showRegionSelector' => $user['showRegionSelector'],
						'selectedRegionId' => $state['regiao_id'],
						'userLevel' => $user['level'],
					),
				);
			case 4:
				return array(
					'type' => 'controller',
					'controller' => ((int) $state['geral'] === 1) ? 'general-production-weekly' : 'general-production-monthly',
					'spacer' => true
				);
			case 5:
				return array(
						'type' => 'view',
						'view' => 'admin/index',
						'data' => array(
						'userLevel' => $user['level'],
						'hidArea' => $state['area_id'],
						'banks' => $this->repository->listAdminBanks($user['sectorId'], $user['clientIds']),
					),
				);
			case 8:
				return array('type' => 'controller', 'controller' => 'user-admin');
			case 9:
				return array('type' => 'controller', 'controller' => 'sector-admin');
			case 11:
				return array('type' => 'controller', 'controller' => 'client-admin');
			case 12:
				return array('type' => 'controller', 'controller' => 'andamento-admin');
			case 13:
				return array(
					'type' => 'view',
					'view' => 'index/metas-select',
					'data' => array(
						'banks' => $this->repository->listBanksForMetas($user['sectorId'], $user['clientIds']),
						'monthYearLabel' => $monthYearLabel,
					),
				);
			case 14:
				return array('type' => 'controller', 'controller' => 'meta-admin');
			case 15:
				return array('type' => 'controller', 'controller' => 'week-admin');
			case 16:
				return array('type' => 'controller', 'controller' => 'region-admin');
			default:
				return array(
					'type' => 'view',
					'view' => 'index/default',
					'data' => array(
						'areas' => $this->repository->listAreas($user['sectorId']),
					),
				);
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
