# Render Deployment Guide for StockFlowPOS

Complete guide for deploying StockFlowPOS to Render cloud platform.

## 📋 Overview

Render is a modern cloud platform that makes it easy to deploy Laravel applications. This guide covers deploying StockFlowPOS with:
- **Laravel 11** backend
- **React 19** frontend with Inertia.js
- **MySQL** database
- **Queue workers** for background jobs

## 🚀 Quick Start

1. **Connect Repository** to Render
2. **Create Database** in Render dashboard
3. **Set Environment Variables**
4. **Deploy** - Render will auto-detect `render.yaml`

For detailed steps, see [Quick Start Guide](#quick-start-guide) below.

## 📦 Prerequisites

- Render account (sign up at [render.com](https://render.com))
- GitHub/GitLab/Bitbucket repository with your code
- Basic understanding of Laravel and environment variables

## 🏗️ Architecture

Your deployment will include:

1. **Web Service** - Main Laravel application
2. **Worker Service** - Background queue processing (optional)
3. **MySQL Database** - Persistent data storage

## 📝 Step-by-Step Deployment

### Step 1: Prepare Your Repository

Ensure your repository has:
- ✅ `render.yaml` file (already included)
- ✅ `composer.json` and `package.json`
- ✅ All application code
- ✅ `.env.example` or environment documentation

### Step 2: Connect Repository to Render

1. Log in to [Render Dashboard](https://dashboard.render.com)
2. Click **"New +"** → **"Blueprint"**
3. Connect your Git repository
4. Render will detect `render.yaml` automatically
5. Click **"Apply"** to create services

**Alternative: Manual Setup**
1. Click **"New +"** → **"Web Service"**
2. Connect your repository
3. Render will auto-detect Laravel
4. Configure manually (see below)

### Step 3: Configure Database

#### Option A: Using render.yaml (Recommended)

The `render.yaml` file will automatically create a database. After deployment:

1. Go to your **Web Service** → **Environment**
2. Add database connection variables (see Step 4)

#### Option B: Manual Database Creation

1. In Render Dashboard, click **"New +"** → **"PostgreSQL"** or **"MySQL"**
2. Choose:
   - **Name**: `stockflowpos-db`
   - **Database**: `stockflowpos`
   - **User**: `stockflowpos`
   - **Plan**: Starter (or higher for production)
   - **Region**: Same as your web service
3. Click **"Create Database"**
4. Note the connection details

### Step 4: Configure Environment Variables

Go to your **Web Service** → **Environment** and add:

#### Required Variables

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-generated-key-here
APP_URL=https://your-service.onrender.com

DB_CONNECTION=mysql
DB_HOST=your-db-host.internal
DB_PORT=3306
DB_DATABASE=stockflowpos
DB_USERNAME=stockflowpos
DB_PASSWORD=your-db-password

SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

#### Database Connection (Auto-inject from Database)

If using `fromDatabase` in `render.yaml`, these are auto-injected. Otherwise, set manually:

1. Go to your **Database** → **"Info"** tab
2. Copy connection details
3. Add to Web Service environment variables

#### Application Key

Generate `APP_KEY`:

**Option 1: Via Render Shell**
```bash
# In Render dashboard, open Shell for your service
php artisan key:generate --show
# Copy the output and add to APP_KEY
```

**Option 2: Locally**
```bash
php artisan key:generate --show
# Copy output to Render environment variables
```

**Option 3: Let Render Generate**
- Leave `APP_KEY` empty initially
- Render will generate during first build
- Copy from build logs and add to environment

#### Optional Variables

```env
# Application
APP_TIMEZONE=UTC
LOG_CHANNEL=stack
LOG_LEVEL=error

# Sanctum (for API authentication)
SANCTUM_STATEFUL_DOMAINS=your-service.onrender.com,localhost

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=StockFlowPOS

# MPesa (if using)
MPESA_CONSUMER_KEY=your-key
MPESA_CONSUMER_SECRET=your-secret
MPESA_SHORTCODE=your-shortcode
MPESA_PASSKEY=your-passkey
MPESA_ENV=production
MPESA_CALLBACK_URL=https://your-service.onrender.com/api/payments/mpesa/callback
```

### Step 5: Configure Build Settings

If using `render.yaml`, these are already configured. For manual setup:

#### Build Command
```bash
composer install --no-dev --optimize-autoloader --no-interaction && \
npm ci --only=production && \
npm run build && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
php artisan optimize
```

#### Start Command
```bash
php artisan serve --host=0.0.0.0 --port=$PORT
```

#### Health Check Path
```
/health
```

### Step 6: Run Database Migrations

#### Option 1: Via Render Shell (Recommended)

1. Go to your **Web Service** → **Shell**
2. Run:
```bash
php artisan migrate --force
php artisan db:seed  # If you have seeders
```

#### Option 2: Via Build Command

Add to `buildCommand` in `render.yaml`:
```bash
php artisan migrate --force
```

**Note**: This runs on every deploy. Use only if you want auto-migrations.

#### Option 3: Via Web Installer

1. Visit: `https://your-service.onrender.com/install`
2. Follow the installation wizard
3. It will run migrations automatically

### Step 7: Create Storage Link

1. Go to **Web Service** → **Shell**
2. Run:
```bash
php artisan storage:link
```

Or visit: `https://your-service.onrender.com/link-storage` (while logged in)

### Step 8: Deploy

1. **If using Blueprint**: Click **"Apply"** in Blueprint view
2. **If manual setup**: Click **"Manual Deploy"** → **"Deploy latest commit"**
3. Wait for build to complete (5-10 minutes)
4. Check build logs for errors

### Step 9: Verify Deployment

1. Visit your service URL: `https://your-service.onrender.com`
2. Should redirect to `/login` or `/install`
3. If installer appears, complete setup
4. Test login functionality

## 🔧 Advanced Configuration

### Using Docker (Alternative)

To use Docker instead of native PHP:

1. Edit `render.yaml`
2. Change `env: php` to `env: docker`
3. Ensure `Dockerfile` exists in repository
4. Deploy

**Note**: Docker builds are slower but offer more control.

### Setting Up Queue Worker

The `render.yaml` includes a worker service. To enable:

1. **If using Blueprint**: Worker is created automatically
2. **If manual**: Create new **Worker Service**
3. Use same environment variables as web service
4. Start command: `php artisan queue:work --tries=3 --timeout=300`

### Using Redis (Optional)

1. Create **Redis** instance in Render
2. Add to environment variables:
```env
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
REDIS_PORT=6379
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Custom Domain

1. Go to **Web Service** → **Settings** → **Custom Domains**
2. Add your domain
3. Update DNS records as instructed
4. Update `APP_URL` and `SANCTUM_STATEFUL_DOMAINS`

### SSL Certificate

Render provides free SSL certificates automatically for:
- `*.onrender.com` domains
- Custom domains (after DNS configuration)

## 🐛 Troubleshooting

### Build Fails

**Error: "composer install failed"**
- Check PHP version (should be 8.2+)
- Verify `composer.json` is valid
- Check build logs for specific error

**Error: "npm install failed"**
- Check Node.js version (should be 18+)
- Verify `package.json` is valid
- Clear npm cache: Add `npm cache clean --force` to build command

**Error: "npm run build failed"**
- Ensure all dependencies are in `package.json`
- Check for build errors in logs
- Verify `vite.config.js` is correct

### Application Errors

**Error: "No application encryption key"**
- Generate `APP_KEY` (see Step 4)
- Add to environment variables
- Redeploy

**Error: "Database connection failed"**
- Verify database credentials
- Check database is running
- Ensure database and web service are in same region
- For internal connections, use `*.internal` hostname

**Error: "500 Internal Server Error"**
- Check application logs: **Web Service** → **Logs**
- Enable `APP_DEBUG=true` temporarily
- Check `storage/logs/laravel.log` via Shell

**Error: "Route not found"**
- Run: `php artisan route:clear` in Shell
- Run: `php artisan route:cache` in Shell
- Redeploy

### Performance Issues

**Slow Build Times**
- Use native PHP buildpack instead of Docker
- Optimize `composer.json` (remove dev dependencies)
- Use `npm ci` instead of `npm install`

**Slow Application**
- Enable caching (already configured)
- Use Redis for cache/sessions
- Upgrade to higher plan
- Optimize database queries

### Storage Issues

**Files Not Persisting**
- Render has ephemeral filesystem
- Use external storage (S3, etc.) for uploads
- Or use Render's persistent disk (paid feature)

**Storage Link Not Working**
- Run `php artisan storage:link` in Shell
- Check `public/storage` symlink exists
- For persistent storage, consider S3

## 📊 Monitoring

### View Logs

1. **Web Service** → **Logs** - Real-time application logs
2. **Shell** → Access Laravel logs: `tail -f storage/logs/laravel.log`

### Health Checks

- Health endpoint: `/health`
- Render monitors automatically
- Check **Metrics** tab for uptime

### Database Monitoring

- **Database** → **Metrics** - Connection stats
- **Database** → **Logs** - Query logs

## 🔒 Security Best Practices

1. **Never commit `.env`** - Use environment variables
2. **Use strong `APP_KEY`** - Generate securely
3. **Set `APP_DEBUG=false`** in production
4. **Use HTTPS** - Render provides free SSL
5. **Secure database** - Use strong passwords
6. **Limit access** - Use Render's IP restrictions if needed
7. **Regular updates** - Keep dependencies updated

## 💰 Cost Optimization

### Free Tier Limits

- **Web Services**: Spins down after 15 min inactivity
- **Databases**: 90 days free trial
- **Bandwidth**: Limited on free tier

### Paid Plans

- **Starter**: $7/month - Always on, better performance
- **Standard**: $25/month - More resources
- **Pro**: Custom pricing - Enterprise features

### Tips to Reduce Costs

1. Use **Starter** plan for production
2. Combine services in same region
3. Optimize build times
4. Use efficient caching
5. Monitor resource usage

## 🔄 Continuous Deployment

Render supports automatic deployments:

1. **Auto Deploy**: Enabled by default
2. **Manual Deploy**: Deploy specific commits
3. **Preview Deployments**: Test before production

### Branch Deployments

- **Production**: Deploy from `main` branch
- **Staging**: Deploy from `staging` branch
- **Preview**: Deploy from PR branches

## 📚 Additional Resources

- [Render Documentation](https://render.com/docs)
- [Laravel on Render](https://render.com/docs/deploy-laravel)
- [Render Community](https://community.render.com)

## ✅ Deployment Checklist

- [ ] Repository connected to Render
- [ ] Database created and configured
- [ ] Environment variables set
- [ ] `APP_KEY` generated
- [ ] Build settings configured
- [ ] Database migrations run
- [ ] Storage link created
- [ ] Application accessible
- [ ] Health check passing
- [ ] SSL certificate active
- [ ] Custom domain configured (if applicable)
- [ ] Queue worker running (if needed)
- [ ] Monitoring set up
- [ ] Backups configured

## 🎉 Success!

Your StockFlowPOS application is now live on Render!

**Next Steps:**
1. Complete installation via `/install` if needed
2. Set up regular backups
3. Configure monitoring
4. Optimize performance
5. Set up custom domain (optional)

---

**Need Help?**
- Check Render logs
- Review Laravel logs
- Consult Render documentation
- Check application health endpoint
