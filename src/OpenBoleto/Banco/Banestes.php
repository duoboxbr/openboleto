<?php

namespace OpenBoleto\Banco;

use OpenBoleto\BoletoAbstract;

class Banestes extends BoletoAbstract
{
    const CARTEIRA_SIMPLES = 1;
    const CARTEIRA_CAUCIONADA = 3;
    const COBRANCA_COM_REGISTRO = 4;
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
        $sequencial = self::zeroFill($this->getSequencial(), 8);
        $d1 = static::modulo10($sequencial);
        $d2 = static::modulo11($d1, 9);
        return $sequencial . '-' . $d1 . $d2["digito"];
    }

    public function getCampoLivre()
    {
        $d1 = static::modulo10($this->getSequencial());
        $d2 = static::modulo11($d1, 9);
        $digitoVerificador = $d1 . $d2["digito"];

        $campoLivre = self::zeroFill(substr($this->getNossoNumero(false), 0, 8), 8) .
            self::zeroFill($this->getConta(), 11) .
            self::COBRANCA_COM_REGISTRO .
            self::zeroFill($this->codigoBanco, 3) .
            self::zeroFill($digitoVerificador, 2);

        return $campoLivre;
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

        $d1 = self::modulo10($chave);
        $d2 = self::modulo11($chave, $d1);

        return $chave . $d1 . $d2["digito"];
    }

    protected static function modulo10($num)
    {
        $d1 = $k = $s = 0;
        $peso = 2;

        for ($i = 0; $i < strlen($num); $i++) {
            $valor = $num[$i];

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
}