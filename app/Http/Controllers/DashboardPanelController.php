<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class DashboardPanelController
{
	/** @var mysqli */
	private $mysqlConnection;

	/** @var mixed */
	private $sqlsrvConnection;

	/** @var array */
	private $months;

	public function __construct($mysqlConnection, $sqlsrvConnection, array $months)
	{
		$this->mysqlConnection = $mysqlConnection;
		$this->sqlsrvConnection = $sqlsrvConnection;
		$this->months = $months;
	}

	public function index(array $input = array())
	{
		$conexao4 = $this->mysqlConnection;
		$conexao1 = $this->sqlsrvConnection;
		$arrMonths = $this->months;
		$_POST = $input;

		ob_start();
		include base_path('views/dashboard/panel.php');
		return (string) ob_get_clean();
	}
}
