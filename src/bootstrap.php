<?php
require __DIR__ . '/Env.php';
require __DIR__ . '/AsaasClient.php';

date_default_timezone_set('America/Sao_Paulo');
session_start();
Env::load(__DIR__ . '/../.env');

const PRODUCT = ['name' => 'Descarte de impressão 3d', 'value' => 05.00];

function e(mixed $v): string {

    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

function redirect(string $to): never {

    header('Location: ' . $to);
    exit;
}