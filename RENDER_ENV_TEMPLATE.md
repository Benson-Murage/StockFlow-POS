# Render Environment Variables Template

Copy these environment variables to your Render Web Service → Environment section.

## 🔴 Required Variables

```env
# Application
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_URL=https://your-service.onrender.com
APP_TIMEZONE=UTC

# Database (Auto-injected if using fromDatabase in render.yaml)
DB_CONNECTION=mysql
DB_HOST=your-db-host.internal
DB_PORT=3306
DB_DATABASE=stockflowpos
DB_USERNAME=stockflowpos
DB_PASSWORD=your-database-password

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_DRIVER=database
QUEUE_CONNECTION=database
BROADCAST_DRIVER=log
FILESYSTEM_DISK=local

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

## 🟡 Recommended Variables

```env
# Sanctum (for API authentication)
SANCTUM_STATEFUL_DOMAINS=your-service.onrender.com,localhost,127.0.0.1

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mail-username
MAIL_PASSWORD=your-mail-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=StockFlowPOS
```

## 🟢 Optional Variables

```env
# MPesa Configuration (if using)
MPESA_CONSUMER_KEY=your-consumer-key
MPESA_CONSUMER_SECRET=your-consumer-secret
MPESA_SHORTCODE=your-shortcode
MPESA_PASSKEY=your-passkey
MPESA_ENV=production
MPESA_CALLBACK_URL=https://your-service.onrender.com/api/payments/mpesa/callback
MPESA_TIMEOUT=30

# Redis (if using Redis cache)
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
REDIS_PORT=6379
# Then update:
# CACHE_DRIVER=redis
# SESSION_DRIVER=redis
# QUEUE_CONNECTION=redis

# AWS S3 (if using S3 for file storage)
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_USE_PATH_STYLE_ENDPOINT=false
# Then update:
# FILESYSTEM_DISK=s3
```

## 📝 How to Set Variables in Render

### Method 1: Via Dashboard

1. Go to your **Web Service** → **Environment**
2. Click **"Add Environment Variable"**
3. Enter **Key** and **Value**
4. Click **"Save Changes"**
5. Service will auto-redeploy

### Method 2: Using fromDatabase (Recommended)

In `render.yaml`, use `fromDatabase` to auto-inject database credentials:

```yaml
envVars:
  - key: DB_HOST
    fromDatabase:
      name: stockflowpos-db
      property: host
  - key: DB_PASSWORD
    fromDatabase:
      name: stockflowpos-db
      property: password
```

This automatically syncs database credentials!

### Method 3: Using sync: false

For variables that should be set manually:

```yaml
envVars:
  - key: APP_KEY
    sync: false  # Must be set manually
  - key: APP_URL
    sync: false  # Auto-set by Render
```

## 🔑 Generating APP_KEY

### Option 1: Via Render Shell (Recommended)

1. Deploy your service first
2. Go to **Web Service** → **Shell**
3. Run:
```bash
php artisan key:generate --show
```
4. Copy the output
5. Add to `APP_KEY` environment variable
6. Redeploy

### Option 2: Locally

```bash
php artisan key:generate --show
```
Copy output to Render environment variables.

### Option 3: Let Render Generate

1. Leave `APP_KEY` empty initially
2. Add to build command: `php artisan key:generate --force`
3. Check build logs for generated key
4. Copy and add to environment variables

## 🔒 Security Notes

1. **Never commit** `.env` file to repository
2. **Use strong passwords** for database
3. **Rotate keys** periodically
4. **Use secrets** for sensitive data
5. **Limit access** to environment variables

## ✅ Verification

After setting variables:

1. Check **Environment** tab shows all variables
2. Verify no typos in variable names
3. Ensure values are correct
4. Service should auto-redeploy
5. Check logs for any errors

## 🐛 Common Issues

### Variable Not Working?

- Check spelling (case-sensitive)
- Verify value is correct
- Ensure no extra spaces
- Check if service redeployed

### Database Connection Fails?

- Verify `DB_HOST` uses `*.internal` for internal connections
- Check database is in same region
- Verify credentials match database
- Test connection via Shell

### APP_KEY Issues?

- Ensure key starts with `base64:`
- Verify key is complete (64 characters)
- Regenerate if unsure
- Clear config cache: `php artisan config:clear`

---

**Pro Tip**: Use `fromDatabase` in `render.yaml` to automatically sync database credentials!
