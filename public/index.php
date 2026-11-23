<?php
require __DIR__ . '/../src/bootstrap.php';
$erro = $_SESSION['erro'] ?? null;
unset($_SESSION['erro']);
?>
<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Pagamento</title>
        <style>
            body {
                font-family: system-ui, sans-serif;
                max-width: 480px;
                margin: 40px auto;
                padding: 0 16px;
            }
            label {
                display: block;
                margin: 12px 0 4px;
                font-weight: 600;
            }
            input {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 6px;
                font: inherit;
            }
            button {
                margin-top: 20px;
                width: 100%;
                padding: 14px;
                font: inherit;
                font-weight: 600;
                background: #0b7d74;
                color: #fff;
                border: 0;
                border-radius: 8px;
                cursor: pointer;
            }
            .erro {
                background: #fdecea;
                color: #b42318;
                padding: 10px;
                border-radius: 6px;
            }
            .metodo label {
                display: inline;
                font-weight: 400;
                margin-right: 12px;
            }
            .metodo input {
                width: auto
            }
        </style>
    </head>
    <body>
        <h1>Pagamento</h1>
        <p><?= e(PRODUCT['name']) ?>: <strong>R$ <?=number_format(PRODUCT['value'], 2, ',', '.') ?></strong></p>

        <?php if ($erro): ?><p class="erro" role="alert"><?= e($erro) ?></p><?php endif; ?>

        <form action="checkout.php" method="post">
            <label for="name">Nome Completo</label>
            <input id="name" name="name" required>

            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" required>

            <label for="cpfCnpj">CPF ou CNPJ</label>
            <input id="cpfCnpj" name="cpfCnpj" inputmode="numeric" required>

            <label for="phone">Celular com DDD</label>
            <input id="phone" name="phone" type="tel" required>

            <label>Forma de Pagamento</label>
            <div class="metodo">
                <label><input type="radio" name="method" value="PIX" checked> Pix</label>
                <label><input type="radio" name="method" value="CREDIT_CARD"> Cartão</label>
                <label><input type="radio" name="method" value="BOLETO"> Boleto</label>
            </div>

            <div id="cartao" hidden>
                <label for="card_number">Número do cartão</label>
                <input id="card_number" name="card_number" inputmode="numeric" autocomplete="cc-number" maxlength="19" placeholder="0000 0000 0000 0000">

                <label for="card_holder">Nome no cartão</label>
                <input id="card_holder" name="card_holder" autocomplete="cc-name">

                <label for="card_expiry">Validade</label>
                <input id="card_expiry" name="card_expiry" maxlength="5" placeholder="12/30" autocomplete="cc-exp">

                <label for="card_cvv">CVV</label>
                <input id="card_cvv" name="card_cvv" inputmode="numeric" autocomplete="cc-csc">

                <label for="postalCode">CEP</label>
                <input id="postalCode" name="postalCode" inputmode="numeric">

                <label for="addressNumber">Número do endereço</label>
                <input id="addressNumber" name="addressNumber">
            </div>

            <button type="submit">Pagar</button>
        </form>

        <script>
            //Mostra os campos do cartão só quando "Cartão" estiver marcado
            const cartao = document.getElementById('cartao');
            document.querySelectorAll('input[name="method"]').forEach(radio =>
                radio.addEventListener('change', () => {
                    cartao.hidden = document.querySelector('input[name="method"]:checked').value !== 'CREDIT_CARD';
                })
            );

            const numeroCartao = document.getElementById('card_number');
            const validadeCartao = document.getElementById('card_expiry');

            numeroCartao.addEventListener('input', () => {
                const numeros = numeroCartao.value.replace(/\D/g, '').slice(0,16);

                numeroCartao.value = numeros.match(/.{1,4}/g)?.join(' ') ?? '';
            });

            validadeCartao.addEventListener('input', () => {
                const numeros = validadeCartao.value.replace(/\D/g, '').slice(0, 4);

                validadeCartao.value = numeros.length > 2
                    ? `${numeros.slice(0, 2)}/${numeros.slice(2)}`
                    : numeros;
            })
        </script>
    </body>
</html>