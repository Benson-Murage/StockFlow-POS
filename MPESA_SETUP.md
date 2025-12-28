# MPesa Integration Setup Guide

## Overview

StockFlowPOS includes MPesa payment integration to allow customers to pay for purchases using M-Pesa mobile money. This guide will walk you through setting up MPesa integration properly.

## The Problem

If you encounter the error `{"error":"Unable to obtain MPesa access token."}`, it means your MPesa credentials are either:
1. Not configured in your `.env` file
2. Using placeholder/dummy values
3. Invalid or expired credentials

## Solution

### Step 1: Get MPesa API Credentials

1. **Register at Safaricom Developer Portal**
   - Visit [developer.safaricom.co.ke](https://developer.safaricom.co.ke)
   - Create an account or log in if you already have one

2. **Create an App**
   - Go to "My Apps" section
   - Click "Create App"
   - Choose "MPesa" API
   - Fill in your business details

3. **Get Your Credentials**
   - Copy the following from your app:
     - **Consumer Key**
     - **Consumer Secret**
     - **Shortcode** (Business Paybill/Till Number)
     - **Passkey** (for STK Push)

### Step 2: Configure Environment Variables

Update your `.env` file with the real credentials:

```env
# MPesa Configuration
MPESA_CONSUMER_KEY=your_real_consumer_key_here
MPESA_CONSUMER_SECRET=your_real_consumer_secret_here
MPESA_SHORTCODE=your_real_shortcode_here
MPESA_PASSKEY=your_real_passkey_here
MPESA_ENV=sandbox  # Change to 'production' when going live
MPESA_CALLBACK_URL=${APP_URL}/api/payments/mpesa/callback
MPESA_TIMEOUT=30
```

**Important Notes:**
- Replace `your_real_*_here` with actual values from Safaricom Developer Portal
- Use `sandbox` for testing, `production` for live transactions
- The `APP_URL` will be automatically substituted

### Step 3: Verify Configuration

The system now includes improved validation that will:
- ✅ Check if credentials are not empty
- ✅ Detect dummy/placeholder values
- ✅ Provide clear error messages
- ✅ Show proper configuration status in the POS interface

### Step 4: Test the Integration

1. **In Sandbox Mode:**
   - Use Safaricom test numbers provided in the developer portal
   - Test with small amounts (e.g., KES 1)

2. **In Production Mode:**
   - Use real phone numbers and amounts
   - Monitor logs for any issues

## Environment Variables Reference

| Variable | Description | Example |
|----------|-------------|---------|
| `MPESA_CONSUMER_KEY` | Your app's consumer key | `abcd1234efgh5678ijkl9012mnop3456` |
| `MPESA_CONSUMER_SECRET` | Your app's consumer secret | `xyz7890abc123def456ghi789jkl012` |
| `MPESA_SHORTCODE` | Your business shortcode | `174379` |
| `MPESA_PASSKEY` | MPesa API passkey | `bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ad2` |
| `MPESA_ENV` | Environment (sandbox/production) | `sandbox` |
| `MPESA_CALLBACK_URL` | Webhook URL for payment notifications | `${APP_URL}/api/payments/mpesa/callback` |
| `MPESA_TIMEOUT` | Request timeout in seconds | `30` |

## Error Messages and Solutions

### "MPesa credentials are not configured"
**Cause:** Missing environment variables  
**Solution:** Add all required MPesa variables to `.env` file

### "MPesa credentials appear to be placeholder values"
**Cause:** Using dummy values like `dummy_key`, `your_consumer_key_here`  
**Solution:** Replace with real credentials from Safaricom Developer Portal

### "MPesa authentication failed. Please check your consumer key and secret"
**Cause:** Invalid consumer key or secret  
**Solution:** Verify credentials in Safaricom Developer Portal

### "MPesa access forbidden. Please check your API permissions"
**Cause:** App doesn't have proper permissions  
**Solution:** Ensure your app has STK Push permissions enabled

## Security Best Practices

1. **Never commit `.env` file to version control**
2. **Use different credentials for sandbox and production**
3. **Regularly rotate your API credentials**
4. **Monitor API usage and transactions**
5. **Keep your passkey secure**

## Troubleshooting

### Check Configuration Status
The POS interface will show MPesa configuration status:
- ✅ **MPesa Enabled & Configured**: Ready for payments
- ❌ **MPesa Not Configured**: Check your credentials

### View Logs
Check Laravel logs for detailed error information:
```bash
tail -f storage/logs/laravel.log
```

### Test API Connection
You can test your MPesa connection manually:
```php
use App\Services\MpesaService;

try {
    $mpesa = new MpesaService();
    // This will attempt to get access token
    $token = $mpesa->accessToken();
    echo "MPesa connection successful!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Support

If you continue to experience issues:

1. **Verify your Safaricom Developer Portal account status**
2. **Check that your app has the required permissions**
3. **Ensure you're using the correct environment (sandbox/production)**
4. **Review the Laravel logs for detailed error information**
5. **Contact Safaricom support if API credentials are not working**

## Moving to Production

When you're ready to go live:

1. **Change `MPESA_ENV` to `production`**
2. **Use production credentials**
3. **Update your callback URL if needed**
4. **Test with a small amount first**
5. **Monitor transactions closely**

Remember: Production transactions use real money!