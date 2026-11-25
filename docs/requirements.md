# 🛍️ eCommerce Website Requirements (Apparel)

> The original brief this store was built from. The visual identity section is historical: the storefront now follows the Sitemys design system described in the README.

## 1. Overview

A lightweight eCommerce platform for selling limited apparel items such as caps, hoodies, and sweatshirts.

- Initial product range: ~5 items
- Focus: clean UI, fast checkout, mobile-first, high conversion

---

## 2. Core Features

### 2.1 Customer (Buyer)
- Browse products
- View product details
- Add to cart
- Checkout & payment
- Automatic account creation upon purchase
- Basic order tracking
- Email notifications

### 2.2 Admin (Seller)
- Manage products
- Manage orders
- View customers
- Monitor payments
- Update order status

---

## 3. Frontend Structure

### 3.1 Homepage

**Sections:**
- Hero Section (featured product + CTA)
- Featured Products (grid display)
- Category Highlight (optional)
- Brand Story
- Trust Section (shipping, returns, security)
- Newsletter / CTA (optional)
- Footer (legal + contact links)

---

### 3.2 Shop Page

- Product listing (all items)
- Optional filters
- Sorting (price, latest)

**Product Card:**
- Image
- Name
- Price
- Add to cart

---

### 3.3 Product Detail Page

- Product gallery
- Name & price
- Description
- Size selection (if applicable)
- Quantity selector
- Add to Cart button

Optional:
- Size guide
- Reviews (future)

---

### 3.4 Cart Page

- List of items
- Update quantity
- Remove items
- Price breakdown:
  - Subtotal
  - Shipping
  - Total

CTA:
- Proceed to Checkout

---

### 3.5 Checkout Page

**Sections:**

1. Customer Info
   - Name
   - Email
   - Phone

2. Shipping Address
   - Address
   - City
   - State
   - Postcode

3. Order Summary
   - Items
   - Total

4. Payment Selection
   - Billplz

CTA:
- Pay Now

---

### 3.6 Payment Flow

- Redirect to Billplz
- Handle:
  - Success
  - Failed
  - Cancelled

---

### 3.7 Thank You Page

- Order confirmation
- Order ID
- Purchase summary

CTA:
- Continue shopping

---

## 4. Legal Pages

### 4.1 Privacy Policy
- Data collection
- Payment handling
- Email usage

### 4.2 Terms & Conditions
- Usage agreement
- Liability
- Order terms

### 4.3 Return & Refund Policy
- Eligibility
- Return timeframe (e.g. 7 days)
- Refund conditions
- Non-returnable items

---

## 5. Authentication Logic

### Buyer Registration (Auto)
- Auto-create account after checkout
- Email as username
- Password:
  - Auto-generated OR
  - Magic login link

### Login System
- Email + password
- Optional magic link login

---

## 6. Backend (Admin Panel)

### 6.1 Dashboard
- Total orders
- Revenue
- Recent orders

---

### 6.2 Product Management
- Create / Edit / Delete products

Fields:
- Name
- Price
- Description
- Images
- Sizes / variants
- Stock quantity

---

### 6.3 Order Management
- View all orders
- View order details:
  - Customer info
  - Items
  - Payment status

Order status:
- Pending
- Paid
- Shipped
- Completed

---

### 6.4 Customer Management
- Customer list
- Order history

---

### 6.5 Payment Monitoring
- Track Billplz transactions
- Sync payment status

---

## 7. Payment Gateway Integration (Billplz)

### Requirements:
- Create Billplz collection
- Generate bill during checkout
- Redirect user to payment page
- Handle callback / webhook

### Flow:
1. Create bill via API
2. Redirect user
3. Receive callback
4. Validate payment
5. Update order status (PAID)

---

## 8. Email Notifications

### Customer:
- Order confirmation
- Payment success
- Shipping update

### Admin:
- New order alert

---

