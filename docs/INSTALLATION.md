# Sales Application - Implementation & Installation Guide

This guide provides instructions for setting up the Sales Application on a new system (Local or Server).

## Prerequisites
- **PHP**: 8.1 or higher
- **Web Server**: Apache (XAMPP/WAMP) or Nginx
- **Database**: MySQL/MariaDB
- **Composer**: PHP Dependency Manager
- **Node.js & NPM**: For frontend assets (optional if pre-compiled)

## 1. Setup Files
1. Clone or copy the project folder to your web root (e.g., `C:/xampp/htdocs/Sales`).
2. Open a terminal in the project directory.

## 2. Install Dependencies
Run the following commands:
```bash
composer install
npm install
```

## 3. Environment Configuration
1. Copy `.env.example` to `.env`.
2. Generate an application key:
   ```bash
   php artisan key:generate
   ```
3. Open the `.env` file and configure your database settings:
   ```env
   DB_DATABASE=sales_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

## 4. Database Setup
1. Create a new database in MySQL (e.g., `sales_db`).
2. Run migrations and seeders:
   ```bash
   php artisan migrate:fresh --seed
   ```
   *Note: This will create all tables and the default administrator account.*

## 5. Storage Link
Link the storage folder to the public directory:
```bash
php artisan storage:link
```

## 6. Accessing the App
1. Start your XAMPP/WAMP services.
2. Visit `http://localhost/Sales/public` in your browser.
3. **Default Credentials**:
   - **Email**: `admin@sales.com` (or the one set in seeders)
   - **Password**: `password`

## 7. Production Deployment (Optional)
If deploying to a live server:
- Ensure `APP_ENV=production`.
- Set `APP_DEBUG=false`.
- Optimize the app:
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```
