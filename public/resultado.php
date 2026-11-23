<?php
require __DIR__ . '/../src/bootstrap.php';

$id = $_SESSION['payment_id'] ?? null;

if (!$id) {
    redirect('index.php');
}

$erro = null;
try {

    $asaas = AsaasClient::fromEnv();
    $pagamento = $asaas->getPayment($id); // status atual direto da API
    $pix = $pagamento['billingType'] === 'PIX' ? $asaas->getPixQrCode($id) : null;
} catch (RuntimeException $e) {
    $erro = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado</title>
    <style>
        body{
            font-family: system-ui, sans-serif;
            max-width: 480px;
            margin: 40px auto;
            padding: 0 16px;
        }
    </style>
</head>
<body>
    <?php if($erro): ?>
        <h1>Não foi possível consultar</h1>
        <p><?= e($erro) ?></p>
    <?php else: ?>
        <h1>Status: <?= e($pagamento['status']) ?></h1>
        <p>Valor: R$ <?= number_format((float) $pagamento['value'], 2, ',', '.') ?></p>
        
        <?php if($pix): ?>
            <img src="data:image/png;base64,<?= e($pix['encodedImage']) ?>" width="220" height="220" alt="QR Code Pix">
            <p>Pix copia e cola:</p>
            <textarea readonly rows="4" style="width:100%"><?= e($pix['payload']) ?></textarea>
        <?php endif; ?>

        <?php if(!empty($pagamento['bankSlipUrl'])): ?>
            <p><a href="<?= e($pagamento['bankSlipUrl']) ?>" target="_blank" rel="noopener">Abrir boleto</a></p>
        <?php endif; ?>

        <P><a href="<?= e($pagamento['invoiceUrl']) ?>" target="_blank" rel="noopener">Abrir fatura no Asaas</a></p>
    <?php endif; ?>
        <p><a href="index.php">Nova Compra</a></p>
</body>
</html>