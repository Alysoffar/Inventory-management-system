# 📦 Smart Inventory Management System

A comprehensive Laravel-based inventory tracking system with automated restock functionality, email notifications, GPS tracking, and modern responsive UI.

## 🌟 Features

### Core Functionality
- **Complete Inventory Tracking**: Real-time stock monitoring with low-stock alerts
- **Automated Restock System**: Automatic purchase orders when stock falls below thresholds  
- **Email Notifications**: Instant alerts sent to `alysoffar06@gmail.com` for low stock situations
- **GPS Location Tracking**: Track inventory locations with interactive maps using Leaflet.js
- **Modern Responsive UI**: Clean, gradient-based interface built with Bootstrap 5

### Advanced Features
- **Dashboard Analytics**: Visual insights into inventory levels and trends
- **Multi-location Support**: Track inventory across different warehouses/locations
- **Supplier Management**: Comprehensive supplier database with contact information
- **Purchase History**: Complete audit trail of all inventory transactions
- **Role-based Access**: Secure user authentication and authorization
- **Mobile-First Design**: Fully responsive across all devices

## 🚀 Technology Stack

- **Backend**: Laravel 10.x (PHP 8.2+)
- **Database**: MySQL 8.0+
- **Frontend**: Bootstrap 5, Leaflet.js for maps
- **Email**: Laravel Mail with SMTP support
- **Authentication**: Laravel Sanctum
- **Task Scheduling**: Laravel Scheduler for automated processes
- **Development**: XAMPP for local development

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP**: Version 8.2 or higher
- **Composer**: Latest version
- **MySQL**: Version 8.0 or higher
- **XAMPP**: For local development environment
- **Node.js**: Version 16+ (for asset compilation)
- **Git**: For version control

## 🛠️ Installation & Setup

### 1. Clone the Repository
```bash
git clone <repository-url>
cd inventory-management-system
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies (if using Laravel Mix)
npm install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup
Update your `.env` file with database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_system
DB_USERNAME=root
DB_PASSWORD=
```

Create the database and run migrations:
```bash
# Create database (if not exists)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS inventory_system;"

# Run migrations
php artisan migrate
```

### 5. Email Configuration
Configure email settings in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Notification email
NOTIFICATION_EMAIL=alysoffar06@gmail.com
```

### 6. Start the Application
```bash
# Start Laravel development server
php artisan serve

# The application will be available at: http://127.0.0.1:8000
```

## 📁 Project Structure

```
inventory-management-system/
├── app/
│   ├── Console/
│   │   ├── Commands/          # Custom Artisan commands
│   │   └── Kernel.php         # Task scheduling
│   ├── Http/
│   │   ├── Controllers/       # Request handlers
│   │   │   ├── ProductController.php
│   │   │   ├── InventoryController.php
│   │   │   ├── SupplierController.php
│   │   │   └── DashboardController.php
│   │   └── Middleware/        # Custom middleware
│   ├── Mail/                  # Email templates
│   │   ├── LowStockAlert.php
│   │   └── RestockNotification.php
│   ├── Models/                # Eloquent models
│   │   ├── Product.php
│   │   ├── Supplier.php
│   │   ├── InventoryLog.php
│   │   └── Notification.php
│   └── Jobs/                  # Queue jobs
├── database/
│   ├── migrations/            # Database schema
│   └── seeders/               # Sample data
├── resources/
│   ├── views/                 # Blade templates
│   │   ├── dashboard.blade.php
│   │   ├── products/
│   │   ├── suppliers/
│   │   └── layouts/
│   ├── css/                   # Styles
│   └── js/                    # JavaScript
├── routes/
│   ├── web.php               # Web routes
│   ├── api.php               # API routes
│   └── console.php           # Artisan commands
├── public/                   # Public assets
├── storage/                  # File storage
└── config/                   # Configuration files
```

## 🎯 Usage Guide

### Dashboard
- Navigate to `/dashboard` for the main inventory overview
- View real-time stock levels, low stock alerts, and analytics
- Interactive charts showing inventory trends

### Product Management
- **Add Products**: `/products/create`
- **View All Products**: `/products`
- **Edit Product**: `/products/{id}/edit`
- **Stock Adjustment**: Built-in stock adjustment functionality

### Supplier Management
- **Manage Suppliers**: `/suppliers`
- **Add New Supplier**: `/suppliers/create`
- **Supplier Details**: View contact information and purchase history

### Inventory Tracking
- **Real-time Updates**: Stock levels update automatically
- **Location Tracking**: View product locations on interactive maps
- **Audit Trail**: Complete history of all inventory movements

### Automated Features
- **Low Stock Monitoring**: Automatic checking every hour
- **Email Alerts**: Instant notifications when stock is low
- **Auto Restock**: Automatic purchase order generation

## 🔧 Configuration

### Email Notifications
The system sends automated emails to `alysoffar06@gmail.com` for:
- Low stock alerts
- Automatic restock notifications
- Daily/weekly inventory reports

### Task Scheduling
Add this to your crontab for automated tasks:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Map Configuration
The system uses Leaflet.js for mapping. Default coordinates can be configured in:
- `resources/views/layouts/app.blade.php`
- Map tiles are provided by OpenStreetMap

## 🚀 Deployment

### Production Deployment
1. **Environment Setup**:
   ```bash
   # Set production environment
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Optimize Application**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   composer install --optimize-autoloader --no-dev
   ```

3. **File Permissions**:
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

### XAMPP Deployment
1. Copy project to `C:\xampp\htdocs\inventory-system`
2. Start Apache and MySQL in XAMPP Control Panel
3. Create database via phpMyAdmin
4. Run migrations: `php artisan migrate`
5. Access via `http://localhost/inventory-system/public`

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific feature tests
php artisan test --filter=InventoryTest

# Generate test coverage report
php artisan test --coverage
```

## 🐛 Troubleshooting

### Common Issues

**Issue**: "Class not found" errors
**Solution**: Run `composer dump-autoload`

**Issue**: Permission denied on storage
**Solution**: Set proper permissions: `chmod -R 775 storage`

**Issue**: Database connection errors
**Solution**: Verify database credentials in `.env` file

**Issue**: Email not sending
**Solution**: Check SMTP settings and ensure "Less secure app access" is enabled for Gmail

### Debug Mode
Enable debug mode in development:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/new-feature`
3. Commit your changes: `git commit -am 'Add new feature'`
4. Push to the branch: `git push origin feature/new-feature`
5. Submit a pull request

### Coding Standards
- Follow PSR-12 coding standards
- Use meaningful commit messages
- Add tests for new features
- Update documentation as needed

## 📝 Changelog

### Version 2.0.0 (Current)
- ✅ Added GPS location tracking with interactive maps
- ✅ Implemented automated email notifications
- ✅ Enhanced UI with modern Bootstrap 5 design
- ✅ Added supplier management system
- ✅ Integrated task scheduling for automated monitoring

### Version 1.0.0
- ✅ Basic inventory tracking functionality
- ✅ Product CRUD operations
- ✅ Simple dashboard interface

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Support

For support and questions:
- **Email**: alysoffar06@gmail.com
- **Issues**: Create an issue on GitHub repository
- **Documentation**: Check the `/docs` folder for detailed guides

## 🙏 Acknowledgments

- Laravel Framework for the robust foundation
- Bootstrap team for the excellent UI components  
- Leaflet.js for the mapping functionality
- OpenStreetMap for map tiles
- XAMPP for local development environment

---

**Built with ❤️ for efficient inventory management**
