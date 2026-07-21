<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\RegionAdminRepository;
use App\Support\WriteResult;

class RegionAdminService
{
	/** @var RegionAdminRepository */
	private $repository;

	public function __construct(RegionAdminRepository $repository)
	{
		$this->repository = $repository;
	}

	public function indexData()
	{
		return array(
			'regions' => $this->repository->all(),
			'ufs' => $this->brUfs(),
		);
	}

	public function editPayload($id)
	{
		$row = $this->repository->findById($id);
		if (!$row) {
			return null;
		}

		$ufs = $this->repository->listUfsByRegionId((int) $row['regiao_id']);

		return array(
			'regiao_id' => (int) $row['regiao_id'],
			'regiao_nome' => (string) $row['regiao_nome'],
			'regiao_slug' => (string) $row['regiao_slug'],
			'regiao_status' => (string) $row['regiao_status'],
			'ufs' => $ufs,
		);
	}

	public function create(array $input)
	{
		$payload = $this->normalizePayload($input, false);
		if ($payload === false) {
			return WriteResult::error();
		}

		if ($this->repository->findBySlug($payload['regiao_slug'])) {
			return WriteResult::duplicate();
		}

		if (!$this->repository->insert($payload)) {
			return WriteResult::error();
		}

		$regionId = $this->repository->lastInsertId();
		if ($regionId <= 0) {
			return WriteResult::error();
		}

		return $this->repository->replaceUfs($regionId, $payload['ufs'])
			? WriteResult::success()
			: WriteResult::error();
	}

	public function update(array $input)
	{
		$payload = $this->normalizePayload($input, true);
		if ($payload === false) {
			return WriteResult::error();
		}

		if ($this->repository->findBySlug($payload['regiao_slug'], $payload['regiao_id'])) {
			return WriteResult::duplicate();
		}

		if (!$this->repository->update($payload)) {
			return WriteResult::error();
		}

		return $this->repository->replaceUfs($payload['regiao_id'], $payload['ufs'])
			? WriteResult::success()
			: WriteResult::error();
	}

	public function delete($id)
	{
		$result = $this->repository->delete($id);
		if ($result === 'LINKED_USERS') {
			return WriteResult::linkedUsers();
		}

		return $result ? WriteResult::success() : WriteResult::error();
	}

	private function normalizePayload(array $input, $isUpdate)
	{
		$name = isset($input['regiao_nome']) ? trim((string) $input['regiao_nome']) : '';
		$slugInput = isset($input['regiao_slug']) ? trim((string) $input['regiao_slug']) : '';
		$slug = $this->slugify($slugInput !== '' ? $slugInput : $name);
		if ($name === '' || $slug === '') {
			return false;
		}

		return array(
			'regiao_id' => isset($input['regiao_id']) ? (int) $input['regiao_id'] : 0,
			'regiao_nome' => $name,
			'regiao_slug' => $slug,
			'regiao_status' => isset($input['regiao_status']) ? (string) $input['regiao_status'] : 'Y',
			'ufs' => $this->parseUfList(isset($input['regiao_ufs']) ? (string) $input['regiao_ufs'] : ''),
		);
	}

	private function parseUfList($value)
	{
		$ufs = array();
		foreach (explode(',', (string) $value) as $uf) {
			$uf = strtoupper(trim($uf));
			if (preg_match('/^[A-Z]{2}$/', $uf)) {
				$ufs[$uf] = $uf;
			}
		}

		return array_values($ufs);
	}

	private function slugify($value)
	{
		$value = strtolower(trim((string) $value));
		$value = preg_replace('/[^a-z0-9]+/', '-', $value);
		$value = trim((string) $value, '-');

		return $value;
	}

	private function brUfs()
	{
		return array(
			'AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG',
			'PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'
		);
	}
}
