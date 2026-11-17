<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * As telas de detalhe (andamentos e faturamento) abrem fora do shell e carregam os proprios
 * scripts: o botao "Exportar para Excel" chama exportDetailTable() (details.js) e os filtros do
 * cabecalho vem do carregarFiltros() (jFilterXCel2003.js). Sem eles: "exportDetailTable is not defined".
 */
class DetailViewScriptsTest extends TestCase
{
    public static function detailViews(): array
    {
        return [
            'andamentos' => ['dados_anda/index.blade.php'],
            'faturamento' => ['dados_fatur/index.blade.php'],
        ];
    }

    #[DataProvider('detailViews')]
    public function test_detail_view_loads_export_and_filter_scripts(string $view): void
    {
        $source = file_get_contents(resource_path('views/' . $view));

        $this->assertStringContainsString("exportDetailTable(", $source);
        $this->assertStringContainsString("asset('js/modules/details.js')", $source);
        $this->assertStringContainsString("asset('js/jFilterXCel2003.js')", $source);
        $this->assertFileExists(public_path('js/modules/details.js'));
        $this->assertFileExists(public_path('js/jFilterXCel2003.js'));
    }
}
