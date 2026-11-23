<?php
require __DIR__ . '/../src/Env.php';
require __DIR__ . '/../src/AsaasClient.php';

Env::load(__DIR__ . '/../.env');

try {

    $r = AsaasClient::fromEnv()->ping();
    echo "OK! Clientes na conta: ".($r['totalCount'] ?? 0) . PHP_EOL;
} catch (RuntimeException $e) {
    echo "Falhou: " . $e->getMessage(). PHP_EOL;
}