# sitemys. store

An open-source Laravel shop for small Malaysian brands: a fast storefront with a **shop-the-look video hero**, FPX checkout through Billplz, and an admin panel for products, orders, customers and payments.

It ships with a fictional demo label so it looks alive the moment you clone it: **Drop 01 · Simpang**, ten streetwear pieces that each carry one embroidered red dot.

> **Demo content.** The products, people and places in the images and clips are AI-generated concept art. Customer and order data is seeded and fictional. Swap in your own products from the admin panel.

## What's inside

**Storefront** (Blade + Alpine.js, server-rendered)
- Shop-the-look hero: short clips per look, with tappable hotspots pinned to each garment. The pins re-project for any screen size, so they stay on the clothes from phone to widescreen. There's an "In this look" strip with the total price
- Product grid with real stock badges ("3 left", "Sold out")
- Product page with size picker, quantity, stock notice and "Pairs well with"
- Cart drawer and cart page, with flat shipping that becomes free above a threshold (both set in config)
- Checkout with FPX online banking through Billplz, a retry page for unfinished payments, and a thank-you page
- Privacy, terms and return policy pages; branded 403/404/500 pages

**Admin** (Inertia + React + shadcn/ui)
- Dashboard: revenue trend, month-on-month comparison, low-stock warning, top sellers, recent orders and payments
- Products: create, edit, delete, sizes, images, stock
- Orders: status filters and fulfilment flow (processing → packed → shipped → delivered → completed, refunds and cancellations), with an email to the customer at each step
- Customers and payments lists
- Light and dark mode

**Emails**: order pending, confirmed, processing, shipped, delivered, payment failed, expired, cancelled, refund, welcome, new-order alert, low-stock alert.

## Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 (PHP 8.2+) |
| Storefront | Blade + Alpine.js |
| Admin | Inertia.js + React 19 + shadcn/ui |
| Styling | Tailwind CSS v4, Sitemys design tokens |
| Database | SQLite by default (MySQL works too) |
| Payments | Billplz (FPX) |

## Quick start

```bash
git clone https://github.com/ashranhelmi/sitemys-store.git
cd sitemys-store
composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed      # 10 demo products, ~90 demo orders, a local demo admin

php artisan serve               # http://localhost:8000
```

- Storefront: `http://localhost:8000`
- Admin: `http://localhost:8000/login`. Locally, the seeder creates `admin@example.com` / `password`. Change it before showing anyone.

### Admin accounts

There's no public sign-up. Checkout creates a customer account for every buyer, so only accounts flagged as admin can open the dashboard. Create or promote one with:

```bash
php artisan store:admin you@yourdomain.com --name="Your Name"
```

The demo admin is only seeded when `APP_ENV` is `local` or `testing`.

## Make it yours

Everything brand-specific lives in `config/store.php` (most values can be set from `.env`):

| Setting | `.env` | Default |
|---|---|---|
| Store name | `STORE_NAME` | Sitemys Store |
| Contact email (legal pages) | `STORE_EMAIL` | hello@example.com |
| Order number prefix | `STORE_ORDER_PREFIX` | SMS |
| Flat shipping fee (RM) | `STORE_SHIPPING_FLAT` | 10 |
| Free shipping from (RM, 0 = never) | `STORE_FREE_SHIPPING_OVER` | 150 |
| Demo notice in footer | `STORE_DEMO` | true |

**Shop-the-look hero.** Each entry in `looks` has a clip (`public/videos`), a poster (the clip's first frame, `public/images/looks`), its pixel size, and pins. A pin is a product slug plus `x`/`y` as fractions of the frame (0 to 1). To place one, open the poster, find the garment, and divide its pixel position by the width and height. Pins for products that are inactive or deleted are hidden automatically. Visitors who prefer reduced motion see the posters instead of video.

## Payments (Billplz)

```env
BILLPLZ_API_KEY=
BILLPLZ_COLLECTION_ID=
BILLPLZ_X_SIGNATURE_KEY=
BILLPLZ_SANDBOX=true    # false for live payments
```

Get sandbox keys at [billplz-sandbox.com](https://www.billplz-sandbox.com). Billplz calls back to `/payment/callback` (redirect) and `/webhook/billplz` (server-to-server, CSRF-exempt, X-Signature verified), so the site needs a public HTTPS URL for real payments.

## Email

Set `MAIL_*` in `.env`. Use a test inbox such as Mailtrap during development and a real mailbox in production. `MAIL_MAILER=log` writes emails to `storage/logs` instead of sending them.

## Tests

```bash
php artisan test
```

The suite covers auth, the profile, the admin gate (a customer account must not open the dashboard), closed registration, the storefront home page and the shipping rule.

## Deploying

There are notes for shared hosting (Hostinger) in [`docs/hostinger-deploy.md`](docs/hostinger-deploy.md) and [`docs/hostinger-git-checklist.md`](docs/hostinger-git-checklist.md). The original requirements, buyer and seller flows, email templates and the QA checklist are also in [`docs/`](docs).

## Routes

| Area | Routes |
|---|---|
| Storefront | `/` · `/shop` · `/shop/{slug}` · `/cart` · `/checkout` · `/thank-you/{order}` · `/payment/retry/{order}` |
| Billplz | `GET /payment/callback` · `POST /webhook/billplz` |
| Admin | `/dashboard` · `/dashboard/admin/{products,orders,customers,payments}` · `/dashboard/profile` |

## Credits

Built by [sitemys.](https://sitemys.com), a web and systems studio in Malaysia. Product photos and look stills were generated with OpenAI's image model; the look clips were generated from those stills with BytePlus Seedance. All of it is demo content, not real products or real people.

## License

[MIT](LICENSE)
