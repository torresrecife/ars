<?php

namespace Tests\Unit;

use App\Repositories\NeoSqlsrvRepository;
use Tests\TestCase;

class NeoSqlsrvRepositoryTest extends TestCase
{
	public function test_person_type_condition_filters_only_defendant_and_treats_null_as_individual()
	{
		$repository = new class(null) extends NeoSqlsrvRepository {
			public function exposeBuildPersonTypeCondition($personType)
			{
				return $this->buildPersonTypeCondition($personType);
			}
		};

		$condition = $repository->exposeBuildPersonTypeCondition('fisica');

		$this->assertStringContainsString("pp.TipoPessoa = N'Réu'", $condition);
		$this->assertStringContainsString('ISNULL(pp.TipoJuridica, 0) = 0', $condition);
		$this->assertStringNotContainsString('Pessoa AS', $condition);
	}

	public function test_person_type_condition_filters_legal_entities_without_joining_person_table()
	{
		$repository = new class(null) extends NeoSqlsrvRepository {
			public function exposeBuildPersonTypeCondition($personType)
			{
				return $this->buildPersonTypeCondition($personType);
			}
		};

		$condition = $repository->exposeBuildPersonTypeCondition('juridica');

		$this->assertStringContainsString("pp.TipoPessoa = N'Réu'", $condition);
		$this->assertStringContainsString('ISNULL(pp.TipoJuridica, 0) = 1', $condition);
		$this->assertStringNotContainsString('Pessoa AS', $condition);
	}

    public function test_build_uf_condition_includes_code_and_name_variants()
    {
        $repository = new class(null) extends NeoSqlsrvRepository {
            public function exposeBuildUfCondition(array $ufCodes, $field = 'p.UFComarca')
            {
                return $this->buildUfCondition($ufCodes, $field);
            }
        };

        $condition = $repository->exposeBuildUfCondition(array('SP', 'PR'));

        $this->assertStringContainsString("p.UFComarca IN", $condition);
        $this->assertStringContainsString("'SP'", $condition);
        $this->assertStringContainsString("'São Paulo'", $condition);
        $this->assertStringContainsString("'Sao Paulo'", $condition);
        $this->assertStringContainsString("'PR'", $condition);
        $this->assertStringContainsString("'Paraná'", $condition);
        $this->assertStringContainsString("'Parana'", $condition);
    }
}