## 9. Technical Considerations

- Mobile-first design
- Optimized images
- Basic SEO setup
- HTTPS security
- CSRF protection
- Payment validation

---

## 10. Future Enhancements

- Promo codes / discounts
- Inventory alerts
- Reviews & ratings
- Wishlist
- Multi-payment gateway
- Analytics dashboard

---

## 11. Suggested Stack

- Backend: Laravel
- Frontend: Blade + Alpine.js
- Styling: Tailwind CSS
- Payment: Billplz API
- Email: SMTP / Mailgun

---

## 12. Tech Stack Analysis & Recommendation

### The Key Question: Reuse the sellerdesk template?

The sellerdesk template runs **Laravel 12 + Inertia.js + React 19 + Tailwind v4 + shadcn/ui**.
The original suggested stack in §11 was **Laravel + Blade + Alpine.js**.

These serve different use cases. Here's the honest breakdown:

---

### Option Comparison

| Factor | Blade + Alpine.js | Inertia + React (sellerdesk template) |
|--------|------------------|--------------------------------------|
| SEO (product pages) | ✅ Native — server-rendered HTML | ⚠️ SPA — poor SEO without SSR setup |
| Mobile performance | ✅ Lighter, faster first load | ⚠️ Larger JS bundle |
| Admin panel richness | ⚠️ Good but limited | ✅ shadcn/ui tables, charts, full React |
| Dev speed (for you) | ⚠️ Starting fresh | ✅ Template already configured |
| Cart / dynamic UI | ⚠️ Alpine.js — manageable | ✅ React state — cleaner |
| Familiarity | Medium | ✅ You already know it from sellerdesk |
| Two-frontend split | N/A | Admin = React, Storefront = Blade? |

---

### Recommendation: Hybrid — Split Frontend by Audience

**Use the sellerdesk template as your base, but split the frontend by role.**

```
Admin Panel     → Inertia + React + shadcn/ui  (same as sellerdesk)
Customer Store  → Blade + Alpine.js + Tailwind  (server-rendered for SEO)
```

**Why this works for this project:**

1. The admin panel (orders, products, customers, payments) is where shadcn/ui shines — data tables, dashboards, status updates. Reuse 100% of the sellerdesk template structure here.
2. The customer storefront (~5 product pages) needs SEO. Lifestyle brands live on Google Images, Pinterest, and social links. Blade ensures product pages are fully crawlable with zero extra config.
3. You're not doubling work — both sides share the same Laravel backend, same models, same routes. Only the view layer differs.
4. Alpine.js is lightweight enough for cart drawer, quantity selectors, and mobile nav without React overhead.

**The single-stack alternative (if you want simplicity):**

If SEO is not a concern (brand awareness comes from social/word-of-mouth only), go full Inertia + React and reuse the template end-to-end. Faster to build, one consistent codebase. For ~5 products on a boutique label, this is a defensible choice.

---

### Final Recommended Stack

| Layer | Choice |
|-------|--------|
| Backend | Laravel 12 |
| Admin Frontend | Inertia.js + React 19 + shadcn/ui (reuse sellerdesk template) |
| Storefront Frontend | Blade + Alpine.js |
| Styling | Tailwind CSS v4 (both sides) |
| Payment | Billplz API |
| Email | Mailgun (or Laravel SMTP + Mailtrap for dev) |
| Auth | Laravel Breeze (already in template) |
| Storage | Laravel Storage (local) → S3-compatible for prod |

---

## 13. Design Direction

### Concept: "Dark & Clean Streetwear"

A modern boutique apparel brand aesthetic — minimal, confident, product-forward.
Not overly styled. Let the products breathe. The design is the frame, not the painting.

**Mood:** Urban · Quiet luxury · Wearable art  
**Feel:** The site should feel like walking into a clean concept store — nothing unnecessary, everything intentional.

---

### Color Palette

**Primary Direction: Dark Base / Warm Neutral**

