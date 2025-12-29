# StockFlowPOS Docker Deployment Guide

This guide covers deploying the StockFlowPOS Laravel + React application using Docker for both local development and production deployment on Render.

## 📋 Overview

The StockFlowPOS application uses:
- **Laravel 11** backend with PHP 8.3+
- **React 19** frontend with Inertia.js and Vite
- **MySQL** database
- **Session and cache drivers** set to database
- **Laravel Sail** for development

## 🏗️ Architecture

The Docker setup includes:
- Multi-stage Dockerfile for optimized production builds
- Development Docker Compose environment
- Render platform configuration
- Nginx + PHP-FPM production setup

## 📁 File Structure

```
├── Dockerfile                 # Production Dockerfile
├── Dockerfile.frontend        # Frontend development Dockerfile
├── docker-compose.yml         # Local development environment
├── .dockerignore             # Docker build optimization
├── render.yaml               # Render platform configuration
├── deployment/               # Production configurations
│   ├── nginx.conf           # Nginx configuration
│   ├── php.ini              # PHP production settings
│   └── supervisord.conf     # Service management
├── docker/local/            # Local development configs
│   └── supervisord.conf     # Development service config
├── database/sql/            # Database initialization
│   └── init.sql            # MySQL setup script
├── package.json             # Updated with Docker scripts
└── composer.json            # Updated with deployment scripts
```

## 🚀 Quick Start

### Local Development

1. **Start the development environment:**
```bash
# Start all services
docker-compose up -d

# Or start specific services
docker-compose up -d app mysql redis mailhog phpmyadmin
```

2. **Access the application:**
- Laravel App: http://localhost:8080
- Vite Dev Server: http://localhost:5173
- PHPMyAdmin: http://localhost:8081
- MailHog: http://localhost:8025

3. **Run migrations:**
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

### Production Deployment on Render

1. **Connect your repository to Render**

2. **Set environment variables in Render dashboard:**
```env
APP_KEY=base64:your-generated-app-key
DB_HOST=stockflowpos-db.internal
DB_DATABASE=stockflowpos
DB_USERNAME=stockflowpos
DB_PASSWORD=your-db-password
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

3. **Deploy:**
- Push to your main branch
- Render will automatically detect and deploy using `render.yaml`

## 🔧 Configuration

### Environment Variables

#### Required for Production:
```env
APP_KEY=base64:your-app-key-here
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=your-database-host
DB_DATABASE=stockflowpos
DB_USERNAME=your-username
DB_PASSWORD=your-password
SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

#### Optional but Recommended:
```env
BROADCAST_DRIVER=log
FILESYSTEM_DISK=local
SANCTUM_STATEFUL_DOMAINS=your-domain.com
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

### Database Configuration

The application is configured to use database drivers for:
- **Sessions**: Stored in `sessions` table
- **Cache**: Stored in `cache` table
- **Queue Jobs**: Stored in `jobs` table

## 🛠️ Available Scripts

### Package Scripts (npm)
```bash
npm run dev                 # Development server
npm run build              # Production build
npm run build:prod         # Production build with optimizations
npm run docker:dev         # Docker development
npm run docker:build       # Docker production build
npm run docker:install     # Install dependencies for Docker
npm run deploy:build       # Build for deployment
npm run preview            # Preview production build
```

### Composer Scripts
```bash
composer docker:install    # Install PHP dependencies for Docker
composer docker:dev        # Install PHP dependencies for development
composer docker:build      # Production PHP dependencies install
composer deploy:setup      # Initial deployment setup
composer deploy:production # Production optimization
composer queue:work        # Start queue worker
composer schedule:run      # Run scheduled tasks
```

## 🔍 Troubleshooting

### Common Issues

1. **Permission Errors:**
```bash
# Fix Laravel storage permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

2. **Database Connection Issues:**
```bash
# Check database connectivity
docker-compose exec app php artisan tinker
> DB::connection()->getPdo();
```

3. **Vite Not Loading Assets:**
```bash
# Rebuild assets
docker-compose exec app npm run build
```

4. **Queue Not Processing:**
```bash
# Start queue worker
docker-compose exec app php artisan queue:work
```

### Health Checks

- Application health: `GET /health`
- Database connectivity: Laravel migrations status
- Redis connectivity: `redis-cli ping`
- Queue status: Laravel queue status

## 📊 Performance Optimization

### Production Optimizations Included:
1. **Multi-stage Docker builds** for smaller image sizes
2. **Nginx + PHP-FPM** for better performance
3. **OPcache enabled** for PHP
4. **Gzip compression** for assets
5. **Laravel route/config/view caching**
6. **Optimized autoloading**

### Additional Optimizations:
- Enable Redis for sessions and cache in production
- Use a CDN for static assets
- Enable HTTP/2 in Nginx
- Configure database connection pooling

## 🔒 Security Considerations

1. **Environment Variables**: Never commit `.env` files
2. **Database**: Use strong passwords and restrict access
3. **Sessions**: Database sessions are encrypted and secure
4. **File Permissions**: Proper ownership and permissions set
5. **HTTPS**: Enable SSL in production

## 📝 Deployment Checklist

- [ ] Set up production environment variables
- [ ] Generate new APP_KEY
- [ ] Configure database with proper credentials
- [ ] Set up SMTP settings
- [ ] Configure domain in SANCTUM_STATEFUL_DOMAINS
- [ ] Test database migrations
- [ ] Verify queue workers are running
- [ ] Test email functionality
- [ ] Enable HTTPS/SSL
- [ ] Set up monitoring and logging

## 🤝 Support

For issues related to:
- **Laravel**: Check Laravel documentation
- **React/Vite**: Check React and Vite documentation
- **Docker**: Check Docker documentation
- **Render**: Check Render documentation

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [React Documentation](https://react.dev)
- [Vite Documentation](https://vitejs.dev)
- [Docker Documentation](https://docs.docker.com)
- [Render Documentation](https://render.com/docs)