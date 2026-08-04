<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\DashboardPanelInput;
use App\Data\GeneralProductionInput;
use App\Http\Controllers\AndamentoAdminController;
use App\Http\Controllers\ClientAdminController;
use App\Http\Controllers\DashboardPanelController;
use App\Http\Controllers\GeneralProductionController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\RegionAdminController;
use App\Http\Controllers\SectorAdminController;
use App\Http\Controllers\UserAdminController;
use App\Http\Controllers\WeekController;
use App\Support\View;
use App\ViewModels\MainPageContent;

class MainPageContentRenderer
{
	/** @var View */
	private $view;

	public function __construct(View $view)
	{
		$this->view = $view;
	}

	public function render(MainPageContent $content, array $input, array $session)
	{
		if ($content->type() === 'view') {
			return $this->view->render($content->view(), $content->data());
		}

		if ($content->type() !== 'controller') {
			return '';
		}

		return $this->renderControllerContent($content->controller(), $input, $session);
	}

	private function renderControllerContent($controllerName, array $input, array $session)
	{
		switch ($controllerName) {
			case 'dashboard-panel':
				return app(DashboardPanelController::class)->index(DashboardPanelInput::fromArray($input), $session);
			case 'general-production-weekly':
				return app(GeneralProductionController::class)->weekly(GeneralProductionInput::fromArray($input), $session);
			case 'general-production-monthly':
				return app(GeneralProductionController::class)->monthly(GeneralProductionInput::fromArray($input), $session);
			case 'user-admin':
				return app(UserAdminController::class)->index();
			case 'sector-admin':
				return app(SectorAdminController::class)->index();
			case 'client-admin':
				return app(ClientAdminController::class)->index();
			case 'andamento-admin':
				return app(AndamentoAdminController::class)->index();
			case 'meta-admin':
				return app(MetaController::class)->index($input, $session);
			case 'week-admin':
				return app(WeekController::class)->index();
			case 'region-admin':
				return app(RegionAdminController::class)->index();
			default:
				return '';
		}
	}
}
