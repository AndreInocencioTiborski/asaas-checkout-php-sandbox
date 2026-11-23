# Checkout Asaas em PHP

Tela de pagamento (Pix, boleto e cartão) integrada à API do Asaas, usando o Sandbox.

## Como rodar
1. PHP 8.1+ com a extensão `curl`
2. `copy .env.example .env` e coloque sua chave do Sandbox
3. `php scripts/teste-conexao.php` para validar a chave
4. `php -S localhost:8000 -t public` e abra http://localhost:8000

## Estrutura
- `public/`: páginas (formulário, checkout, resultado)
- `src/`: leitor de .env e cliente da API
- `scripts/`: teste de conexão

> Cartões de teste: veja a documentação de Sandbox do Asaas.