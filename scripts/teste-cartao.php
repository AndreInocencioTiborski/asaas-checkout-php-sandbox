<?php
require __DIR__ . '/../src/Env.php';
require __DIR__ . '/../src/AsaasClient.php';

Env::load(__DIR__ . '/../.env');

// php scripts/teste-cartao.php        -> cartão que aprova
// php scripts/teste-cartao.php falha  -> cartão que recusa
$numero = ($argv[1] ?? '') === 'falha' ? '5184019740373151' : '4444444444444444';

try {
    $asaas = AsaasClient::fromEnv();

    $cliente = $asaas->findOrCreateCustomer([
        'name' => 'Teste Asaas',
        'cpfCnpj' => '52998224725',
        'email' => 'teste@teste.com.br',
        'mobilePhone' => '41999998888',
    ]);
    echo "Cliente: {$cliente['id']}\n";

    $pagamento = $asaas->createPayment([
        'customer' => $cliente['id'],
        'billingType' => 'CREDIT_CARD',
        'value' => 49.90,
        'dueDate' => date('Y-m-d', strtotime('+3 days')),
        'description' => 'Teste de cartão',
        'creditCard' => [
            'holderName' => 'Teste Asaas',
            'number' => $numero,
            'expiryMonth' => '12',
            'expiryYear' => '2030',
            'ccv' => '123',
        ],
        'creditCardHolderInfo' => [
            'name' => 'Teste Asaas',
            'email' => 'teste@teste.com.br',
            'cpfCnpj' => '52998224725',
            'postalCode' => '80010000',
            'addressNumber' => '123',
            'phone' => '41999998888',
            'mobilePhone' => '41999998888',
        ],
    ]);

    echo "Cobrança: {$pagamento['id']} | status: {$pagamento['status']}\n";
} catch (RuntimeException $e) {
    echo "Erro da API: " . $e->getMessage() . "\n";
}