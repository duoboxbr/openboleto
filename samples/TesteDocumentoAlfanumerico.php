<?php

require '../autoloader.php';

use OpenBoleto\Agente;

/**
 * Testa Agente::getTipoDocumento() com CPF, CNPJ numérico e CNPJ
 * alfanumérico (novo formato da Receita Federal, com letras nos 12
 * primeiros caracteres)
 */

$casos = [
    'CPF' => ['123.456.789-01', 'CPF'],
    'CNPJ numérico' => ['11.222.333/0001-81', 'CNPJ'],
    'CNPJ alfanumérico' => ['12.ABC.345/01DE-35', 'CNPJ'],
    'valor inválido' => ['valor-qualquer', 'Documento'],
];

$falhas = 0;

foreach ($casos as $nome => [$documento, $esperado]) {
    $agente = new Agente('Nome Teste', $documento);
    $resultado = $agente->getTipoDocumento();
    $ok = $resultado === $esperado;

    if (!$ok) {
        $falhas++;
    }

    printf(
        "[%s] %s\n    documento: %s\n    tipo: %s (esperado: %s)\n\n",
        $ok ? 'OK' : 'FALHOU',
        $nome,
        $documento,
        $resultado,
        $esperado
    );
}

echo $falhas === 0
    ? "Todos os casos passaram." . PHP_EOL
    : "{$falhas} caso(s) falharam." . PHP_EOL;

exit($falhas === 0 ? 0 : 1);
