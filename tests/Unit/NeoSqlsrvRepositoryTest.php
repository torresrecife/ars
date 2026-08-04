<?php

namespace Tests\Unit;

use App\Repositories\NeoSqlsrvRepository;
use Tests\TestCase;

class NeoSqlsrvRepositoryTest extends TestCase
{
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
