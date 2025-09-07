 # 📦 Smart Inventory Management System

![Laravel](https://img.shields.io/badge/Laravel-12.28.0-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)
![Leaflet](https://img.shields.io/badge/Leaflet-1.9.4-green.svg)
![License](https://img.shields.io/badge/License-MIT-yellow.svg)

A comprehensive **Laravel-based inventory management system** with AI-powered predictions, real-time GPS tracking, automated restocking, email notifications, and a modern responsive interface. Built for businesses of all sizes to efficiently manage their inventory operations.

## 🌟 Key Features

### 📊 Core Inventory Management
- **Real-time Stock Monitoring**: Live inventory tracking with instant updates
- **Multi-location Support**: Manage inventory across multiple warehouses and locations
- **Automated Restock System**: Smart automatic purchase orders when stock falls below thresholds
- **Low Stock Alerts**: Instant email notifications to `alysoffar06@gmail.com`
- **Comprehensive Audit Trail**: Complete history of all inventory transactions
- **Barcode Integration**: Ready for barcode scanner integration
- **Batch Operations**: Bulk stock adjustments and transfers

### 🤖 AI-Powered Predictions
- **Demand Forecasting**: Machine learning models predict future inventory needs
- **Stockout Risk Assessment**: AI calculates probability of running out of stock
- **Intelligent Reorder Points**: Dynamic reorder thresholds based on sales patterns
- **Seasonal Trend Analysis**: Identify seasonal patterns in demand
- **Export Predictions**: CSV/PDF export of AI predictions and recommendations

### 🗺️ GPS & Location Tracking
- **Interactive Maps**: Real-time inventory locations using Leaflet.js and OpenStreetMap
- **Moving Truck Tracking**: Live tracking of delivery vehicles
- **Warehouse Locations**: Visual representation of all storage facilities
- **Route Optimization**: Plan efficient delivery routes
- **Geofencing**: Location-based alerts and notifications

### 📈 Advanced Analytics & Reporting
- **Visual Dashboard**: Comprehensive analytics with charts and graphs
- **Sales Analytics**: Revenue tracking, customer insights, purchase patterns
- **Inventory Reports**: Stock levels, turnover rates, aging analysis
- **Performance Metrics**: KPIs for inventory efficiency and profitability
- **Export Capabilities**: PDF/CSV reports for all modules
- **Real-time Notifications**: System-wide alerts and updates

### 👥 User & Access Management
- **Role-based Access Control**: Secure user permissions and roles
- **Multi-user Support**: Team collaboration with different access levels
- **Activity Logging**: Track all user actions and system changes
- **Secure Authentication**: Laravel Sanctum-powered login system

### 🎨 Modern User Interface
- **Responsive Design**: Mobile-first Bootstrap 5 interface
- **Dark/Light Themes**: User preference-based theming
- **Optimized Typography**: Enhanced readability with larger fonts for important data
- **Compact Layout**: Space-efficient design with reduced padding
- **Interactive Components**: Modern UI elements and smooth animations
- **Accessibility**: WCAG compliant for users with disabilities

## 🚀 Technology Stack

### Backend Framework
- **Laravel**: 12.28.0 (Latest LTS)
- **PHP**: 8.2+ with modern features
- **Database**: SQLite (development) / MySQL 8.0+ (production)
- **Authentication**: Laravel Sanctum
- **Task Scheduling**: Laravel Scheduler for automated processes
- **Email System**: Laravel Mail with SMTP integration

### Frontend Technologies
- **Bootstrap**: 5.3.2 for responsive design
- **Font Awesome**: 6.4+ for comprehensive iconography
- **Leaflet.js**: 1.9.4 for interactive mapping
- **Chart.js**: Dynamic charts and visualizations
- **jQuery**: DOM manipulation and AJAX requests

### AI & Machine Learning
- **Python**: 3.8+ for AI engine
- **TensorFlow**: 2.10+ for deep learning models
- **Pandas**: Data manipulation and analysis
- **Scikit-learn**: Machine learning algorithms
- **Flask**: API server for AI predictions

### Development & Deployment
- **Composer**: PHP dependency management
- **NPM/Yarn**: Frontend package management
- **Git**: Version control system
- **XAMPP**: Local development environment
- **Docker**: Containerization (optional)

## 📋 System Requirements

### Development Environment
- **Operating System**: Windows 10/11, macOS, or Linux
- **PHP**: Version 8.2 or higher
  - Extensions: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath
- **Composer**: Latest version (2.5+)
- **Database**: 
  - SQLite 3.8+ (development)
  - MySQL 8.0+ or PostgreSQL 13+ (production)
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **XAMPP**: 8.2+ for local development
- **Node.js**: 16+ for asset compilation
- **Python**: 3.8+ for AI features
- **Git**: 2.30+ for version control

### Production Environment
- **Server**: Linux (Ubuntu 20.04+ recommended)
- **Memory**: Minimum 2GB RAM (4GB+ recommended)
- **Storage**: 10GB+ available space
- **SSL Certificate**: For HTTPS encryption
- **Email Service**: SMTP server or service (SendGrid, Mailgun, etc.)

## 🛠️ Installation Guide

### Step 1: Environment Setup

#### For Windows (XAMPP)
```bash
# Download and install XAMPP with PHP 8.2+
# Start Apache and MySQL services

# Clone the repository
git clone https://github.com/Alysoffar/Inventory-management-system.git
cd inventory-management-system
```

#### For Linux/macOS
```bash
# Install required packages
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-mysql composer git nodejs npm

# Clone the repository
git clone https://github.com/Alysoffar/Inventory-management-system.git
cd inventory-management-system
```

### Step 2: Laravel Application Setup

```bash
# Install PHP dependencies
composer install

# Copy environment configuration
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database (edit .env file)
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# Or for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=inventory_management
# DB_USERNAME=your_username
# DB_PASSWORD=your_password
```

### Step 3: Database Setup

```bash
# Create SQLite database file (if using SQLite)
touch database/database.sqlite

# Run database migrations
php artisan migrate

# Seed the database with sample data (optional)
php artisan db:seed

# Create storage links
php artisan storage:link
```

### Step 4: Environment Configuration

Edit your `.env` file with these essential settings:

```bash
# Application
APP_NAME="Smart Inventory Management"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Email Configuration (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# AI API Configuration (optional)
AI_API_URL=http://localhost:5000
AI_API_KEY=your_ai_api_key

# Map Configuration
MAP_DEFAULT_LAT=40.7128
MAP_DEFAULT_LNG=-74.0060
MAP_DEFAULT_ZOOM=13
```

### Step 5: Frontend Assets

```bash
# Install Node.js dependencies (if using asset compilation)
npm install

# Compile assets (optional)
npm run dev

# Or for production
npm run build
```

### Step 6: Start the Application

```bash
# Start the Laravel development server
php artisan serve --host=127.0.0.1 --port=8000

# Visit http://127.0.0.1:8000 in your browser
```

## 🔧 Advanced Configuration

### Email Notifications Setup

1. **Gmail Configuration**:
   ```bash
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your_email@gmail.com
   MAIL_PASSWORD=your_app_password  # Use App Password, not regular password
   MAIL_ENCRYPTION=tls
   ```

2. **Enable Gmail App Passwords**:
   - Go to Google Account settings
   - Enable 2-Factor Authentication
   - Generate an App Password for the application

### AI Predictions Setup (Optional)

1. **Install Python Dependencies**:
   ```bash
   pip install -r requirements.txt
   ```

2. **Start AI API Server**:
   ```bash
   cd scripts
   python TrainingPipline.py
   ```

3. **Configure AI API URL** in `.env`:
   ```bash
   AI_API_URL=http://localhost:5000
   ```

### Task Scheduling (Automated Processes)

Add to your server's crontab for automated restock checks:
```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### Database Optimization

For production environments:
```bash
# Optimize database
php artisan optimize

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

## 📖 User Guide

### Dashboard Overview
- **Statistics Cards**: Total products, low stock alerts, out of stock items, inventory value
- **Recent Activities**: Live feed of inventory changes
- **Low Stock Alerts**: Immediate attention items
- **Quick Actions**: Add products, run inventory checks, export reports

### Product Management
- **Add Products**: Complete product information with images and locations
- **Stock Adjustments**: Increase/decrease inventory levels
- **Bulk Operations**: Mass updates for multiple products
- **Barcode Support**: Ready for scanner integration

### GPS Tracking & Maps
- **Real-time Locations**: View all inventory locations on interactive map
- **Truck Tracking**: Monitor delivery vehicles
- **Warehouse Management**: Visual warehouse layout
- **Route Planning**: Optimize delivery routes

### AI Predictions
- **Demand Forecasting**: Predict future sales and inventory needs
- **Risk Assessment**: Identify products at risk of stockout
- **Reorder Recommendations**: AI-suggested reorder points
- **Export Predictions**: Download forecasts in CSV/PDF format

### Reports & Analytics
- **Sales Reports**: Revenue analysis and trends
- **Inventory Reports**: Stock levels and turnover
- **Customer Analytics**: Purchase patterns and insights
- **Export Options**: PDF and CSV downloads

## 🔍 API Documentation

### RESTful API Endpoints

#### Products
```http
GET    /api/products        # List all products
POST   /api/products        # Create new product
GET    /api/products/{id}   # Get specific product
PUT    /api/products/{id}   # Update product
DELETE /api/products/{id}   # Delete product
```

#### Inventory
```http
GET    /api/inventory/status      # Current inventory status
POST   /api/inventory/adjust      # Adjust stock levels
GET    /api/inventory/low-stock   # Low stock items
```

#### AI Predictions
```http
GET    /api/ai/predictions       # Get all predictions
POST   /api/ai/predict          # Generate new prediction
GET    /api/ai/health           # Check AI service status
```

## 🧪 Testing

### Run Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

### Test Categories
- **Unit Tests**: Model and service testing
- **Feature Tests**: HTTP endpoints and user flows
- **Browser Tests**: Laravel Dusk for UI testing

## 🚀 Deployment

### Production Deployment

1. **Server Preparation**:
   ```bash
   # Ubuntu 20.04+ server
   sudo apt update
   sudo apt install nginx mysql-server php8.2-fpm composer git
   ```

2. **Deploy Application**:
   ```bash
   # Clone repository
   git clone https://github.com/Alysoffar/Inventory-management-system.git
   cd inventory-management-system
   
   # Install dependencies
   composer install --optimize-autoloader --no-dev
   
   # Set permissions
   sudo chown -R www-data:www-data storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   ```

3. **Configure Nginx**:
   ```nginx
   server {
       listen 80;
       server_name your-domain.com;
       root /var/www/inventory-management-system/public;
       
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
       
       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }
   }
   ```

## 🛡️ Security

### Security Features
- **CSRF Protection**: Laravel's built-in CSRF tokens
- **XSS Prevention**: Automatic output escaping
- **SQL Injection Protection**: Eloquent ORM with prepared statements
- **Authentication**: Secure user login with Laravel Sanctum
- **Access Control**: Role-based permissions
- **Data Encryption**: Sensitive data encryption

### Security Best Practices
- Keep Laravel and dependencies updated
- Use strong passwords and 2FA
- Regular security audits
- HTTPS in production
- Regular backups
- Monitor system logs

## 📊 Performance Optimization

### Frontend Optimization
- **Asset Minification**: Compressed CSS and JavaScript
- **Image Optimization**: Responsive images with lazy loading
- **Caching**: Browser caching for static assets
- **CDN Integration**: Content delivery network support

### Backend Optimization
- **Database Indexing**: Optimized query performance
- **Query Optimization**: Efficient database queries
- **Caching**: Redis/Memcached integration
- **Background Jobs**: Queued processing for heavy tasks

## 🔧 Troubleshooting

### Common Issues

#### Server Error 500
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

#### Database Connection Issues
```bash
# Check database configuration
php artisan config:show database

# Test database connection
php artisan migrate:status
```

#### Email Not Working
```bash
# Test email configuration
php artisan tinker
Mail::raw('Test email', function($message) {
    $message->to('test@example.com')->subject('Test');
});
```

#### Permission Issues
```bash
# Fix Laravel permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 📝 Changelog

### Version 2.0.0 (Current)
- ✅ Enhanced UI with improved fonts and compact design
- ✅ AI-powered demand forecasting
- ✅ Real-time GPS tracking with moving vehicles
- ✅ Comprehensive export functionality
- ✅ Advanced analytics dashboard
- ✅ Mobile-responsive design improvements

### Version 1.5.0
- ✅ Added multi-location support
- ✅ Implemented automated restocking
- ✅ Enhanced email notification system
- ✅ GPS integration with Leaflet.js

### Version 1.0.0
- ✅ Initial release
- ✅ Basic inventory management
- ✅ User authentication
- ✅ Simple reporting

## 🤝 Contributing

We welcome contributions! Please read our contributing guidelines:

1. **Fork the Repository**
2. **Create Feature Branch**: `git checkout -b feature/amazing-feature`
3. **Commit Changes**: `git commit -m 'Add amazing feature'`
4. **Push to Branch**: `git push origin feature/amazing-feature`
5. **Open Pull Request**

### Development Standards
- Follow PSR-12 coding standards
- Write comprehensive tests
- Document new features
- Use meaningful commit messages

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author & Support

**Developed by**: Alys Offar
**Email**: alysoffar06@gmail.com
**GitHub**: [Alysoffar](https://github.com/Alysoffar)

### Support Channels
- 📧 Email: alysoffar06@gmail.com
- 🐛 Issues: [GitHub Issues](https://github.com/Alysoffar/Inventory-management-system/issues)
- 💬 Discussions: [GitHub Discussions](https://github.com/Alysoffar/Inventory-management-system/discussions)

## 🙏 Acknowledgments

- Laravel Framework Team
- Bootstrap Contributors
- Leaflet.js Community
- OpenStreetMap Contributors
- Font Awesome Team
- Chart.js Developers

---

**⭐ If this project helped you, please give it a star on GitHub!**

*Last Updated: September 8, 2025*

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