| Role | Name | Hex |
|------|------|-----|
| Background | Near Black | `#0C0C0C` |
| Surface / Card | Dark Charcoal | `#161616` |
| Border / Divider | Subtle Gray | `#2A2A2A` |
| Primary Text | Warm White | `#F2EDE8` |
| Secondary Text | Muted Warm | `#9A9188` |
| Accent / CTA | Warm Sand | `#C8A87A` |
| Accent Hover | Lighter Sand | `#D9BC99` |
| Success | Soft Green | `#4CAF82` |
| Error | Muted Red | `#E05555` |

**Alternative Direction: Light & Clean (if brand leans minimal/premium-casual)**

| Role | Hex |
|------|-----|
| Background | `#FAFAF8` |
| Surface | `#FFFFFF` |
| Text | `#111111` |
| Accent | `#1A1A1A` (monochrome — clothing does the colour work) |

> **Recommendation**: Start with Dark Base. Apparel brands in the streetwear/limited-drop space (caps, hoodies) almost universally use dark palettes. It makes product photography pop. Can always invert to light for a seasonal campaign.

---

### Typography

| Role | Font | Weight |
|------|------|--------|
| Display / Hero | Geist or Inter | 800 (ExtraBold) |
| Section Headings | Inter | 600–700 |
| Body | Inter | 400 |
| Price / Labels | Inter | 500–600, tracked wide |
| Nav | Inter | 500, uppercase, tracked |

All available via Google Fonts or Bunny Fonts. Inter covers everything cleanly. If you want a personality bump, pair a serif display font (e.g., Playfair Display) for brand name only — everything else stays Inter.

---

### Layout Direction

#### Storefront (Customer-Facing)

**Homepage**
```
[ Full-width Hero — product lifestyle photo, minimal text overlay, single CTA ]
[ Featured Products — 2-col mobile / 3-col desktop, clean cards ]
[ Brand Story — short copy, left-text / right-image split ]
[ Trust Bar — icons: free shipping / secure checkout / easy returns ]
[ Footer — links, legal, social ]
```

**Shop Page**
```
[ Filter bar (top, horizontal) — size, sort ]
[ Product Grid — 2-col mobile / 3-col desktop ]
[ Product Card — image (hover = alt angle), name, price, quick-add ]
```

**Product Detail Page**
```
[ Left: Image Gallery — main + thumbnails, swipeable mobile ]
[ Right: Name, Price, Size selector, Qty, Add to Cart CTA ]
[ Below: Description, Size Guide (accordion) ]
```

**Cart**
- Slide-over drawer (right side), not a full page
- Shows items, qty controls, subtotal, checkout CTA
- Persisted in session / localStorage

**Checkout**
- Single-page layout, no multi-step wizard (reduces drop-off)
- Left column: form (customer info + address)
- Right column: sticky order summary
- Single CTA: Pay Now → redirects to Billplz

**Thank You Page**
- Full-width, centered
- Order confirmed ✓, Order ID, brief summary
- CTA: Continue Shopping

---

#### Admin Panel (Inertia + React)

Reuse the sellerdesk `AppLayout` with sidebar as-is. Sections:

```
Sidebar:
├── Dashboard
├── Orders
├── Products
├── Customers
└── Payments
```

- Dashboard: 3 stat cards (revenue, orders, pending) + recent orders table
- Orders: TanStack Table with filters (status, date), row click → detail drawer
- Products: Card grid + create/edit modal with image upload
- Customers: Table with order count + last order date
- Payments: Billplz transaction log, sync status badge

---

### Mobile-First Notes

- Hero: image fills viewport, text stays minimal to not compete
- Product cards: full-width on small screens, tap target ≥ 44px
- Cart drawer: full-width on mobile, slides from bottom
- Checkout: single column, large input fields, sticky CTA button at bottom
- Nav: hamburger menu, minimal links

---

