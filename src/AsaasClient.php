<?php
final class AsaasClient {
    
    public function __construct(private string $apiKey, private string $baseUrl) {}

    public static function fromEnv(): self {

        $ambiente = Env::get('ASAAS_ENV', 'sandbox');
        $url = $ambiente === 'production'
            ? Env::get('ASAAS_URL_PRODUCTION')
            : Env::get('ASAAS_URL_SANDBOX');

        return new self((string) Env::get('ASAAS_API_KEY', ''), rtrim((string) $url, '/'));
    }

    public function ping(): array {

        return $this->request('GET', 'customers', ['limit' => 1]);
    }

    // Procura cliente pelo cpf ou cnpj, caso nao existir ele cria
    public function findOrCreateCustomer(array $cliente): array {

        $achados = $this->request('GET', 'customers', ['cpfCnpj' => $cliente['cpfCnpj'], 'limit' => 1]);

        if(!empty($achados['data'][0])) {
            return $achados['data'][0];
        }
        return $this->request('POST', 'customers', [], $cliente);
    }

    public function createPayment(array $cobranca): array {

        return $this->request('POST', 'payments', [], $cobranca);
    }

    public function getPayment(string $id): array {

        return $this->request('GET', 'payments/' . rawurlencode($id));
    }

    // Devolve encodedImage(PNG em base64) e payload ("copia e cola")
    public function getPixQrCode(string $id): array {

        return $this->request('GET', 'payments/' . rawurlencode($id) . '/pixQrCode');
    }

    public function request(string $method, string $path, array $query = [], ?array $body = null): array {

        $url = $this->baseUrl . '/' . ltrim($path, '/');

        if($query) {
            $url .= '?' . http_build_query($query);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'User-Agent: MeuCheckout/1.0 (PHP)',
                'access_token: '. $this->apiKey,
            ],
        ]);

        if ($body !== null) {

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $bruto = curl_exec($ch);
        if ($bruto === false) {

            throw new RuntimeException('Falha de conexão: '. curl_error($ch));
        }

        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $dados = json_decode($bruto, true) ?? [];

        if($status >= 400) {

            $msg = isset($dados['errors'])
                ? implode(' ', array_column($dados['errors'], 'description'))
                : "Erro HTTP $status";
            throw new RuntimeException($msg);
        }
        return $dados;
    }
}