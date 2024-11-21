<?php
/*
 * OpenBoleto - Geração de boletos bancários em PHP
 *
 * LICENSE: The MIT License (MIT)
 *
 * Copyright (C) 2013 Estrada Virtual
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this
 * software and associated documentation files (the "Software"), to deal in the Software
 * without restriction, including without limitation the rights to use, copy, modify,
 * merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies
 * or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace OpenBoleto\Banco;

use duobox\Cobranca\Boletos\Parser\CodigoBarraCresol;
use OpenBoleto\BoletoAbstract;
use OpenBoleto\Exception;

/**
 * Classe Cresol
 *
 * @package    OpenBoleto
 * @author     Daniel Garajau <http://github.com/kriansa>
 * @copyright  Copyright (c) 2013 Estrada Virtual (http://www.estradavirtual.com.br)
 * @license    MIT License
 * @version    1.0
 */
class Cresol extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '133';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'cresol.png';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'Pagar preferencialmente nas cooperativas do Sistema Cresol.';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('9');

    /**
     * Campo obrigatório para emissão de boletos com carteira 198 fornecido pelo Banco com 5 dígitos
     * @var int
     */
    protected $codigoCliente;

    /**
     * Dígito verificador da carteira/nosso número para impressão no boleto
     * @var int
     */
    protected $carteiraDv;

    /**
     * Dígito de auto-conferência do nosso número
     * @var int
     */
    protected $dacNossoNumero;

    /**
     * Cache do campo livre para evitar processamento desnecessário.
     *
     * @var string
     */
    protected $campoLivre;

    /**
     * Gera o Nosso Número.
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $carteira = self::zeroFill($this->getCarteira(), 2);
        $sequencial = self::zeroFill($this->getSequencial(), 11);

        $numero = $carteira . '/' . $sequencial . '-' . $this->gerarDigitoVerificadorNossoNumero();

        return $numero;
    }

    protected function gerarDigitoVerificadorNossoNumero() {
        $carteira = self::zeroFill($this->getCarteira(), 2);
        $sequencial = self::zeroFill($this->getSequencial(), 11);

        $numero = $carteira . $sequencial;
        $resto = static::modulo11($numero,7);
        if ($resto['resto'] == 1) {
            $digitoVerificador = 'P';
        } elseif ($resto['resto'] == 0) {
            $digitoVerificador = 0;
        } else {
            $digitoVerificador = 11 - $resto['resto'];
        }

        return $digitoVerificador;
    }

    public function getCampoLivre()
    {
        return $this->getAgencia() .
            self::zeroFill($this->getCarteira(), 2) .
            self::zeroFill($this->getSequencial(), 11) .
            self::zeroFill($this->getConta(), 7) . '0';
    }
}