# InfinityFree Deployment Checklist

Use this checklist to ensure your StockFlowPOS deployment is complete and working.

## Pre-Deployment

- [ ] **Build Production Assets**
  ```bash
  npm run build
  ```
  ✅ Verify: Check that `public/build/` folder exists and contains files

- [ ] **Install Production Dependencies**
  ```bash
  composer install --no-dev --optimize-autoloader
  ```
  ✅ Verify: Check that `vendor/` folder exists

- [ ] **Remove Development Files** (Optional but recommended)
  - Remove `node_modules/` folder (saves space)
  - Remove `.git/` folder if present (saves space)
  - Remove test files if not needed

## InfinityFree Setup

- [ ] **Create Database**
  - Database name: ________________
  - Username: ________________
  - Password: ________________
  - Host: ________________

- [ ] **Note Your Domain**
  - Domain/Subdomain: ________________

## File Upload

- [ ] **Upload All Files to htdocs**
  - Use FTP client or File Manager
  - Upload entire project to `htdocs/` folder
  - Ensure folder structure is maintained

- [ ] **Verify File Structure**
  ```
  htdocs/
  ├── app/
  ├── bootstrap/
  ├── config/
  ├── database/
  ├── public/
  ├── resources/
  ├── routes/
  ├── storage/
  ├── vendor/
  ├── .env (create this)
  ├── .htaccess
  └── artisan
  ```

## Configuration

- [ ] **Create .env File**
  - Copy from `.env.infinityfree.example` or create new
  - Fill in all required values

- [ ] **Set Database Credentials**
  - DB_HOST: ________________
  - DB_DATABASE: ________________
  - DB_USERNAME: ________________
  - DB_PASSWORD: ________________

- [ ] **Set Application URL**
  - APP_URL: https://yourdomain.infinityfreeapp.com

- [ ] **Generate APP_KEY**
  - Use installer at `/install` OR
  - Generate locally and add to .env

## Permissions

- [ ] **Set Storage Permissions**
  - `storage/` folder: 755 or 777
  - `storage/app/`: 755 or 777
  - `storage/framework/`: 755 or 777
  - `storage/logs/`: 755 or 777
  - All subfolders: 755 or 777

- [ ] **Set Bootstrap Cache Permissions**
  - `bootstrap/cache/`: 755 or 777

## Installation

- [ ] **Run Web Installer**
  - Visit: `https://yourdomain.infinityfreeapp.com/install`
  - Complete all steps:
    - [ ] Requirements check
    - [ ] Database configuration
    - [ ] Application settings
    - [ ] Store setup
    - [ ] Admin user creation
    - [ ] Installation completion

- [ ] **Verify Installation**
  - Check that `storage/installed` file exists
  - Verify database tables were created

## Post-Installation

- [ ] **Create Storage Link**
  - Visit: `https://yourdomain.infinityfreeapp.com/link-storage`
  - Or manually create if symbolic links don't work

- [ ] **Clear Cache**
  - Visit: `https://yourdomain.infinityfreeapp.com/clear-cache`
  - Verify success message

- [ ] **Test Login**
  - Visit: `https://yourdomain.infinityfreeapp.com/login`
  - Login with admin credentials
  - Verify dashboard loads

## Security

- [ ] **Verify APP_DEBUG=false**
  - Check `.env` file
  - Should be `false` in production

- [ ] **Verify APP_KEY is Set**
  - Check `.env` file
  - Should be a long base64 string

- [ ] **Enable SSL**
  - Go to InfinityFree control panel
  - Enable free SSL certificate
  - Update APP_URL to use https://

- [ ] **Verify .env is Protected**
  - Check that `.env` is not publicly accessible
  - Should return 403 or 404 if accessed directly

## Functionality Tests

- [ ] **Test POS System**
  - Add products to cart
  - Complete a test sale
  - Verify receipt generation

- [ ] **Test Product Management**
  - Create a product
  - Upload product image
  - Verify image displays

- [ ] **Test Reports**
  - View sales report
  - View inventory report
  - Verify data accuracy

- [ ] **Test Backup System**
  - Navigate to Settings > Backups
  - Create a backup
  - Verify backup file is created

## Performance

- [ ] **Verify Assets Load**
  - Check browser console for errors
  - Verify CSS/JS files load correctly
  - Check network tab for 404 errors

- [ ] **Test Page Load Speed**
  - Use browser dev tools
  - Check load times
  - Optimize if needed

## Final Checks

- [ ] **Error Logs**
  - Check `storage/logs/laravel.log` for errors
  - Check InfinityFree error logs
  - Fix any critical errors

- [ ] **Database Connection**
  - Verify all database operations work
  - Test CRUD operations

- [ ] **File Uploads**
  - Test product image upload
  - Verify files save correctly
  - Check file permissions

- [ ] **Email Configuration** (if using)
  - Test email sending
  - Verify SMTP settings

## Backup Strategy

- [ ] **Set Up Regular Backups**
  - Configure automatic backups in app
  - Schedule regular database backups
  - Download backups periodically

## Documentation

- [ ] **Save Deployment Info**
  - Database credentials (store securely)
  - Admin credentials (store securely)
  - Domain information
  - FTP credentials (store securely)

---

## Troubleshooting Quick Reference

| Issue | Solution |
|-------|----------|
| 500 Error | Check .env, permissions, error logs |
| Database Connection Failed | Verify credentials, check host |
| Assets Not Loading | Run `npm run build`, check APP_URL |
| Migration Errors | Use installer or web migration runner |
| Permission Denied | Set storage/ to 755 or 777 |
| APP_KEY Missing | Generate via installer or online tool |

---

**Deployment Date**: ________________  
**Deployed By**: ________________  
**Domain**: ________________  
**Status**: ⬜ Complete ⬜ In Progress ⬜ Issues
