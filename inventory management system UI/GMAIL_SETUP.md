# Gmail SMTP Configuration Guide

To properly configure Gmail SMTP for sending emails from the Inventory Management System:

## Step 1: Enable 2-Factor Authentication
1. Go to your Google Account settings
2. Enable 2-Factor Authentication if not already enabled

## Step 2: Generate App Password
1. Go to https://myaccount.google.com/apppasswords
2. Select "Mail" for the app
3. Select "Other" for the device and name it "Inventory Management System"
4. Copy the generated 16-character password

## Step 3: Update .env File
Replace the MAIL_PASSWORD in your .env file with the generated app password:

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

## Step 4: Clear Configuration Cache
Run these commands in your terminal:
```bash
php artisan config:clear
php artisan cache:clear
```

## Step 5: Test Email
You can test the email functionality by registering a new user account.

## Security Notes:
- Never commit your app password to version control
- The app password is different from your regular Gmail password
- Keep your app password secure and regenerate if compromised

## Troubleshooting:
- If emails still don't send, check your firewall settings
- Ensure port 587 is not blocked
- Verify the app password is correctly copied (no spaces)
- Check Laravel logs at storage/logs/laravel.log for detailed error messages
