# EMS Korea - Docker Deployment Guide

## Overview
This guide provides complete Docker setup for the EMS Korea Laravel application with Nginx, MySQL, and Redis.

## Prerequisites
- Docker and Docker Compose installed
- At least 2GB RAM available
- Ports 80, 443, 3306, 6379 available

## Quick Start

### Development Setup
```bash
# Clone the repository
git clone <repository-url>
cd ems-korea

# Setup development environment
./setup-dev.sh

# Access application
open http://localhost
```

### Production Deployment
```bash
# Configure environment
cp .env.docker .env.production
# Edit .env.production with your settings

# Deploy to production
./deploy.sh

# Access application
open https://your-domain.com
```

## File Structure
```
docker/
├── nginx/
│   ├── nginx.conf          # Main Nginx configuration
│   ├── default.conf        # Development site configuration
│   ├── production.conf     # Production site configuration
│   └── ssl/               # SSL certificates directory
├── mysql/
│   ├── production.cnf     # MySQL production configuration
│   └── init/              # Database initialization scripts
└── supervisord.conf       # Process supervisor configuration

Dockerfile                 # Multi-stage application build
docker-compose.yml         # Development environment
docker-compose.prod.yml    # Production environment
.env.docker               # Environment template
.dockerignore             # Docker ignore rules
deploy.sh                 # Production deployment script
setup-dev.sh              # Development setup script
```

## Services

### Application Stack
- **Nginx**: Web server and reverse proxy
- **PHP-FPM**: PHP application server
- **MySQL 8.0**: Database server
- **Redis**: Cache and session storage
- **Queue Worker**: Background job processing
- **Scheduler**: Laravel task scheduling

### Ports
- **80**: HTTP (redirects to HTTPS in production)
- **443**: HTTPS (production only)
- **3306**: MySQL (development access)
- **6379**: Redis (development access)

## Configuration

### Environment Variables
Key variables to configure in `.env.docker`:

```env
# Application
APP_NAME="EMS Korea"
APP_URL=https://your-domain.com
APP_KEY=base64:generated-key

# Database
DB_DATABASE=ems_korea
DB_USERNAME=ems_user
DB_PASSWORD=secure_password
DB_ROOT_PASSWORD=root_password

# Redis
REDIS_PASSWORD=redis_password

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### SSL Certificates
For production HTTPS:

1. **Let's Encrypt** (recommended):
```bash
# Install certbot
sudo apt install certbot

# Generate certificate
sudo certbot certonly --standalone -d your-domain.com

# Copy certificates
cp /etc/letsencrypt/live/your-domain.com/fullchain.pem docker/nginx/ssl/cert.pem
cp /etc/letsencrypt/live/your-domain.com/privkey.pem docker/nginx/ssl/key.pem
```

2. **Self-signed** (development):
```bash
cd docker/nginx/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout key.pem -out cert.pem
```

## Deployment Commands

### Development
```bash
# Start development environment
docker-compose up -d

# View logs
docker-compose logs -f

# Run artisan commands
docker-compose exec app php artisan migrate

# Access container
docker-compose exec app bash

# Stop services
docker-compose down
```

### Production
```bash
# Deploy with production configuration
docker-compose -f docker-compose.prod.yml --env-file .env.production up -d

# View production logs
docker-compose -f docker-compose.prod.yml logs -f

# Run maintenance commands
docker-compose -f docker-compose.prod.yml exec app php artisan down
docker-compose -f docker-compose.prod.yml exec app php artisan up

# Update application
git pull
docker-compose -f docker-compose.prod.yml build --no-cache app
docker-compose -f docker-compose.prod.yml up -d

# Backup database
docker-compose -f docker-compose.prod.yml exec db mysqldump -u root -p ems_korea > backup.sql
```

## Performance Optimization

### Production Settings
- **PHP-FPM**: Optimized process management
- **Nginx**: Gzip compression, static file caching
- **MySQL**: InnoDB optimization, query cache
- **Redis**: Memory optimization, persistence
- **Laravel**: Route/config/view caching

### Scaling
For high traffic, consider:
- Multiple app containers behind load balancer
- Separate Redis instances for cache/sessions
- Database read replicas
- CDN for static assets

## Monitoring

### Health Checks
```bash
# Application health
curl http://localhost/health

# Service status
docker-compose ps

# Resource usage
docker stats
```

### Log Management
```bash
# Application logs
docker-compose logs app

# Nginx access logs
docker-compose exec nginx tail -f /var/log/nginx/access.log

# MySQL slow query logs
docker-compose exec db tail -f /var/lib/mysql/mysql-slow.log

# Clear logs
docker-compose exec app php artisan log:clear
```

## Maintenance

### Database Operations
```bash
# Run migrations
docker-compose exec app php artisan migrate

# Backup database
docker-compose exec db mysqldump -u ems_user -p ems_korea > backup.sql

# Restore database
docker-compose exec -T db mysql -u ems_user -p ems_korea < backup.sql

# Database console
docker-compose exec db mysql -u ems_user -p ems_korea
```

### Cache Management
```bash
# Clear all caches
docker-compose exec app php artisan optimize:clear

# Rebuild caches
docker-compose exec app php artisan optimize

# Queue operations
docker-compose exec app php artisan queue:work
docker-compose exec app php artisan queue:restart
```

### Updates
```bash
# Update dependencies
docker-compose exec app composer update

# Update frontend assets
docker-compose exec app npm update
docker-compose exec app npm run build

# Rebuild application
docker-compose build --no-cache app
docker-compose up -d
```

## Security

### Production Checklist
- [ ] Use strong passwords for all services
- [ ] Enable SSL/HTTPS with valid certificates
- [ ] Configure firewall (only ports 80/443 public)
- [ ] Regular security updates
- [ ] Database access restricted to app containers
- [ ] Redis password protection enabled
- [ ] File permissions properly set
- [ ] Debug mode disabled (`APP_DEBUG=false`)

### Backup Strategy
- Database: Daily automated backups
- Application files: Version control + periodic snapshots
- User uploads: Replicated storage or cloud backup
- Configuration: Secure environment variable storage

## Troubleshooting

### Common Issues

1. **Port conflicts**:
```bash
# Check port usage
sudo lsof -i :80
sudo lsof -i :3306

# Use different ports
# Edit docker-compose.yml ports section
```

2. **Permission errors**:
```bash
# Fix Laravel permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 755 storage bootstrap/cache
```

3. **Database connection failed**:
```bash
# Check database container
docker-compose logs db

# Verify credentials
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo();
```

4. **Memory issues**:
```bash
# Increase Docker memory limit
# Edit Docker Desktop settings

# Monitor usage
docker stats
```

## Support
For issues and support:
- Check logs: `docker-compose logs`
- Verify configuration: Review `.env` settings
- Container status: `docker-compose ps`
- Resource usage: `docker system df`
