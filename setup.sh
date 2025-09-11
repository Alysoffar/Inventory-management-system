#!/bin/bash

# ====================================================
# AI-Powered Inventory Management System
# Quick Setup Script
# ====================================================

echo "🚀 Setting up AI-Powered Inventory Management System..."

# Check if we're in the right directory
if [ ! -d "inventory management system UI" ]; then
    echo "❌ Error: Please run this script from the project root directory"
    exit 1
fi

# Install Python dependencies
echo "📦 Installing Python dependencies..."
if command -v python3 &> /dev/null; then
    python3 -m pip install -r requirements.txt
elif command -v python &> /dev/null; then
    python -m pip install -r requirements.txt
else
    echo "❌ Error: Python not found. Please install Python 3.8+"
    exit 1
fi

# Setup Laravel
echo "🔧 Setting up Laravel application..."
cd "inventory management system UI"

# Install PHP dependencies
if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader
else
    echo "❌ Error: Composer not found. Please install Composer"
    exit 1
fi

# Setup environment
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "✅ Created .env file"
fi

# Generate application key
php artisan key:generate

# Setup database
if [ ! -f "database/database.sqlite" ]; then
    touch database/database.sqlite
    echo "✅ Created SQLite database"
fi

# Run migrations
php artisan migrate --force

# Seed database with sample data
php artisan db:seed --force

# Cache config for better performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "🎉 Setup complete!"
echo ""
echo "📚 Quick Start:"
echo "1. Start AI API: python ai_prediction_api.py"
echo "2. Start Web Server: cd 'inventory management system UI' && php artisan serve"
echo "3. Open browser: http://localhost:8000"
echo ""
echo "🔐 Default Login:"
echo "   Email: admin@example.com"
echo "   Password: password"
echo ""
echo "✨ Happy inventory managing!"
