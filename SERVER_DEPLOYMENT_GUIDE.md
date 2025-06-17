# EMS Korea - Server Deployment Configuration

## Server Requirements

### Minimum Requirements
- **CPU**: 2 cores
- **RAM**: 4GB
- **Storage**: 20GB SSD
- **OS**: Ubuntu 20.04 LTS or CentOS 8
- **Network**: 100 Mbps

### Recommended for Production
- **CPU**: 4 cores
- **RAM**: 8GB
- **Storage**: 50GB SSD
- **OS**: Ubuntu 22.04 LTS
- **Network**: 1 Gbps

## Server Setup

### 1. Initial Server Configuration

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install essential packages
sudo apt install -y curl wget git unzip software-properties-common

# Configure firewall
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# Create deployment user
sudo adduser deploy
sudo usermod -aG sudo deploy
sudo usermod -aG docker deploy
```

### 2. Docker Installation

```bash
# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.24.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Start Docker service
sudo systemctl enable docker
sudo systemctl start docker

# Add user to docker group
sudo usermod -aG docker $USER
newgrp docker
```

### 3. Application Deployment

```bash
# Clone repository
git clone <your-repository-url> /var/www/ems-korea
cd /var/www/ems-korea

# Configure environment
cp .env.docker .env.production
nano .env.production  # Edit with your settings

# Generate SSL certificates (Let's Encrypt)
sudo apt install certbot
sudo certbot certonly --standalone -d your-domain.com
sudo cp /etc/letsencrypt/live/your-domain.com/fullchain.pem docker/nginx/ssl/cert.pem
sudo cp /etc/letsencrypt/live/your-domain.com/privkey.pem docker/nginx/ssl/key.pem

# Deploy application
./deploy.sh
```

## Production Environment Configuration

### Environment Variables (.env.production)

```env
# Application
APP_NAME="EMS Korea"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_TIMEZONE=Asia/Seoul
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=ems_korea_prod
DB_USERNAME=ems_prod_user
DB_PASSWORD=your_very_secure_db_password

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=your_secure_redis_password
REDIS_PORT=6379

# Cache Configuration
CACHE_DRIVER=redis
CACHE_PREFIX=ems_korea_prod

# Session Configuration
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=.your-domain.com
SESSION_SECURE_COOKIE=true

# Queue Configuration
QUEUE_CONNECTION=redis
QUEUE_FAILED_DRIVER=database-uuids

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server.com
MAIL_PORT=587
MAIL_USERNAME=your-email@your-domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="EMS Korea"

# Logging
LOG_CHANNEL=daily
LOG_LEVEL=warning
LOG_DEPRECATIONS_CHANNEL=null

# Security
BCRYPT_ROUNDS=12
HASH_DRIVER=bcrypt
```

## Domain and SSL Configuration

### 1. DNS Configuration
Point your domain to your server:
```
A Record: your-domain.com → YOUR_SERVER_IP
A Record: www.your-domain.com → YOUR_SERVER_IP
```

### 2. SSL Certificate Setup

#### Option A: Let's Encrypt (Recommended)
```bash
# Install Certbot
sudo apt install certbot

# Generate certificate
sudo certbot certonly --standalone -d your-domain.com -d www.your-domain.com

# Copy certificates to Docker
sudo cp /etc/letsencrypt/live/your-domain.com/fullchain.pem docker/nginx/ssl/cert.pem
sudo cp /etc/letsencrypt/live/your-domain.com/privkey.pem docker/nginx/ssl/key.pem

# Set up auto-renewal
echo "0 12 * * * /usr/bin/certbot renew --quiet" | sudo crontab -
```

#### Option B: Custom SSL Certificate
```bash
# If you have your own SSL certificate
cp your-certificate.crt docker/nginx/ssl/cert.pem
cp your-private-key.key docker/nginx/ssl/key.pem
```

### 3. Update Nginx Configuration
Edit `docker/nginx/production.conf` and replace `your-domain.com` with your actual domain.

## Database Configuration

### MySQL Optimization for Production

Edit `docker/mysql/production.cnf`:
```ini
[mysqld]
# Memory settings (adjust based on your server RAM)
innodb_buffer_pool_size = 2G  # 50-70% of available RAM
innodb_log_file_size = 512M
innodb_buffer_pool_instances = 8

# Connection settings
max_connections = 300
wait_timeout = 28800
interactive_timeout = 28800

# Performance
query_cache_type = 1
query_cache_size = 128M
query_cache_limit = 2M
tmp_table_size = 128M
max_heap_table_size = 128M

# Security
local_infile = 0
skip_symbolic_links
bind-address = 0.0.0.0
```

### Database Backup Strategy

```bash
# Create backup script
cat > /usr/local/bin/backup-ems-db.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/var/backups/ems-korea"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

# Backup database
docker-compose -f /var/www/ems-korea/docker-compose.prod.yml exec -T db \
  mysqldump -u root -p$DB_ROOT_PASSWORD ems_korea_prod | \
  gzip > $BACKUP_DIR/ems_korea_$DATE.sql.gz

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.sql.gz" -mtime +7 -delete

echo "Backup completed: ems_korea_$DATE.sql.gz"
EOF

chmod +x /usr/local/bin/backup-ems-db.sh

# Schedule daily backups
echo "0 2 * * * /usr/local/bin/backup-ems-db.sh" | crontab -
```

## Monitoring and Logging

### 1. System Monitoring
```bash
# Install system monitoring tools
sudo apt install htop iotop nethogs

