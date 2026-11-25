# Hostinger Laravel Git Deployment Checklist

## 1) Prepare your hosting
- Make sure your hosting plan is active.
- Add your domain to the hosting account.
- Set the PHP version to the one required by your Laravel version.

## 2) Create the database
In hPanel, create a **MySQL** database and save:
- database name
- database user
- database password

## 3) Connect your Git repository
- Open **hPanel → Websites → Git**
- Connect your Git repository
- Choose the branch you want to deploy
- Pull/deploy the code to your hosting account

## 4) Place Laravel files correctly
Your Laravel project should be uploaded so the full app is **not exposed** in `public_html`.

Typical structure:
- Laravel app files: outside or above `public_html`
- Public web root: point to Laravel's `public` folder

## 5) Set up `.htaccess`
In `public_html`, create or edit `.htaccess` and add:

```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

## 6) Update `.env`
Edit your `.env` file with your database details:

```env
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

## 7) Generate the app key
If needed, run:

```bash
php artisan key:generate
```

## 8) Install dependencies
If SSH/Terminal is available, go to your project folder and run:

```bash
composer install --no-dev --optimize-autoloader
```

## 9) Run migrations
If your app uses a database:

```bash
php artisan migrate --force
```

## 10) Build frontend assets
If your Laravel app uses Vite / Node:

```bash
npm install
npm run build
```

## 11) Cache Laravel settings
Run:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 12) Test the site
- Open the domain in your browser
- Check the homepage
- Test login, forms, and database features
- Fix any file permission or `.env` issues if needed

## 13) Optional: set up cron jobs
If your app uses scheduled tasks, create a cron job like this:

```bash
/usr/bin/php /home/u12345678/domains/yourdomain.com/public_html/artisan schedule:run
```

Replace:
- `u12345678` with your hosting username
- `yourdomain.com` with your domain

## 14) Optional: if using SQLite instead
Set in `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/home/youruser/domains/yourdomain/public_html/database/database.sqlite
```

## 15) Future Git updates
When you push new code:
1. Push changes to your Git branch
2. Pull/deploy from hPanel Git
3. Run migrations if needed
4. Rebuild assets if frontend files changed
5. Clear/cache Laravel settings again if needed
