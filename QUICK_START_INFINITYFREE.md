# Quick Start Guide - InfinityFree Deployment

## 🚀 Fast Deployment (5 Minutes)

### Step 1: Prepare Files (Do this locally)

```bash
# Build production assets
npm run build

# Install production dependencies
composer install --no-dev --optimize-autoloader
```

### Step 2: Create Database in InfinityFree

1. Log in to InfinityFree control panel
2. Go to **MySQL Databases** → **Create Database**
3. Save these details:
   - Database Name: `epiz_XXXXX_stockflow`
   - Username: `epiz_XXXXX_stockflow`
   - Password: `[your password]`
   - Host: `sqlXXX.infinityfree.com` (or `localhost`)

### Step 3: Upload Files

**Option A: FTP Upload**
1. Connect via FTP to your InfinityFree account
2. Upload ALL files to `htdocs/` folder
3. Maintain folder structure

**Option B: File Manager**
1. Zip your project folder
2. Upload ZIP to InfinityFree File Manager
3. Extract in `htdocs/` folder

### Step 4: Configure Environment

1. In `htdocs/` folder, create `.env` file
2. Copy this template and fill in your values:

```env
APP_NAME="StockFlowPOS"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.infinityfreeapp.com

DB_CONNECTION=mysql
DB_HOST=sqlXXX.infinityfree.com
DB_PORT=3306
DB_DATABASE=epiz_XXXXX_stockflow
DB_USERNAME=epiz_XXXXX_stockflow
DB_PASSWORD=your_password

SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database
```

### Step 5: Set Permissions

Set these folders to **755** (or 777 if 755 doesn't work):
- `storage/` and all subfolders
- `bootstrap/cache/`

**Via File Manager:**
- Right-click folder → Change Permissions → 755

**Via FTP:**
- CHMOD 755 for folders
- CHMOD 644 for files

### Step 6: Install via Web Interface

1. Visit: `https://yourdomain.infinityfreeapp.com/install`
2. Follow the installer:
   - ✅ Check requirements
   - ✅ Configure database (use the credentials from Step 2)
   - ✅ Set application settings
   - ✅ Create store
   - ✅ Create admin user
3. Complete installation

### Step 7: Final Steps

1. **Create Storage Link:**
   - Visit: `https://yourdomain.infinityfreeapp.com/link-storage`

2. **Clear Cache:**
   - Visit: `https://yourdomain.infinityfreeapp.com/clear-cache`

3. **Test Login:**
   - Visit: `https://yourdomain.infinityfreeapp.com/login`
   - Login with admin credentials

## ✅ Done!

Your StockFlowPOS is now live! 🎉

## 🔧 Common Issues

### "No application encryption key"
→ Use the installer at `/install` - it will generate the key automatically

### "Database connection failed"
→ Check your database host (might be `localhost` instead of `sqlXXX.infinityfree.com`)

### "Permission denied"
→ Set `storage/` folder to 755 or 777

### "500 Internal Server Error"
→ Check `.env` file exists and has correct values
→ Check error logs in `storage/logs/laravel.log`

## 📞 Need Help?

1. Check `INFINITYFREE_DEPLOYMENT.md` for detailed guide
2. Check `DEPLOYMENT_CHECKLIST.md` for step-by-step checklist
3. Review error logs in `storage/logs/`

---

**Pro Tip:** Enable SSL in InfinityFree control panel and update `APP_URL` to use `https://`
