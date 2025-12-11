<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class MpesaService
{
    protected string $baseUrl;

    public function __construct()
    {
        $env = config('mpesa.environment', 'sandbox');
        $this->baseUrl = $env === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    protected function accessToken(): string
    {
        $consumerKey = config('mpesa.consumer_key');
        $consumerSecret = config('mpesa.consumer_secret');

        $response = Http::timeout(config('mpesa.request_timeout', 30))
            ->withBasicAuth($consumerKey, $consumerSecret)
            ->get($this->baseUrl . '/oauth/v1/generate', [
                'grant_type' => 'client_credentials',
            ]);

        if (! $response->successful()) {
            Log::error('MPesa access token request failed', ['body' => $response->body()]);
            throw new RuntimeException('Unable to obtain MPesa access token.');
        }

        return $response->json()['access_token'] ?? '';
    }

    public function initiateStkPush(float $amount, string $phone, string $reference, string $description = 'StockFlowPOS Payment'): array
    {
        $token = $this->accessToken();

        $shortcode = config('mpesa.shortcode');
        $passkey = config('mpesa.passkey');
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($shortcode . $passkey . $timestamp);
        $callbackUrl = config('mpesa.callback_url');

        // Normalize phone to 2547xxxxxxxx
        $normalizedPhone = $this->normalizePhone($phone);

        $payload = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $normalizedPhone,
            'PartyB' => $shortcode,
            'PhoneNumber' => $normalizedPhone,
            'CallBackURL' => $callbackUrl,
            'AccountReference' => Str::limit($reference, 20, ''),
            'TransactionDesc' => Str::limit($description, 40, ''),
        ];

        $response = Http::timeout(config('mpesa.request_timeout', 30))
            ->withToken($token)
            ->post($this->baseUrl . '/mpesa/stkpush/v1/processrequest', $payload);

        if (! $response->successful()) {
            Log::error('MPesa STK Push failed', ['payload' => $payload, 'body' => $response->body()]);
            throw new RuntimeException('Unable to initiate MPesa STK Push.');
        }

        $json = $response->json();
        if (($json['ResponseCode'] ?? null) !== '0') {
            Log::error('MPesa STK Push error response', ['payload' => $payload, 'json' => $json]);
            throw new RuntimeException($json['ResponseDescription'] ?? 'MPesa STK Push rejected.');
        }

        return $json;
    }

    protected function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (Str::startsWith($digits, '0')) {
            $digits = '254' . substr($digits, 1);
        } elseif (Str::startsWith($digits, '7') && strlen($digits) === 9) {
            $digits = '254' . $digits;
        }

        return $digits;
    }
}

