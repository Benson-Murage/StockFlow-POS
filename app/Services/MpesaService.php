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

        // Validate that credentials are not dummy values
        if (empty($consumerKey) || empty($consumerSecret)) {
            throw new RuntimeException('MPesa credentials are not configured. Please set MPESA_CONSUMER_KEY and MPESA_CONSUMER_SECRET in your .env file.');
        }

        if (in_array($consumerKey, ['dummy_key', 'your_consumer_key_here']) || 
            in_array($consumerSecret, ['dummy_secret', 'your_consumer_secret_here'])) {
            throw new RuntimeException('MPesa credentials appear to be placeholder values. Please configure real Safaricom API credentials.');
        }

        $response = Http::timeout(config('mpesa.request_timeout', 30))
            ->withBasicAuth($consumerKey, $consumerSecret)
            ->get($this->baseUrl . '/oauth/v1/generate', [
                'grant_type' => 'client_credentials',
            ]);

        if (! $response->successful()) {
            Log::error('MPesa access token request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $this->baseUrl . '/oauth/v1/generate'
            ]);
            
            $errorMessage = 'Unable to obtain MPesa access token.';
            if ($response->status() === 401) {
                $errorMessage = 'MPesa authentication failed. Please check your consumer key and secret.';
            } elseif ($response->status() === 403) {
                $errorMessage = 'MPesa access forbidden. Please check your API permissions.';
            }
            
            throw new RuntimeException($errorMessage);
        }

        $responseData = $response->json();
        if (!isset($responseData['access_token'])) {
            Log::error('MPesa access token response missing access_token', ['response' => $responseData]);
            throw new RuntimeException('Invalid response from MPesa API: access_token missing.');
        }

        return $responseData['access_token'];
    }

    public function initiateStkPush(float $amount, string $phone, string $reference, string $description = 'StockFlowPOS Payment'): array
    {
        $token = $this->accessToken();

        $shortcode = config('mpesa.shortcode');
        $passkey = config('mpesa.passkey');
        
        // Validate shortcode and passkey
        if (empty($shortcode) || empty($passkey)) {
            throw new RuntimeException('MPesa shortcode or passkey is not configured. Please check your .env file.');
        }
        
        if (in_array($shortcode, ['dummy_shortcode', 'your_shortcode_here']) || 
            in_array($passkey, ['dummy_passkey', 'your_passkey_here'])) {
            throw new RuntimeException('MPesa shortcode or passkey appear to be placeholder values. Please configure real Safaricom API credentials.');
        }

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
