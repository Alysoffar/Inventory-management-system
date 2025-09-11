# Email Configuration Solutions for Inventory Management System

## CURRENT ERROR
Gmail SMTP authentication failed because you need to set up an App Password.

## SOLUTION 1: Gmail SMTP with App Password (Recommended)

### Step 1: Enable 2-Factor Authentication
1. Go to https://myaccount.google.com/security
2. Enable 2-Step Verification if not already enabled
3. Wait for it to be fully activated (may take a few minutes)

### Step 2: Generate App Password
1. Go to https://myaccount.google.com/apppasswords
2. Select "Mail" for the app
3. Select "Windows Computer" for device type
4. Click "Generate"
5. Copy the 16-character password (format: xxxx xxxx xxxx xxxx)

### Step 3: Update .env File
Replace your current email settings with:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=alysoffar06@gmail.com
MAIL_PASSWORD=your_16_character_app_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="alysoffar06@gmail.com"
MAIL_FROM_NAME="Inventory Management System"
```

### Step 4: Clear Cache
Run: php artisan config:clear

## SOLUTION 2: Mailtrap (Testing - Easier Setup)

If Gmail is too complex, use Mailtrap for testing:

1. Go to https://mailtrap.io and create free account
2. Create an inbox
3. Copy the SMTP credentials
4. Update .env:
```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="admin@inventoryms.com"
MAIL_FROM_NAME="Inventory Management System"
```

## SOLUTION 3: Use Log Driver (Current Working Solution)

Keep emails in logs until proper SMTP is set up:
```
MAIL_MAILER=log
MAIL_FROM_ADDRESS="admin@inventoryms.com"
MAIL_FROM_NAME="Inventory Management System"
```

Emails will be saved in: storage/logs/laravel.log

## TESTING
After any configuration change:
1. Run: php artisan config:clear
2. Visit: http://127.0.0.1:8000/test-email
3. Check for success/error message
