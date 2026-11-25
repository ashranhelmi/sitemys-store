# Laravel on Hostinger Business Web Hosting

## 1) Create the database
In hPanel, create a new MySQL database and note:
- database name
- database user
- database password

## 2) Update `.env`
Use this for MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

## 3) Upload your Laravel project
Upload the Laravel project files so the app files are **outside** `public_html`.

## 4) Point the site to Laravel's public folder
Create or edit `.htaccess` in `public_html` and add:

```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

## 5) Install dependencies
If SSH/Terminal is available, go to your Laravel project folder and run:

```bash
composer install --no-dev --optimize-autoloader
```

## 6) Generate app key
Run:

```bash
php artisan key:generate
```

## 7) Run migrations
Run:

```bash
php artisan migrate --force
```

## 8) Clear and cache config
Run:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 9) If your app uses Vite / frontend assets
Run:

```bash
npm install
npm run build
```

## 10) If using Git
- Connect your Git repo in hPanel
- Deploy/pull the repo
- Then run the commands above on the hosting account

## 11) If using SQLite instead
Set in `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/home/youruser/domains/yourdomain/public_html/database/database.sqlite
```
