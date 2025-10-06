<?php

namespace Tests\Unit;

use Tests\TestCase;

class PrefixHelperTest extends TestCase
{
    public function test_prefix_function_returns_correct_value()
    {
        // Chama a função com um nome de tabela exemplo
        $result = prefix('usuario');

        // Esperado: syrios_usuario (ou o prefixo configurado)
        $expected = config('prefix.tabelas', 'syrios_') . 'usuario';

        $this->assertEquals($expected, $result);
    }
}
