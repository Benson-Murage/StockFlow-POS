# Render Quick Start Guide - StockFlowPOS

Get your StockFlowPOS application deployed on Render in **10 minutes**!

## 🚀 5-Minute Deployment

### Step 1: Connect Repository (2 min)

1. Go to [render.com](https://render.com) and sign up/login
2. Click **"New +"** → **"Blueprint"**
3. Connect your GitHub/GitLab repository
4. Render will detect `render.yaml` automatically
5. Click **"Apply"**

### Step 2: Configure Database (1 min)

The `render.yaml` will create a database automatically. After services are created:

1. Go to **Web Service** → **Environment**
2. Database variables will be auto-injected (if using `fromDatabase`)
3. If not, copy from **Database** → **Info** tab

### Step 3: Set Environment Variables (2 min)

Go to **Web Service** → **Environment** and add:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=  # Leave empty, will be generated
APP_URL=  # Will be set automatically by Render

# Database (auto-injected if using fromDatabase)
DB_CONNECTION=mysql
# DB_HOST, DB_DATABASE, etc. will be auto-set
```

**Generate APP_KEY:**
1. After first deploy, go to **Web Service** → **Shell**
2. Run: `php artisan key:generate --show`
3. Copy output and add to `APP_KEY` environment variable
4. Redeploy

### Step 4: Deploy (5 min)

1. Click **"Manual Deploy"** → **"Deploy latest commit"**
2. Wait for build (5-10 minutes)
3. Check build logs for errors

### Step 5: Run Migrations (1 min)

1. Go to **Web Service** → **Shell**
2. Run:
```bash
php artisan migrate --force
php artisan storage:link
```

### Step 6: Access Your App

1. Visit your service URL: `https://your-service.onrender.com`
2. If installer appears, complete setup
3. Login and start using!

## ✅ Done!

Your app is live! 🎉

## 🔧 Quick Fixes

### Build Fails?
- Check build logs
- Verify PHP 8.2+ and Node.js 18+
- Ensure `composer.json` and `package.json` are valid

### App Not Working?
- Check environment variables
- Verify database connection
- Check application logs
- Enable `APP_DEBUG=true` temporarily to see errors

### Database Connection Error?
- Verify database credentials
- Ensure database and web service in same region
- Use `*.internal` hostname for internal connections

## 📚 Next Steps

- Read [RENDER_DEPLOYMENT.md](./RENDER_DEPLOYMENT.md) for detailed guide
- Set up custom domain
- Configure email
- Set up monitoring
- Enable queue worker

---

**Need Help?** Check the full deployment guide: `RENDER_DEPLOYMENT.md`
