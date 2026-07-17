<?php

declare(strict_types=1);

namespace App\Services;

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
				return app(DashboardPanelController::class)->index($input, $session);
			case 'general-production-weekly':
				return app(GeneralProductionController::class)->weekly($input, $session);
			case 'general-production-monthly':
				return app(GeneralProductionController::class)->monthly($input, $session);
			case 'user-***REMOVED***':
				return app(UserAdminController::class)->index();
			case 'sector-***REMOVED***':
				return app(SectorAdminController::class)->index();
			case 'client-***REMOVED***':
				return app(ClientAdminController::class)->index();
			case 'andamento-***REMOVED***':
				return app(AndamentoAdminController::class)->index();
			case 'meta-***REMOVED***':
				return app(MetaController::class)->index($input, $session);
			case 'week-***REMOVED***':
				return app(WeekController::class)->index();
			case 'region-***REMOVED***':
				return app(RegionAdminController::class)->index();
			default:
				return '';
		}
	}
}
