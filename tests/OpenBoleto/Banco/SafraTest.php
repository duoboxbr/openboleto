<?php

namespace Tests\OpenBoleto\Banco;
use OpenBoleto\Banco\Safra;


class SafraTest extends KernelTestCaseAncestor
{
    public function testInstantiateWithoutArgumentsShouldWork()
    {
        $this->assertInstanceOf('OpenBoleto\\Banco\\Safra', new Safra());
    }

    public function testInstantiateShouldWork()
    {
        $instance = new Safra(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new \DateTime('2020-08-01'),
            'valor' => 51.23,
            'sequencial' => 11, // 8 dígitos
            'agencia' => 17800, // 4 dígitos
            'carteira' => 1, // 3 dígitos
            'conta' => 582037, // 5 dígitos
            'contaDv' => 8, // 5 dígitos

            // Parâmetro obrigatório somente se a carteira for
            // 107, 122, 142, 143, 196 ou 198
            'codigoCliente' => 12345, // 5 dígitos
            'numeroDocumento' => 11, // 7 dígitos
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\Safra', $instance);
        $this->assertEquals('34191.12127 34567.881726 41234.580003 1 55650000001050', $instance->getLinhaDigitavel());
        $this->assertSame('112/12345678-8', (string) $instance->getNossoNumero());
    }

    public function testInstantiateWithCarteira107ShouldWork()
    {
        $instance = new Safra(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new \DateTime('2020-08-01'),
            'valor' => 51.23,
            'sequencial' => 11, // 8 dígitos
            'agencia' => 17800, // 4 dígitos
            'carteira' => 1, // 3 dígitos
            'conta' => 582037, // 5 dígitos
            'contaDv' => 8, // 5 dígitos

            // Parâmetro obrigatório somente se a carteira for
            // 107, 122, 142, 143, 196 ou 198
            'codigoCliente' => 12345, // 5 dígitos
            'numeroDocumento' => 11, // 7 dígitos
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\Safra', $instance);
        $this->assertEquals('34191.07127 34567.812341 56766.677001 9 55650000001050', $instance->getLinhaDigitavel());
        $this->assertSame('107/12345678-0', (string) $instance->getNossoNumero());
    }
}
