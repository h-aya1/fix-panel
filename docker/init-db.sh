#!/bin/bash

# Wait for database to be ready
until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do
    echo "Waiting for database to be ready..."
    sleep 2
done

echo "Database is ready. Running initialization..."

# Run the init SQL
mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" << EOF
-- Initial database setup
CREATE DATABASE IF NOT EXISTS ems_korea CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Set timezone
SET GLOBAL time_zone = '+09:00';

-- Create additional databases if needed
-- CREATE DATABASE IF NOT EXISTS ems_korea_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EOF

echo "Database initialization completed."

# Run Laravel migrations
cd /var/www/html
php artisan migrate --force
php artisan db:seed --force

echo "Laravel setup completed."
