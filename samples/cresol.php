<?php

require '../autoloader.php';

use OpenBoleto\Banco\Cresol;
use OpenBoleto\Agente;

$sacado = new Agente('João Teste', '023.434.234-34', 'ABC 302 Bloco N', '72000-000', 'Brasília', 'DF');
$cedente = new Agente('Empresa de cosméticos LTDA', '02.123.123/0001-11', 'CLS 403 Lj 23', '71000-000', 'Brasília', 'DF');

$boleto = new Cresol(array(
    // Parâmetros obrigatórios
    'dataVencimento' => new DateTime('2024-12-24'),
    'valor' => 23.00,
    'sequencial' => 123456789, // Até 9 dígitos
    'sacado' => $sacado,
    'cedente' => $cedente,
    'agencia' => 85, // Até 3 dígitos
    'carteira' => 9, // 1 ou 2
    'conta' => 12345678, // Até 8 dígitos

    // Parâmetros recomendáveis
    //'logoPath' => 'http://empresa.com.br/logo.jpg', // Logo da sua empresa
    'contaDv' => 2,
    'agenciaDv' => 1,
    'descricaoDemonstrativo' => array( // Até 5
        'Compra de materiais cosméticos',
        'Compra de alicate',
    ),
    'instrucoes' => array( // Até 8
        'Após o dia 30/11 cobrar 2% de mora e 1% de juros ao dia.',
        'Não receber após o vencimento.',
    ),
));

echo $boleto->getOutput();
