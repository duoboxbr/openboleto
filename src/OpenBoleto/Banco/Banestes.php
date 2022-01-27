<?php

namespace OpenBoleto\Banco;

use OpenBoleto\BoletoAbstract;

class Banestes extends BoletoAbstract
{
    const CARTEIRA_SIMPLES = 1;
    const CARTEIRA_CAUCIONADA = 3;
    const COBRANCA_COM_REGISTRO = 4;

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
        $d1 = static::modulo11($sequencial);
        $d2 = static::modulo11($sequencial . $d1['digito']);
        return $sequencial . '-' . $d1['digito'] . $d2["digito"];
    }

    public function getCampoLivre()
    {
        $nossoNumero = self::zeroFill(substr($this->getNossoNumero(false), 0, 8), 8);
        $conta = self::zeroFill(str_replace('-', '', $this->getConta()), 11);

        $chave = $nossoNumero . $conta . self::COBRANCA_COM_REGISTRO . $this->codigoBanco;

        $d1 = self::modulo10($chave);
        $d2 = self::modulo11( $chave . $d1, 7);

        return $chave . $d1 . $d2["digito"];
    }

    public function gerarDigitoVerificador(): string
    {
        $sequencial = self::zeroFill($this->getSequencial(), 12);
        return static::modulo10($sequencial);
    }

    private static function modulo10Asbace($num)
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