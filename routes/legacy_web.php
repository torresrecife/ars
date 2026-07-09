<?php

declare(strict_types=1);

return array(
	'/metas' => array(
		'controller' => \App\Http\Controllers\MetaController::class,
		'action' => 'index',
	),
	'/metas/ajax' => array(
		'controller' => \App\Http\Controllers\MetaController::class,
		'action' => 'ajax',
	),
	'/semanas' => array(
		'controller' => \App\Http\Controllers\WeekController::class,
		'action' => 'index',
	),
	'/semanas/ajax' => array(
		'controller' => \App\Http\Controllers\WeekController::class,
		'action' => 'ajax',
	),
	'/dados-fatur' => array(
		'controller' => \App\Http\Controllers\FinancialDetailController::class,
		'action' => 'index',
	),
	'/dados-anda' => array(
		'controller' => \App\Http\Controllers\AndamentoDetailController::class,
		'action' => 'index',
	),
	'/dashboard/panel' => array(
		'controller' => \App\Http\Controllers\DashboardPanelController::class,
		'action' => 'index',
	),
);
