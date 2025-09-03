# Quick Start Guide 🚀

## How to Run the Inventory Management System

### Option 1: Using XAMPP (Recommended for Windows)

1. **Install XAMPP**: Download and install XAMPP from https://www.apachefriends.org/

2. **Copy Project**: 
   ```bash
   # Copy the entire project folder to XAMPP htdocs
   xcopy "d:\WORK\Findo\Done\inventory management system" "C:\xampp\htdocs\inventory-system" /E /I /H
   ```

3. **Start Services**:
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

4. **Setup Database**:
   - Open http://localhost/phpmyadmin
   - Create database named `inventory_system`
   - Or run: `CREATE DATABASE inventory_system;`

5. **Run Deployment Script**:
   ```bash
   cd C:\xampp\htdocs\inventory-system
   deploy.bat
   ```

6. **Access Application**:
   - Visit: http://localhost/inventory-system/public
   - Or run: `php artisan serve` for development server

### Option 2: Using Laravel Development Server

1. **Navigate to Project**:
   ```bash
   cd "d:\WORK\Findo\Done\inventory management system"
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   ```

3. **Setup Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Create Database**:
   - Create MySQL database named `inventory_system`
   - Update .env file with database credentials

5. **Run Migrations**:
   ```bash
   php artisan migrate
   ```

6. **Start Server**:
   ```bash
   php artisan serve
   ```
   
7. **Access Application**: 
   - Visit: http://127.0.0.1:8000

### Option 3: Using Docker

1. **Start Docker Services**:
   ```bash
   docker-compose up -d
   ```

2. **Run Setup**:
   ```bash
   docker-compose exec app php artisan migrate
   docker-compose exec app php artisan key:generate
   ```

3. **Access Application**: 
   - Visit: http://localhost:8000

## ⚡ Quick Configuration

### Email Settings (.env file)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
NOTIFICATION_EMAIL=alysoffar06@gmail.com
```

### Database Settings (.env file)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_system
DB_USERNAME=root
DB_PASSWORD=
```

## 📚 Key Features Available

✅ **Dashboard**: Real-time inventory overview with charts
✅ **Product Management**: Add, edit, delete products with GPS tracking  
✅ **Low Stock Alerts**: Automatic email notifications to alysoffar06@gmail.com
✅ **Interactive Maps**: View product locations using Leaflet.js
✅ **Supplier Management**: Complete supplier database
✅ **Auto-Restock**: Automated purchase orders when stock is low
✅ **Mobile Responsive**: Works on all devices
✅ **Modern UI**: Clean Bootstrap 5 design with gradients

## 🔧 Troubleshooting

**Issue**: "Class not found" errors
```bash
composer dump-autoload
```

**Issue**: Permission denied
```bash
chmod -R 755 storage bootstrap/cache
```

**Issue**: Database connection error
- Check .env database credentials
- Ensure MySQL is running
- Verify database exists

**Issue**: Emails not sending
- Check SMTP settings in .env
- Enable "Less secure app access" for Gmail
- Use App Password for Gmail

## 📱 How to Use

1. **Dashboard**: Overview of inventory status and alerts
2. **Products**: Manage inventory with GPS tracking
3. **Suppliers**: Add and manage supplier information  
4. **Reports**: View inventory trends and analytics
5. **Settings**: Configure email alerts and thresholds

## 🌍 Default Locations

The system includes sample GPS coordinates for:
- New York: 40.7128° N, 74.0060° W
- London: 51.5074° N, 0.1278° W  
- Tokyo: 35.6762° N, 139.6503° E

## 📧 Notifications

All low stock alerts and restock notifications are automatically sent to:
**alysoffar06@gmail.com**

## 🎯 Next Steps

1. Add your products with GPS coordinates
2. Set reorder levels for automatic alerts
3. Configure suppliers for auto-restocking
4. Set up cron job for scheduled monitoring
5. Customize the dashboard as needed

Happy inventory managing! 📦
