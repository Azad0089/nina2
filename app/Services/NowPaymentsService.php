<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class NowPaymentsService
{
    protected string $defaultApiKey = '4DMYGP5-7V04ZZJ-G3JTHJH-XGNZHD5';
    protected string $defaultIpnSecret = 'mIV4c5kQrKwYN4PEGRV4L5MM8UQUhAEz';

    public function getApiKey(): string
    {
        return Setting::get('nowpayments_api_key', $this->defaultApiKey) ?: $this->defaultApiKey;
    }

    public function getIpnSecret(): string
    {
        return Setting::get('nowpayments_ipn_secret', $this->defaultIpnSecret) ?: $this->defaultIpnSecret;
    }

    public function isSandbox(): bool
    {
        return Setting::get('nowpayments_sandbox', '0') === '1';
    }

    public function getBaseUrl(): string
    {
        return $this->isSandbox()
            ? 'https://api-sandbox.nowpayments.io/v1'
            : 'https://api.nowpayments.io/v1';
    }

    /**
     * Test connection to NOWPayments API
     */
    public function checkStatus(): array
    {
        $startTime = microtime(true);
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->getApiKey(),
                'Accept' => 'application/json',
            ])->timeout(8)->get($this->getBaseUrl() . '/status');

            $latencyMs = round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'ok' => ($data['message'] ?? '') === 'OK',
                    'message' => $data['message'] ?? 'OK',
                    'latencyMs' => $latencyMs,
                    'baseUrl' => $this->getBaseUrl(),
                    'isSandbox' => $this->isSandbox(),
                ];
            }

            return [
                'ok' => false,
                'message' => 'HTTP ' . $response->status() . ': ' . $response->body(),
                'latencyMs' => $latencyMs,
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'message' => $e->getMessage(),
                'latencyMs' => round((microtime(true) - $startTime) * 1000),
            ];
        }
    }

    /**
     * Get list of supported cryptocurrencies
     */
    public function getCurrencies(): array
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->getApiKey(),
                'Accept' => 'application/json',
            ])->timeout(8)->get($this->getBaseUrl() . '/currencies');

            if ($response->successful()) {
                $data = $response->json();
                return $data['currencies'] ?? [];
            }
        } catch (\Throwable $e) {
            Log::warning('NOWPayments getCurrencies error: ' . $e->getMessage());
        }

        return ['btc', 'eth', 'usdttrc20', 'usdterc20', 'ltc', 'sol', 'doge', 'trx', 'xmr'];
    }

    /**
     * Create a hosted payment invoice
     */
    public function createInvoice(array $params): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->getApiKey(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(12)->post($this->getBaseUrl() . '/invoice', [
            'price_amount' => $params['price_amount'],
            'price_currency' => strtolower($params['price_currency'] ?? 'usd'),
            'pay_currency' => !empty($params['pay_currency']) ? strtolower($params['pay_currency']) : null,
            'order_id' => $params['order_id'],
            'order_description' => $params['order_description'] ?? 'Platform Subscription',
            'ipn_callback_url' => $params['ipn_callback_url'] ?? null,
            'success_url' => $params['success_url'] ?? null,
            'cancel_url' => $params['cancel_url'] ?? null,
        ]);

        $data = $response->json();
        if (!$response->successful() || empty($data['invoice_url'])) {
            $msg = $data['message'] ?? ('NOWPayments HTTP ' . $response->status());
            throw new \RuntimeException($msg);
        }

        return $data;
    }

    /**
     * Create a direct on-chain cryptocurrency payment (returns address & QR details)
     */
    public function createPayment(array $params): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->getApiKey(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(12)->post($this->getBaseUrl() . '/payment', [
            'price_amount' => $params['price_amount'],
            'price_currency' => strtolower($params['price_currency'] ?? 'usd'),
            'pay_currency' => strtolower($params['pay_currency'] ?? 'usdttrc20'),
            'order_id' => $params['order_id'],
            'order_description' => $params['order_description'] ?? 'Platform Crypto Subscription',
            'ipn_callback_url' => $params['ipn_callback_url'] ?? null,
            'customer_email' => $params['customer_email'] ?? null,
        ]);

        $data = $response->json();
        if (!$response->successful() || empty($data['payment_id'])) {
            $msg = $data['message'] ?? ('NOWPayments Direct Payment Error: ' . $response->status());
            throw new \RuntimeException($msg);
        }

        return $data;
    }

    /**
     * Get live status of payment by payment_id
     */
    public function getPaymentStatus(string|int $paymentId): ?array
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->getApiKey(),
                'Accept' => 'application/json',
            ])->timeout(8)->get($this->getBaseUrl() . '/payment/' . $paymentId);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to fetch payment status for {$paymentId}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Verify HMAC-SHA512 signature for incoming IPN notification
     */
    public function verifyIpnSignature(array|string $payload, ?string $signature): bool
    {
        if (empty($signature)) {
            return false;
        }

        $secret = trim($this->getIpnSecret());
        if (empty($secret)) {
            return false;
        }

        try {
            $data = is_array($payload) ? $payload : json_decode($payload, true);
            if (!is_array($data)) {
                return false;
            }

            $sorted = $this->sortObjectKeys($data);
            $sortedJson = json_encode($sorted, JSON_UNESCAPED_SLASHES);
            $calculatedHmac = hash_hmac('sha512', $sortedJson, $secret);

            return hash_equals(strtolower($calculatedHmac), strtolower(trim($signature)));
        } catch (\Throwable $e) {
            Log::error('NOWPayments IPN signature verification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Recursively sort associative array keys alphabetically
     */
    protected function sortObjectKeys(mixed $item): mixed
    {
        if (!is_array($item)) {
            return $item;
        }

        // If numeric indexed array, sort items inside
        if (array_is_list($item)) {
            foreach ($item as $k => $v) {
                $item[$k] = $this->sortObjectKeys($v);
            }
            return $item;
        }

        ksort($item);
        foreach ($item as $k => $v) {
            $item[$k] = $this->sortObjectKeys($v);
        }

        return $item;
    }
}
