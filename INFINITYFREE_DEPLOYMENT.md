# InfinityFree Deployment Guide for StockFlowPOS

This guide will help you deploy StockFlowPOS to InfinityFree free hosting.

## Prerequisites

1. **InfinityFree Account**: Sign up at [infinityfree.net](https://www.infinityfree.net)
2. **Domain or Subdomain**: You'll get a free subdomain (e.g., `yourname.infinityfreeapp.com`)
3. **Database**: Create a MySQL database in InfinityFree control panel
4. **FTP Client**: Use FileZilla or any FTP client to upload files

## InfinityFree Limitations

- **PHP Version**: 8.3 (compatible with Laravel 11)
- **File Size Limit**: 1MB per PHP file
- **Inode Limit**: ~30,000 files
- **No SSH Access**: All operations must be done via web interface or FTP
- **No Command Line**: Migrations must be run via web interface

## Step-by-Step Deployment

### Step 1: Prepare Your Files Locally

1. **Build Production Assets** (Already done):
   ```bash
   npm run build
   ```

2. **Install Dependencies** (if not done):
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

### Step 2: Create Database in InfinityFree

1. Log in to InfinityFree control panel
2. Go to **MySQL Databases**
3. Create a new database:
   - Note the database name (e.g., `epiz_12345678_stockflow`)
   - Note the username (usually same as database name)
   - Note the password (save it securely)
   - Note the host (usually `sqlXXX.infinityfree.com` or `localhost`)

### Step 3: Upload Files to InfinityFree

**Important**: InfinityFree uses `htdocs` as the web root directory.

#### Option A: Direct Upload (Recommended for first time)

1. Connect via FTP to your InfinityFree account
2. Navigate to `htdocs` folder
3. Upload ALL files from your project to `htdocs`
4. **Important**: The `public` folder contents should NOT be moved - Laravel will handle routing

#### Option B: Using File Manager

1. Log in to InfinityFree control panel
2. Go to **File Manager**
3. Upload a ZIP file of your project
4. Extract it in `htdocs` folder

### Step 4: Configure Environment File

1. In `htdocs` folder, create a `.env` file (copy from `.env.example` if available)
2. Configure the following settings:

```env
APP_NAME="StockFlowPOS"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.infinityfreeapp.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=sqlXXX.infinityfree.com
DB_PORT=3306
DB_DATABASE=epiz_12345678_stockflow
DB_USERNAME=epiz_12345678_stockflow
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# MPesa Configuration (if using)
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=your_shortcode
MPESA_PASSKEY=your_passkey
MPESA_ENV=sandbox
MPESA_CALLBACK_URL=${APP_URL}/api/payments/mpesa/callback
MPESA_TIMEOUT=30
```

**Important**: 
- Replace `YOUR_APP_KEY_HERE` with a generated key (see Step 5)
- Replace database credentials with your actual InfinityFree database details
- Replace `yourdomain.infinityfreeapp.com` with your actual domain

### Step 5: Generate Application Key

Since you don't have SSH access, you can generate the key using one of these methods:

#### Method 1: Using Web Interface (Recommended)

1. Visit: `https://yourdomain.infinityfreeapp.com/install`
2. Follow the installer wizard
3. The installer will generate the APP_KEY automatically

#### Method 2: Using Online Tool

1. Visit: `https://laravel-key-generator.com/`
2. Generate a key
3. Copy it to your `.env` file

#### Method 3: Local Generation

If you have Laravel installed locally:
```bash
php artisan key:generate --show
```
Copy the output to your `.env` file.

### Step 6: Set File Permissions

Set the following folders to be writable (755 or 777):

- `storage/` (and all subfolders)
- `bootstrap/cache/`

**Via File Manager:**
1. Right-click on `storage` folder
2. Select "Change Permissions"
3. Set to `755` or `777`

**Via FTP:**
- Use your FTP client to change permissions (CHMOD)

### Step 7: Run Database Migrations

Since InfinityFree doesn't provide SSH access, use the web-based installer:

1. Visit: `https://yourdomain.infinityfreeapp.com/install`
2. Complete the installation wizard:
   - Step 1: Check requirements
   - Step 2: Configure database
   - Step 3: Run migrations
   - Step 4: Create admin user
   - Step 5: Complete installation

**Alternative**: Use the migration runner at `/api/maintenance/database/migrate` (requires authentication)

### Step 8: Create Storage Link

1. Visit: `https://yourdomain.infinityfreeapp.com/link-storage` (while logged in as admin)
2. This will create the symbolic link for storage

**Note**: If symbolic links don't work on InfinityFree, you may need to manually copy files or use a different approach.

### Step 9: Clear and Cache Configuration

Visit these URLs to optimize your application:

1. `https://yourdomain.infinityfreeapp.com/clear-cache` (while logged in)
2. This will clear all caches and optimize the application

### Step 10: Test Your Application

1. Visit your domain: `https://yourdomain.infinityfreeapp.com`
2. You should be redirected to the login page
3. Log in with the admin credentials you created during installation

## Troubleshooting

### Error: "No application encryption key has been specified"

**Solution**: Generate and add `APP_KEY` to your `.env` file (see Step 5)

### Error: "SQLSTATE[HY000] [2002] Connection refused"

**Solution**: 
- Check your database host (might be `localhost` instead of `sqlXXX.infinityfree.com`)
- Verify database credentials in `.env`
- Ensure database is created in InfinityFree control panel

### Error: "The stream or file could not be opened"

**Solution**: 
- Check `storage/` folder permissions (should be 755 or 777)
- Ensure all subfolders in `storage/` are writable

### Error: "500 Internal Server Error"

**Solution**:
1. Check `.env` file exists and is configured correctly
2. Check file permissions on `storage/` and `bootstrap/cache/`
3. Check error logs in InfinityFree control panel
4. Enable `APP_DEBUG=true` temporarily to see detailed errors (remember to disable after fixing)

### Error: "Route not found" or "404 Not Found"

**Solution**:
- Ensure `.htaccess` file exists in `htdocs` folder
- Check that `public/index.php` is accessible
- Verify mod_rewrite is enabled (should be by default on InfinityFree)

### Assets Not Loading (CSS/JS)

**Solution**:
1. Ensure `npm run build` was run before deployment
2. Check that `public/build/` folder exists and contains files
3. Clear browser cache
4. Check `APP_URL` in `.env` matches your actual domain

### Migration Errors

**Solution**:
1. Use the web installer at `/install`
2. Or manually run migrations via `/api/maintenance/database/migrate` (requires auth)
3. Check database connection first

## Post-Deployment Optimization

### 1. Enable Caching

After successful deployment, Laravel will automatically cache:
- Configuration
- Routes
- Views

These are cached automatically when you visit `/clear-cache` while logged in.

### 2. Disable Debug Mode

Ensure `APP_DEBUG=false` in production for security.

### 3. Set Up SSL

InfinityFree provides free SSL certificates:
1. Go to InfinityFree control panel
2. Navigate to SSL/TLS settings
3. Enable free SSL certificate
4. Update `APP_URL` in `.env` to use `https://`

### 4. Regular Backups

Use the built-in backup feature:
- Navigate to Settings > Backups
- Schedule automatic backups
- Download backups regularly

## File Structure on InfinityFree

```
htdocs/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          (This is your web root)
│   ├── index.php
│   ├── .htaccess
│   └── build/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── .htaccess        (Root .htaccess redirects to public/)
├── artisan
├── composer.json
└── package.json
```

## Important Notes

1. **Never commit `.env` file** - It contains sensitive information
2. **Keep `APP_DEBUG=false`** in production
3. **Regular backups** - Use the built-in backup system
4. **File upload limits** - InfinityFree has file size limits, be aware when uploading products
5. **Performance** - Free hosting has resource limits, optimize images and assets

## Support

If you encounter issues:
1. Check InfinityFree status page
2. Review Laravel logs in `storage/logs/`
3. Check InfinityFree error logs in control panel
4. Visit the installer at `/install` to verify configuration

## Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Strong `APP_KEY` generated
- [ ] Database credentials are secure
- [ ] `.env` file is not publicly accessible
- [ ] File permissions are set correctly (755 for folders, 644 for files)
- [ ] SSL certificate is enabled
- [ ] Admin password is strong
- [ ] Regular backups are configured

---

**Congratulations!** Your StockFlowPOS application should now be live on InfinityFree! 🎉
