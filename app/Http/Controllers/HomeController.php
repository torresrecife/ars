<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\MainPageService;
use App\Support\View;

class HomeController
{
	/** @var MainPageService */
	private $service;

	/** @var View */
	private $view;

	public function __construct(MainPageService $service, View $view)
	{
		$this->service = $service;
		$this->view = $view;
	}

	public function index(array $input, array $session)
	{
		$pageData = $this->service->build($input, $session);
		$contentHtml = $this->renderContent($pageData['content'], $input);
		$exportPath = dirname(dirname(dirname(__DIR__))) . '/php2/exportar.php';

		return $this->view->render('index/shell', array(
			'pageData' => $pageData,
			'contentHtml' => $contentHtml,
			'exportPath' => $exportPath,
		));
	}

	private function renderContent(array $content, array $input)
	{
		if ($content['type'] === 'view') {
			return $this->view->render($content['view'], $content['data']);
		}

		$html = '';
		if (!empty($content['spacer'])) {
			$html .= "<br><br><br><br><br>";
		}

		return $html . $this->renderLegacyInclude($content['file'], $input);
	}

	private function renderLegacyInclude($file, array $input)
	{
		$fullPath = base_path($file);
		if (!is_file($fullPath)) {
			return '';
		}

		$originalPost = $_POST;
		$originalRequest = $_REQUEST;
		$_POST = array_merge($_POST, $input);
		$_REQUEST = array_merge($_REQUEST, $input);
		$usu_setor = isset($_SESSION['usuarioSetor']) ? $_SESSION['usuarioSetor'] : 0;
		$usu_Cliente = isset($_SESSION['usuarioCliente']) ? $_SESSION['usuarioCliente'] : 0;
		$usu_nivel = isset($_SESSION['usuarioNivel']) ? $_SESSION['usuarioNivel'] : '';
		$usu_id = isset($_SESSION['usuarioID']) ? $_SESSION['usuarioID'] : 0;
		$hid_send = isset($input['hid_send']) ? $input['hid_send'] : '';
		$hid_area = isset($input['hid_area']) ? $input['hid_area'] : '';
		$hid_flag = isset($input['hid_flag']) ? $input['hid_flag'] : '';
		$mesano = isset($GLOBALS['arrMonths']) ? $GLOBALS['arrMonths'][(int) date('m')] . ' / ' . date('Y') : date('m') . ' / ' . date('Y');

		ob_start();
		include $fullPath;
		$output = (string) ob_get_clean();

		$_POST = $originalPost;
		$_REQUEST = $originalRequest;

		return $output;
	}
}
