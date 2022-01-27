<?php

namespace OpenBoleto\Banco;

use PHPUnit\Framework\TestCase;

class BanestesTest extends TestCase
{
    private $banestes;

    protected function setUp()
    {
        $conf = [
            'dataVencimento' => new \DateTime('2018-04-06'),
            'valor' => 39.00,
            'sequencial' => 123, // Até 13 dígitos
            'agencia' => 84, // Até 4 dígitos
            'carteira' => 11,
            'conta' => 4516015, // Código do cedente: Até 7 dígitos
        ];
        $this->banestes = new Banestes($conf);
    }

    public function testInstanciaBanestes()
    {
        $this->assertInstanceOf('OpenBoleto\\Banco\\Banestes', $this->banestes);
    }

    public function testRetornoLinhaDigitavel()
    {
        $this->assertEquals('02190.00007 12300.004517 60154.021137 1 74860000003900', $this->banestes->getLinhaDigitavel());
    }

    public function testRetornoNossoNumero()
    {
        $this->assertSame('0000012300', $this->banestes->getNossoNumero(false));
    }
}