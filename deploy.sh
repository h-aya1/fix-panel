#!/bin/bash

# EMS Korea Deployment Script
# This script deploys the application to production

set -e

echo "🚀 Starting EMS Korea deployment..."

# Configuration
PROJECT_NAME="ems-korea"
DOCKER_COMPOSE_FILE="docker-compose.prod.yml"
ENV_FILE=".env.docker"

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

# Check if docker-compose is available
if ! command -v docker-compose &> /dev/null; then
    log_error "docker-compose could not be found. Please install docker-compose."
    exit 1
fi

# Check if environment file exists
if [ ! -f "$ENV_FILE" ]; then
    log_error "Environment file $ENV_FILE not found. Please copy .env.docker and configure it."
    exit 1
fi

# Create necessary directories
log_info "Creating necessary directories..."
mkdir -p storage/logs/{nginx,app}
mkdir -p storage/app/public
mkdir -p storage/framework/{cache,sessions,views}

# Set proper permissions
log_info "Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Pull latest images
log_info "Pulling latest Docker images..."
docker-compose -f $DOCKER_COMPOSE_FILE pull

# Build application image
log_info "Building application image..."
docker-compose -f $DOCKER_COMPOSE_FILE build --no-cache app

# Stop existing containers
log_info "Stopping existing containers..."
docker-compose -f $DOCKER_COMPOSE_FILE down

# Start services
log_info "Starting services..."
docker-compose -f $DOCKER_COMPOSE_FILE --env-file $ENV_FILE up -d

# Wait for database to be ready
log_info "Waiting for database to be ready..."
sleep 30

# Run Laravel setup commands
log_info "Running Laravel setup..."

# Generate app key if not set
docker-compose -f $DOCKER_COMPOSE_FILE exec -T app php artisan key:generate --force

# Run migrations
docker-compose -f $DOCKER_COMPOSE_FILE exec -T app php artisan migrate --force

# Clear and cache configuration
docker-compose -f $DOCKER_COMPOSE_FILE exec -T app php artisan config:clear
docker-compose -f $DOCKER_COMPOSE_FILE exec -T app php artisan config:cache

# Clear and cache routes
docker-compose -f $DOCKER_COMPOSE_FILE exec -T app php artisan route:clear
docker-compose -f $DOCKER_COMPOSE_FILE exec -T app php artisan route:cache

# Clear and cache views
docker-compose -f $DOCKER_COMPOSE_FILE exec -T app php artisan view:clear
docker-compose -f $DOCKER_COMPOSE_FILE exec -T app php artisan view:cache

# Storage link
docker-compose -f $DOCKER_COMPOSE_FILE exec -T app php artisan storage:link

# Optimize application
docker-compose -f $DOCKER_COMPOSE_FILE exec -T app php artisan optimize

# Show running services
log_info "Deployment complete! Services status:"
docker-compose -f $DOCKER_COMPOSE_FILE ps

# Show logs command
log_info "To view logs, run: docker-compose -f $DOCKER_COMPOSE_FILE logs -f"

# Health check
log_info "Performing health check..."
sleep 10

if curl -f http://localhost/health > /dev/null 2>&1; then
    log_info "✅ Application is running successfully!"
    log_info "🌐 Access your application at: http://localhost"
else
    log_warn "⚠️  Health check failed. Check the logs with: docker-compose -f $DOCKER_COMPOSE_FILE logs"
fi

echo ""
log_info "🎉 Deployment completed!"
log_info "📋 Useful commands:"
echo "  • View logs: docker-compose -f $DOCKER_COMPOSE_FILE logs -f"
echo "  • Stop services: docker-compose -f $DOCKER_COMPOSE_FILE down"
echo "  • Restart services: docker-compose -f $DOCKER_COMPOSE_FILE restart"
echo "  • Run artisan commands: docker-compose -f $DOCKER_COMPOSE_FILE exec app php artisan <command>"
