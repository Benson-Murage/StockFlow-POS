# Files to Upload to InfinityFree

## ✅ Required Files & Folders

Upload ALL of these to `htdocs/` folder:

### Core Application
- ✅ `app/` - Application code
- ✅ `bootstrap/` - Bootstrap files
- ✅ `config/` - Configuration files
- ✅ `database/` - Migrations and seeders
- ✅ `public/` - Public web root (IMPORTANT!)
- ✅ `resources/` - Views and assets
- ✅ `routes/` - Route definitions
- ✅ `storage/` - Storage directory (must be writable)
- ✅ `vendor/` - Composer dependencies (REQUIRED!)

### Root Files
- ✅ `.htaccess` - Root rewrite rules
- ✅ `.htaccess.infinityfree` - Alternative .htaccess (rename to .htaccess if needed)
- ✅ `artisan` - Laravel command-line tool
- ✅ `composer.json` - PHP dependencies
- ✅ `composer.lock` - Locked PHP dependencies
- ✅ `package.json` - Node dependencies (for reference)
- ✅ `vite.config.js` - Vite configuration

### Configuration
- ✅ `.env` - Environment configuration (create this on server)
- ✅ `.env.infinityfree.example` - Template (for reference)

## ❌ Files/Folders to EXCLUDE (Optional - saves space)

These can be excluded to save space and reduce file count:

### Development Files
- ❌ `node_modules/` - Node dependencies (not needed, assets already built)
- ❌ `.git/` - Git repository (not needed on server)
- ❌ `tests/` - Test files (optional)
- ❌ `.github/` - GitHub workflows (optional)

### Development Documentation
- ❌ `*.md` files (except this one) - Documentation files
- ❌ `Dockerfile*` - Docker files (not needed)
- ❌ `docker-compose.yml` - Docker compose (not needed)
- ❌ `phpunit.xml` - PHPUnit config (optional)

### Build Files (if not needed)
- ❌ `package-lock.json` - Not needed if assets are built
- ❌ `tailwind.config.js` - Not needed if assets are built
- ❌ `jsconfig.json` - Not needed if assets are built

## 📦 Recommended Upload Method

### Method 1: Selective Upload (Recommended)
1. Create a new folder locally
2. Copy only required files/folders listed above
3. Zip the folder
4. Upload ZIP to InfinityFree
5. Extract in `htdocs/`

### Method 2: Full Upload
1. Zip entire project
2. Upload to InfinityFree
3. Extract in `htdocs/`
4. Delete unnecessary files via File Manager

## 📋 Pre-Upload Checklist

Before uploading, ensure:

- [ ] `npm run build` has been executed
- [ ] `public/build/` folder exists with files
- [ ] `composer install --no-dev` has been run
- [ ] `vendor/` folder exists
- [ ] `.env` file is ready (or will be created on server)
- [ ] Storage permissions are set (755 or 777)

## 🔍 Verify After Upload

After uploading, verify these exist in `htdocs/`:

```
htdocs/
├── app/                    ✅
├── bootstrap/              ✅
├── config/                 ✅
├── database/               ✅
├── public/                 ✅
│   ├── index.php          ✅
│   ├── .htaccess          ✅
│   └── build/             ✅ (IMPORTANT - contains built assets)
├── resources/              ✅
├── routes/                 ✅
├── storage/                ✅
├── vendor/                 ✅ (REQUIRED!)
├── .env                    ✅ (create this)
├── .htaccess               ✅
└── artisan                 ✅
```

## ⚠️ Important Notes

1. **vendor/ folder is REQUIRED** - Don't skip this! Laravel needs it to run.

2. **public/build/ folder is REQUIRED** - Contains your compiled CSS/JS assets.

3. **storage/ must be writable** - Set permissions to 755 or 777.

4. **.env file** - Create this on the server with your database credentials.

5. **File Count Limit** - InfinityFree has ~30,000 file limit. Excluding `node_modules/` and `.git/` helps stay under limit.

## 🚀 Quick Upload Script (Optional)

If you want to create a clean upload package:

```bash
# Create upload directory
mkdir stockflowpos-upload

# Copy required files
cp -r app bootstrap config database public resources routes storage vendor stockflowpos-upload/
cp artisan composer.json composer.lock .htaccess stockflowpos-upload/

# Create .env template
cp .env.infinityfree.example stockflowpos-upload/.env.example

# Zip it
zip -r stockflowpos-upload.zip stockflowpos-upload/

# Upload stockflowpos-upload.zip to InfinityFree
```

---

**Total Size Estimate:**
- With vendor/: ~50-100 MB (depending on dependencies)
- Without node_modules/: Saves ~200-500 MB
- Recommended: Upload everything except node_modules/ and .git/
