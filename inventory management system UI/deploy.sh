#!/bin/bash

# Inventory Management System Deployment Script
# This script automates the deployment process

echo "🚀 Starting Inventory Management System Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if .env file exists
if [ ! -f .env ]; then
    print_warning ".env file not found. Copying from .env.example..."
    cp .env.example .env
    print_status ".env file created. Please update your database and email credentials."
fi

# Install Composer dependencies
print_status "Installing Composer dependencies..."
if command -v composer &> /dev/null; then
    composer install --optimize-autoloader --no-dev
else
    print_error "Composer not found. Please install Composer first."
    exit 1
fi

# Generate application key if not exists
if ! grep -q "APP_KEY=base64:" .env; then
    print_status "Generating application key..."
    php artisan key:generate
fi

# Install Node.js dependencies
print_status "Installing Node.js dependencies..."
if command -v npm &> /dev/null; then
    npm install
    npm run production
else
    print_warning "Node.js/npm not found. Frontend assets may not be compiled."
fi

# Create storage directories
print_status "Creating storage directories..."
mkdir -p storage/app/public
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs

# Set permissions
print_status "Setting file permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Create symbolic link for public storage
print_status "Creating storage symbolic link..."
php artisan storage:link

# Run database migrations
print_status "Running database migrations..."
php artisan migrate --force

# Cache optimization for production
if [ "$1" = "production" ]; then
    print_status "Optimizing for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
fi

# Clear any existing caches
print_status "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

print_status "✅ Deployment completed successfully!"
print_status "🌐 Your application is ready!"

# Display next steps
echo ""
echo "📋 Next Steps:"
echo "1. Update your .env file with correct database credentials"
echo "2. Configure your email settings for notifications"
echo "3. Set up your web server to point to the 'public' directory"
echo "4. Set up a cron job for task scheduling:"
echo "   * * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1"
echo ""
echo "🔗 Access your application:"
echo "   Development: php artisan serve"
echo "   Production: Configure your web server"
echo ""
echo "📧 Email notifications will be sent to: alysoffar06@gmail.com"
echo ""
print_status "Happy inventory managing! 📦"
