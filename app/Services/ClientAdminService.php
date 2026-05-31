<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ClientAdminRepository;

class ClientAdminService
{
	/** @var ClientAdminRepository */
	private $repository;

	/** @var string */
	private $debugLog;

	public function __construct(ClientAdminRepository $repository)
	{
		$this->repository = $repository;
		$this->debugLog = base_path('storage/logs/clientes-debug.log');
	}

	public function indexData()
	{
		$clients = $this->repository->all();
		$dadosMap = $this->buildDadosMap($this->repository->listDadosByBanco());

		foreach ($clients as $index => $client) {
			$clients[$index]['dados_html'] = isset($dadosMap[$client['banco_id']]) ? implode('<br>', $dadosMap[$client['banco_id']]) : '';
		}

		return array(
			'clients' => $clients,
			'areas' => $this->repository->listAreas(),
			'carteiras' => $this->repository->listCarteiras(),
			'statusLabels' => array('Y' => 'Ativo', 'N' => 'Inativo', 'P' => 'Pendente'),
		);
	}

	public function editPayload($id)
	{
		$row = $this->repository->findById($id);
		if (!$row) {
			return '';
		}

		$dadosRows = $this->repository->listDadosByBancoId($id);
		$dadosCodes = array();
		foreach ($dadosRows as $dadosRow) {
			$dadosCodes[] = $dadosRow['dados_cod'];
		}

		return implode('-|-', array_values($row)) . '-|-' . implode('|||', $dadosCodes) . '-|-';
	}

	public function create(array $input)
	{
		$payload = $this->normalizePayload($input);
		if ($payload === false) {
			return '0';
		}

		if (!$this->repository->insertBank($payload)) {
			return '0';
		}

		$bancoId = $this->repository->getLastInsertId();
		if ($bancoId <= 0) {
			return '0';
		}

		if (!$this->repository->createCarteira($bancoId, $payload['banco_creator'])) {
			return '0';
		}

		$carteiraId = $this->repository->findCarteiraIdByBanco($bancoId);
		if ($carteiraId <= 0) {
			return '0';
		}

		foreach ($this->extractDadosCodes($input) as $dadosCod) {
			if (!$this->repository->insertDados($carteiraId, $bancoId, $dadosCod, $payload['banco_creator'])) {
				return '0';
			}
		}

		return '1';
	}

	public function update(array $input)
	{
		$payload = $this->normalizePayload($input);
		$this->debug('update.input', array(
			'banco_id' => isset($input['banco_id']) ? $input['banco_id'] : null,
			'cartei_num' => isset($input['cartei_num']) ? $input['cartei_num'] : null,
			'dados_json' => isset($input['dados_json']) ? $input['dados_json'] : null,
			'keys' => array_keys($input),
		));
		if ($payload === false || $payload['banco_id'] <= 0) {
			$this->debug('update.invalid_payload', $input);
			return '0';
		}

		if (!$this->repository->updateBank($payload)) {
			$this->debug('update.updateBank_failed', $payload);
			return '0';
		}

		if (!$this->repository->deleteDadosByBanco($payload['banco_id'])) {
			$this->debug('update.deleteDados_failed', $payload['banco_id']);
			return '0';
		}

		$carteiraId = $this->repository->findCarteiraIdByBanco($payload['banco_id']);
		if ($carteiraId <= 0) {
			$this->debug('update.carteira_not_found', $payload['banco_id']);
			return '0';
		}

		$dadosCodes = $this->extractDadosCodes($input);
		$this->debug('update.codes', $dadosCodes);
		foreach ($dadosCodes as $dadosCod) {
			if (!$this->repository->insertDados($carteiraId, $payload['banco_id'], $dadosCod, $payload['banco_creator'])) {
				$this->debug('update.insert_failed', array(
					'banco_id' => $payload['banco_id'],
					'carteira_id' => $carteiraId,
					'dados_cod' => $dadosCod,
				));
				return '0';
			}
		}

		$this->debug('update.success', array(
			'banco_id' => $payload['banco_id'],
			'codes_count' => count($dadosCodes),
		));

		return '1';
	}

	public function delete($id)
	{
		return $this->repository->deleteBankCascade($id) ? '1' : '0';
	}

	private function normalizePayload(array $input)
	{
		$name = isset($input['banco_name']) ? trim((string) $input['banco_name']) : '';
		$cod = isset($input['banco_cod']) ? trim((string) $input['banco_cod']) : '';
		if ($name === '' || $cod === '') {
			return false;
		}

		return array(
			'banco_id' => isset($input['banco_id']) ? (int) $input['banco_id'] : 0,
			'banco_name' => $name,
			'banco_cod' => $cod,
			'banco_creator' => date('Y-m-d H:i:s'),
			'banco_area' => isset($input['banco_area']) ? (int) $input['banco_area'] : 0,
			'banco_status' => isset($input['banco_status']) ? (string) $input['banco_status'] : '',
			'banco_class' => isset($input['banco_class']) ? trim((string) $input['banco_class']) : '',
			'simulador' => isset($input['simulador']) ? (int) $input['simulador'] : 0,
			'banco_curto' => isset($input['banco_curto']) ? trim((string) $input['banco_curto']) : '',
		);
	}

	private function extractDadosCodes(array $input)
	{
		if (isset($input['dados_json']) && (string) $input['dados_json'] !== '') {
			$decoded = json_decode((string) $input['dados_json'], true);
			if (is_array($decoded)) {
				$codes = array();
				foreach ($decoded as $value) {
					$value = trim((string) $value);
					if ($value === '') {
						continue;
					}
					if (!in_array($value, $codes, true)) {
						$codes[] = $value;
					}
				}

				return $codes;
			}
		}

		$total = isset($input['cartei_num']) ? (int) $input['cartei_num'] : 0;
		$codes = array();

		for ($index = 1; $index <= $total; $index++) {
			$key = 'dados_name_' . $index;
			if (!isset($input[$key])) {
				continue;
			}

			$value = trim((string) $input[$key]);
			if ($value === '') {
				continue;
			}

			if (!in_array($value, $codes, true)) {
				$codes[] = $value;
			}
		}

		return $codes;
	}

	private function buildDadosMap(array $rows)
	{
		$map = array();
		foreach ($rows as $row) {
			$bancoId = isset($row['banco_id']) ? $row['banco_id'] : 0;
			if (!isset($map[$bancoId])) {
				$map[$bancoId] = array();
			}
			$map[$bancoId][] = $row['dados_cod'];
		}

		return $map;
	}

	private function debug($label, $data)
	{
		$line = date('Y-m-d H:i:s') . ' [' . $label . '] ' . json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL;
		@file_put_contents($this->debugLog, $line, FILE_APPEND);
	}
}
