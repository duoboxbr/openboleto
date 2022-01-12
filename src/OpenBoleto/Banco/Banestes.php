<?php

namespace OpenBoleto\Banco;

use OpenBoleto\BoletoAbstract;

class Banestes extends BoletoAbstract
{
    const COBRANCA_SIMPLES = 1;

    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '021';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'banestes.png';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'PAGÁVEL PREFERENCIALMENTE NA REDE BANESTES';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = ['01', '11', '13'];

    protected function gerarNossoNumero()
    {
        $sequencial = self::zeroFill($this->getSequencial(), 10);
        return $sequencial . '-' . static::modulo10($sequencial);
    }

    public function getCampoLivre()
    {
        return self::zeroFill($this->getCodigoBanco(), 3) .
            self::zeroFill($this->getMoeda(), 1) .
            self::zeroFill($this->gerarDigitoVerificador(), 1) .
            self::zeroFill(self::getFatorVencimento(), 4) .
            self::zeroFill($this->getValor(), 10) .
            self::zeroFill($this->gerarChaveAsbace(), 25);
    }

    public function gerarDigitoVerificador(): string
    {
        $sequencial = self::zeroFill($this->getSequencial(), 12);
        return static::modulo10($sequencial);
    }

    public function gerarChaveAsbace(): string
    {
        $nossoNumero = self::zeroFill(substr($this->gerarNossoNumero(), 0, 8), 8);
        $conta = self::zeroFill(str_replace('-', '', $this->getConta()), 11);
        $tipoCobranca = self::COBRANCA_SIMPLES;

        $chave = $nossoNumero . $conta . $tipoCobranca . $this->codigoBanco;

//        $d1 = $this->calcularD1($chave);
//        $d2 = $this->calcularD2($chave, $d1);
//        return $chave . $d1 . $d2;

        $d1 = self::modulo10($chave);
        $d2 = self::modulo11($chave, $d1);

        return $chave . $d1 . $d2["digito"];
    }

    public function calcularD1(string $chave): string
    {
        $d1 = $k = $s = 0;
        $peso = 2;

        for ($x = 0; $x < strlen($chave); $x++) {
            $valor = (int)$chave[$x];

            $p = $valor * $peso;

            if ($p > 9) {
                $k = $p - 9;
            }

            if ($p < 10) {
                $k = $p;
            }

            $s += $k;

            $peso = $peso == 2 ? 1 : 2;
        }

        $resto = $s % 10;

        if ($resto > 0) {
            $d1 = 10 - $resto;
        }

        return $d1;
    }

    public function calcularD2(string $chave, int $d1): string
    {
        $d2 = $k = $s = 0;
        $peso = 2;

        $chaveD1 = $chave . $d1;

        for ($x = 0; $x < strlen($chaveD1); $x++) {
            $valor = (int)$chaveD1[$x];

            $p = $valor * $peso--;

            $s += $p;

            if ($peso == 1) {
                $peso = 7;
            }
        }
        $resto = $s % 11;

        if ($resto == 1) {
            $d1++;
            if ($d1 == 10) {
                $d1 = 0;
            }
            return $this->calcularD2($chave, $d1);
        }

        if ($resto > 1) {
            $d2 = 11 - $resto;
        }

        return $d2;
    }
}