# Docker stats monitoring
docker stats --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.NetIO}}\t{{.BlockIO}}"
```

### 2. Application Monitoring
```bash
# Log monitoring script
cat > /usr/local/bin/monitor-ems.sh << 'EOF'
#!/bin/bash
cd /var/www/ems-korea

echo "=== Container Status ==="
docker-compose -f docker-compose.prod.yml ps

echo -e "\n=== Application Logs (last 20 lines) ==="
docker-compose -f docker-compose.prod.yml logs --tail=20 app

echo -e "\n=== Nginx Error Logs (last 10 lines) ==="
docker-compose -f docker-compose.prod.yml logs --tail=10 nginx | grep error

echo -e "\n=== Database Status ==="
docker-compose -f docker-compose.prod.yml exec db mysqladmin -u root -p$DB_ROOT_PASSWORD status

echo -e "\n=== Disk Usage ==="
df -h

echo -e "\n=== Memory Usage ==="
free -h
EOF

chmod +x /usr/local/bin/monitor-ems.sh
```

### 3. Log Rotation
```bash
# Configure log rotation
sudo cat > /etc/logrotate.d/ems-korea << 'EOF'
/var/www/ems-korea/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
    postrotate
        docker-compose -f /var/www/ems-korea/docker-compose.prod.yml restart app
    endscript
}
EOF
```

## Security Hardening

### 1. Server Security
```bash
# Disable root SSH login
sudo sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config
sudo systemctl restart ssh

# Install fail2ban
sudo apt install fail2ban
sudo systemctl enable fail2ban

# Configure fail2ban for SSH
sudo cat > /etc/fail2ban/jail.local << 'EOF'
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true
port = ssh
logpath = /var/log/auth.log
EOF

sudo systemctl restart fail2ban
```

### 2. Application Security
```bash
# Set proper file permissions
sudo chown -R deploy:deploy /var/www/ems-korea
sudo chmod -R 755 /var/www/ems-korea
sudo chmod -R 600 /var/www/ems-korea/.env*
sudo chmod 600 /var/www/ems-korea/docker/nginx/ssl/*.pem
```

## Performance Optimization

### 1. System Optimization
```bash
# Optimize kernel parameters
sudo cat >> /etc/sysctl.conf << 'EOF'
# Network performance
net.core.rmem_max = 16777216
net.core.wmem_max = 16777216
net.ipv4.tcp_rmem = 4096 65536 16777216
net.ipv4.tcp_wmem = 4096 65536 16777216

# File system
fs.file-max = 100000
vm.swappiness = 10
EOF

sudo sysctl -p
```

### 2. Docker Optimization
```bash
# Configure Docker daemon
sudo cat > /etc/docker/daemon.json << 'EOF'
{
  "log-driver": "json-file",
  "log-opts": {
    "max-size": "10m",
    "max-file": "3"
  },
  "storage-driver": "overlay2"
}
EOF

sudo systemctl restart docker
```

## Maintenance Procedures

### Regular Maintenance Tasks

1. **Weekly Tasks**:
   - Check system updates: `sudo apt update && sudo apt list --upgradable`
   - Review application logs: `/usr/local/bin/monitor-ems.sh`
   - Check disk space: `df -h`
   - Verify SSL certificate expiry: `openssl x509 -in docker/nginx/ssl/cert.pem -noout -dates`

2. **Monthly Tasks**:
   - Update Docker images: `docker-compose pull && docker-compose up -d`
   - Clean Docker system: `docker system prune -f`
   - Review and rotate logs
   - Security scan: `sudo apt audit`

3. **Quarterly Tasks**:
   - Full system backup
   - Security audit
   - Performance review
   - Disaster recovery test

### Deployment Updates
```bash
# Update application
cd /var/www/ems-korea
git pull origin main

# Rebuild and deploy
docker-compose -f docker-compose.prod.yml build --no-cache app
docker-compose -f docker-compose.prod.yml up -d

# Run any new migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Clear and rebuild caches
docker-compose -f docker-compose.prod.yml exec app php artisan optimize
```

## Troubleshooting

### Common Issues and Solutions

1. **Application not accessible**:
   - Check container status: `docker-compose ps`
   - Check nginx logs: `docker-compose logs nginx`
   - Verify firewall: `sudo ufw status`

2. **Database connection errors**:
   - Check database container: `docker-compose logs db`
   - Verify credentials in `.env.production`
   - Test connection: `docker-compose exec app php artisan tinker`

3. **SSL certificate issues**:
   - Verify certificate files exist and are readable
   - Check certificate validity: `openssl x509 -in docker/nginx/ssl/cert.pem -noout -dates`
   - Review nginx error logs

4. **Performance issues**:
   - Monitor resources: `docker stats`
   - Check system load: `htop`
   - Review slow query log
   - Optimize Laravel caches

### Emergency Procedures

1. **Application rollback**:
```bash
cd /var/www/ems-korea
git reset --hard HEAD~1
docker-compose -f docker-compose.prod.yml up -d --build
```

2. **Database restore**:
```bash
# Restore from backup
zcat /var/backups/ems-korea/ems_korea_YYYYMMDD_HHMMSS.sql.gz | \
docker-compose -f docker-compose.prod.yml exec -T db mysql -u root -p ems_korea_prod
```

3. **Emergency maintenance mode**:
```bash
docker-compose -f docker-compose.prod.yml exec app php artisan down --message="Emergency maintenance in progress"
# After maintenance
docker-compose -f docker-compose.prod.yml exec app php artisan up
```

## Contact and Support

For production issues:
- Monitor logs: `docker-compose logs -f`
- Check system status: `/usr/local/bin/monitor-ems.sh`
- Emergency contact: [Your contact information]
