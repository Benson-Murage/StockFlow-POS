<?php

/**
 * Test script to verify MPesa OAuth credentials
 * Run: php test_mpesa_oauth.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$consumerKey = config('mpesa.consumer_key');
$consumerSecret = config('mpesa.consumer_secret');
$environment = config('mpesa.environment', 'sandbox');
$baseUrl = $environment === 'production' 
    ? 'https://api.safaricom.co.ke' 
    : 'https://sandbox.safaricom.co.ke';

echo "=== MPesa OAuth Test ===\n";
echo "Environment: {$environment}\n";
echo "Base URL: {$baseUrl}\n";
echo "Consumer Key: " . substr($consumerKey, 0, 20) . "...\n";
echo "Consumer Secret: " . substr($consumerSecret, 0, 20) . "...\n\n";

$oauthUrl = $baseUrl . '/oauth/v1/generate?grant_type=client_credentials';

echo "Testing OAuth endpoint: {$oauthUrl}\n\n";

try {
    $response = \Illuminate\Support\Facades\Http::timeout(60)
        ->connectTimeout(30)
        ->withHeaders([
            'Accept' => 'application/json',
        ])
        ->withBasicAuth($consumerKey, $consumerSecret)
        ->get($oauthUrl);
    
    echo "Status Code: " . $response->status() . "\n";
    echo "Response Body:\n";
    echo $response->body() . "\n\n";
    
    if ($response->successful()) {
        $data = $response->json();
        if (isset($data['access_token'])) {
            echo "✅ SUCCESS! Access token obtained.\n";
            echo "Token (first 20 chars): " . substr($data['access_token'], 0, 20) . "...\n";
        } else {
            echo "❌ ERROR: No access_token in response\n";
            print_r($data);
        }
    } else {
        echo "❌ ERROR: Request failed\n";
        $errorData = $response->json();
        if ($errorData) {
            echo "Error Details:\n";
            print_r($errorData);
        }
    }
} catch (\Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo "Exception Type: " . get_class($e) . "\n";
}

