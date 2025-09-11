# 🚀 Quick Installation Guide

## One-Command Setup

### Windows
```cmd
git clone https://github.com/Alysoffar/Inventory-management-system.git
cd Inventory-management-system
setup.bat
```

### Linux/Mac
```bash
git clone https://github.com/Alysoffar/Inventory-management-system.git
cd Inventory-management-system
chmod +x setup.sh
./setup.sh
```

## Manual Setup (5 minutes)

### 1. Prerequisites
- PHP 8.1+ with SQLite extension
- Python 3.8+
- Composer

### 2. Install Dependencies
```bash
# Python AI Engine
pip install -r requirements.txt

# Laravel Web Interface
cd "inventory management system UI"
composer install
```

### 3. Configure Environment
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Setup database
php artisan migrate --seed
```

### 4. Start Services
```bash
# Terminal 1: AI API
python ai_prediction_api.py

# Terminal 2: Web Server
cd "inventory management system UI"
php artisan serve
```

### 5. Access Application
- **Web Interface**: http://localhost:8000
- **Default Login**: admin@example.com / password

## Troubleshooting

### Common Issues

**Python Dependencies Fail**
```bash
# Update pip first
python -m pip install --upgrade pip
pip install -r requirements.txt
```

**SQLite Database Issues**
```bash
# Create database manually
touch "inventory management system UI/database/database.sqlite"
php artisan migrate --force
```

**Composer Issues**
```bash
# Clear composer cache
composer clear-cache
composer install --no-cache
```

**Port Already in Use**
```bash
# Use different port
php artisan serve --port=8001
```

## Production Deployment

### Quick Production Setup
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chmod -R 755 storage bootstrap/cache
```

---

**Need Help?** Check the full [README.md](README.md) or create an [issue](https://github.com/Alysoffar/Inventory-management-system/issues).
