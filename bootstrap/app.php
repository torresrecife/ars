<?php

declare(strict_types=1);

use App\Application;

$basePath = dirname(__DIR__);

require_once $basePath . '/app/Support/helpers.php';

$autoloadFiles = array(
	$basePath . '/vendor/autoload.php',
);

foreach ($autoloadFiles as $autoloadFile) {
	if (is_file($autoloadFile)) {
		require_once $autoloadFile;
		break;
	}
}

require_once $basePath . '/app/Application.php';
require_once $basePath . '/app/Support/Env.php';
require_once $basePath . '/app/Support/Config.php';
require_once $basePath . '/app/Support/LegacyConfig.php';
require_once $basePath . '/app/Support/View.php';
require_once $basePath . '/app/Infrastructure/Database/DatabaseManager.php';
require_once $basePath . '/app/Repositories/UserRepository.php';
require_once $basePath . '/app/Repositories/MetaRepository.php';
require_once $basePath . '/app/Repositories/WeekRepository.php';
require_once $basePath . '/app/Repositories/NeoDetailRepository.php';
require_once $basePath . '/app/Repositories/DashboardRepository.php';
require_once $basePath . '/app/Repositories/NeoPanelRepository.php';
require_once $basePath . '/app/Repositories/MainPageRepository.php';
require_once $basePath . '/app/Repositories/GeneralProductionRepository.php';
require_once $basePath . '/app/Repositories/GeneralProductionNeoRepository.php';
require_once $basePath . '/app/Repositories/UserAdminRepository.php';
require_once $basePath . '/app/Repositories/ClientAdminRepository.php';
require_once $basePath . '/app/Repositories/RegionRepository.php';
require_once $basePath . '/app/Repositories/RegionAdminRepository.php';
require_once $basePath . '/app/Services/AuthService.php';
require_once $basePath . '/app/Services/MetaService.php';
require_once $basePath . '/app/Services/WeekService.php';
require_once $basePath . '/app/Services/NeoDetailService.php';
require_once $basePath . '/app/Services/DashboardPanelService.php';
require_once $basePath . '/app/Services/MainPageService.php';
require_once $basePath . '/app/Services/GeneralProductionService.php';
require_once $basePath . '/app/Services/UserAdminService.php';
require_once $basePath . '/app/Services/ClientAdminService.php';
require_once $basePath . '/app/Services/RegionService.php';
require_once $basePath . '/app/Services/RegionAdminService.php';
require_once $basePath . '/app/Http/Controllers/MetaController.php';
require_once $basePath . '/app/Http/Controllers/WeekController.php';
require_once $basePath . '/app/Http/Controllers/FinancialDetailController.php';
require_once $basePath . '/app/Http/Controllers/AndamentoDetailController.php';
require_once $basePath . '/app/Http/Controllers/DashboardPanelController.php';
require_once $basePath . '/app/Http/Controllers/HomeController.php';
require_once $basePath . '/app/Http/Controllers/GeneralProductionController.php';
require_once $basePath . '/app/Http/Controllers/UserAdminController.php';
require_once $basePath . '/app/Http/Controllers/ClientAdminController.php';
require_once $basePath . '/app/Http/Controllers/RegionAdminController.php';

\App\Support\Env::load($basePath . '/.env');

return new Application($basePath);
