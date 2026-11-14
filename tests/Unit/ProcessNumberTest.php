<?php

namespace Tests\Unit;

use App\Support\ProcessNumber;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ProcessNumberTest extends TestCase
{
    public static function numbers(): array
    {
        return array(
            'CNJ sem mascara' => array('00057341920258172810', '0005734-19.2025.8.17.2810'),
            'CNJ ja formatado' => array('0005734-19.2025.8.17.2810', '0005734-19.2025.8.17.2810'),
            'antigo sem mascara' => array('01234567890123456', '01.23.4567.890.12345-6'),
            'antigo ja formatado' => array('01.23.4567.890.12345-6', '01.23.4567.890.12345-6'),
            'CNJ com outra pontuacao' => array('0005734.19.2025.8.17.2810', '0005734-19.2025.8.17.2810'),
            'tamanho desconhecido' => array('12345', '12345'),
        );
    }

    #[DataProvider('numbers')]
    public function test_formats_process_number(string $input, string $expected)
    {
        $this->assertSame($expected, ProcessNumber::format($input));
    }
}
