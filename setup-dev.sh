#!/bin/bash

# EMS Korea Development Setup Script

set -e

echo "🛠️  Setting up EMS Korea for development..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    log_error "Docker is not running. Please start Docker and try again."
    exit 1
fi

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    log_info "Creating .env file..."
    cp .env.example .env 2>/dev/null || cp .env.docker .env
fi

# Create necessary directories
log_info "Creating necessary directories..."
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p bootstrap/cache

# Set proper permissions
log_info "Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Build and start services
log_info "Building and starting Docker services..."
docker-compose up -d --build

# Wait for services to be ready
log_info "Waiting for services to be ready..."
sleep 30

# Install PHP dependencies
log_info "Installing PHP dependencies..."
docker-compose exec app composer install

# Generate application key
log_info "Generating application key..."
docker-compose exec app php artisan key:generate

# Run migrations
log_info "Running database migrations..."
docker-compose exec app php artisan migrate

# Seed database (if seeders exist)
if docker-compose exec app php artisan db:seed --help > /dev/null 2>&1; then
    log_info "Seeding database..."
    docker-compose exec app php artisan db:seed
fi

# Create storage link
log_info "Creating storage link..."
docker-compose exec app php artisan storage:link

# Install and build frontend assets
log_info "Installing and building frontend assets..."
docker-compose exec app npm install
docker-compose exec app npm run build

# Clear caches
log_info "Clearing caches..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Show running services
log_info "Development setup complete! Services status:"
docker-compose ps

log_info "✅ Development environment is ready!"
log_info "🌐 Access your application at: http://localhost"
log_info "📊 Access phpMyAdmin at: http://localhost:8080 (if enabled)"

echo ""
log_info "📋 Useful development commands:"
echo "  • View logs: docker-compose logs -f"
echo "  • Stop services: docker-compose down"
echo "  • Restart services: docker-compose restart"
echo "  • Run artisan commands: docker-compose exec app php artisan <command>"
echo "  • Access app container: docker-compose exec app bash"
echo "  • Run npm commands: docker-compose exec app npm <command>"
