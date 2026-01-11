URL Shortener (Laravel)
This is a Laravel-based URL shortener with role-based access and company separation.
Super admin login credential
Email: superadmin@gmail.com
Password: password

Requirements
PHP 8.1+
Composer
MySQL

1. Clone the project
git clone <repository-url>
cd <project-folder>

2. Install dependencies
composer update


3. Update database details in .env:

4. Run Migrations
php artisan migrate

5. Run Seeders
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=SuperAdminSeeder

6. Now open http://localhost/urlshortener/public URL in browser