<?php
require __DIR__ . '/../src/bootstrap.php';

function voltar(string $mensagem): never {

    $_SESSION['erro'] = $mensagem;
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    
    redirect('index.php');
}

// Vai ler e limpar os dados
$nome = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$cpf = preg_replace('/\D/', '', $_POST['cpfCnpj'] ?? ''); // limpa qualquer letra lendo apenas numero
$fone = preg_replace('/\D/', '', $_POST['phone'] ?? '');
$metodo = $_POST['method'] ?? '';

// Validar NO SERVIDOR
if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) voltar ('Confira nome e e-mail');
if (!in_array(strlen($cpf), [11, 14], true)) voltar ('CPF ou CNPJ inválido.');
if (!in_array($metodo, ['PIX', 'BOLETO', 'CREDIT_CARD'], true)) voltar ('Escolha a forma de pagamento.');

try {

    $asaas = AsaasClient::fromEnv();

    // Cliente
    $cliente = $asaas->findOrCreateCustomer([
        'name' => $nome, 'cpfCnpj' => $cpf, 'email' => $email, 'mobilePhone' => $fone,
    ]);

    // Cobrança
    $cobranca = [
        'customer' => $cliente['id'],
        'billingType' => $metodo,
        'value' => PRODUCT['value'],
        'dueDate' => date('Y-m-d', strtotime('+3 days')),
        'description' => PRODUCT['name'],
    ];

    if ($metodo === 'CREDIT_CARD') {

        if (!preg_match('#^(\d{2})/(\d{2})$#', trim($_POST['card_expiry'] ?? ''), $m)) {
            voltar('Validade inválida. Use MM/AA.');
        }

        $cobranca['creditCard'] = [
            'holderName' => trim($_POST['card_holder'] ?? ''),
            'number' => preg_replace('/\D/', '', $_POST['card_number'] ?? ''),
            'expiryMonth' => $m[1],
            'expiryYear' => '20' . $m[2],
            'ccv' => preg_replace('/\D/', '', $_POST['card_cvv'] ?? ''),
        ];

        $cobranca['creditCardHolderInfo'] = [
            'name' => $nome, 'email' => $email, 'cpfCnpj' => $cpf,
            'postalCode' => preg_replace('/\D/', '', $_POST['postalCode'] ?? ''),
            'addressNumber' => trim($_POST['addressNumber'] ?? ''),
            'phone' => $fone, 'mobilePhone' => $fone,
        ];

        $cobranca['remoteIp'] = $_SERVER['REMOTE_ADDR'];
    }

    $pagamento = $asaas->createPayment($cobranca);
} catch (RuntimeException $e) {
    voltar($e->getMessage());
}

// Guarda o id e redireciona
$_SESSION['payment_id'] = $pagamento['id'];
redirect('resultado.php');