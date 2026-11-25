# 🧪 eCommerce Platform Test Script & QA Checklist

## 1. Overview

This document defines the complete testing scenarios for the eCommerce apparel platform.

The objective is to validate:

- Buyer flow
- Seller/Admin flow
- Payment flow
- Inventory behavior
- Email triggers
- Shipment lifecycle
- Edge cases
- Error handling
- Security & session behavior

---

# 2. Testing Environment

## Recommended Environments

### Development
- Local Docker/Sail

### Staging
- Sandbox Billplz API
- Test SMTP provider

---

# 3. Test Accounts

## Buyer Accounts
| Role | Email |
|---|---|
| Buyer 1 | buyer1@test.com |
| Buyer 2 | buyer2@test.com |

---

## Admin Accounts
| Role | Email |
|---|---|
| Admin | admin@test.com |

---

# 4. Buyer Flow Test Cases

---

# 4.1 Homepage & Product Browsing

## Test Case: HP-001
### Objective
Verify homepage loads correctly.

### Steps
1. Open homepage
2. Scroll entire page

### Expected Result
- Hero section visible
- Featured products visible
- Footer links accessible
- No layout breaking

---

# 4.2 Product Listing

## Test Case: SHOP-001

### Steps
1. Open Shop page

### Expected Result
- Products display correctly
- Price displayed
- Images loaded
- Add to Cart button visible

---

# 4.3 Product Detail Page

## Test Case: PDP-001

### Steps
1. Open product page

### Expected Result
- Product images visible
- Product details correct
- Variant selection working
- Quantity selector functional

---

# 4.4 Add To Cart

## Test Case: CART-001

### Steps
1. Add item to cart

### Expected Result
- Cart updated
- Quantity updated correctly
- Mini-cart updates (if exists)

---

# 4.5 Cart Quantity Update

## Test Case: CART-002

### Steps
1. Increase quantity
2. Decrease quantity

### Expected Result
- Total recalculated correctly

---

# 4.6 Remove Cart Item

## Test Case: CART-003

### Steps
1. Remove item

### Expected Result
- Item removed
- Cart total updated

---

# 4.7 Guest Checkout

## Test Case: CHK-001

### Steps
1. Proceed to checkout
2. Fill buyer details
3. Continue payment

### Expected Result
- Checkout successful
- Validation working

---

# 4.8 Form Validation

## Test Case: CHK-002

### Steps
1. Submit empty form

### Expected Result
- Required field errors shown

---

# 4.9 Invalid Email Validation

## Test Case: CHK-003

### Steps
1. Enter invalid email

### Expected Result
- Validation error displayed

---

# 4.10 Successful Payment

## Test Case: PAY-001

### Steps
1. Complete Billplz payment

### Expected Result
- Order marked Paid
- Redirect to Thank You page
- Confirmation email sent

---

# 4.11 Failed Payment

## Test Case: PAY-002

### Steps
1. Simulate failed payment

### Expected Result
- Order remains Pending/Failed
- Retry payment available

---

# 4.12 Cancelled Payment

## Test Case: PAY-003

### Steps
1. Cancel at Billplz page

### Expected Result
- Order remains Pending Payment

---

# 4.13 Payment Timeout

## Test Case: PAY-004

### Steps
1. Leave payment idle

### Expected Result
- Order expires after timeout
- Reserved stock released

---

# 4.14 Refresh During Checkout

## Test Case: CHK-004

### Steps
1. Refresh during checkout

### Expected Result
- No duplicate order created

---

# 4.15 Duplicate Submit Prevention

## Test Case: CHK-005

### Steps
1. Double click Pay Now

### Expected Result
- Only one order created

---

# 4.16 Auto Account Creation

## Test Case: ACC-001

### Steps
1. Complete purchase

### Expected Result
- Customer account auto-created

---

# 4.17 Password Reset

## Test Case: ACC-002

### Steps
1. Request password reset

### Expected Result
- Reset email received
- Link functional

---

# 4.18 Session Persistence

## Test Case: SES-001

### Steps
1. Add item to cart
2. Refresh page

### Expected Result
- Cart persists

---

# 4.19 Session Expiry

## Test Case: SES-002

### Steps
1. Wait until session timeout

### Expected Result
- Cart/session cleared appropriately

---

# 5. Seller/Admin Test Cases

---

# 5.1 Admin Login

## Test Case: ADM-001

### Steps
1. Login with admin account

### Expected Result
- Dashboard accessible

---

# 5.2 Create Product

## Test Case: PROD-001

### Steps
1. Create new product

### Expected Result
- Product saved successfully

---

# 5.3 Edit Product

## Test Case: PROD-002

### Steps
1. Edit product price/details

### Expected Result
- Changes reflected frontend

---

# 5.4 Archive Product

## Test Case: PROD-003

### Steps
1. Archive product

### Expected Result
- Product hidden from storefront

---

# 5.5 Inventory Update

## Test Case: INV-001

### Steps
1. Update stock quantity

### Expected Result
- Inventory updated correctly

---

# 5.6 Out Of Stock Handling

## Test Case: INV-002

### Steps
1. Set stock to 0

### Expected Result
- Product marked Out of Stock
- Add to Cart disabled

---

# 5.7 Overselling Prevention

## Test Case: INV-003

### Steps
1. Two buyers purchase last stock simultaneously

### Expected Result
- Only one successful purchase

---

# 5.8 View Orders

## Test Case: ORD-001

### Steps
1. Open orders page

### Expected Result
- Orders listed correctly

---

# 5.9 Order Status Update

## Test Case: ORD-002

### Steps
1. Update order status

### Expected Result
- Customer notified
- Status updated correctly

---

# 5.10 Shipment Creation

## Test Case: SHIP-001

### Steps
1. Add tracking number

### Expected Result
- Shipment email sent

---

# 5.11 Refund Request

## Test Case: REF-001

### Steps
1. Submit refund request

### Expected Result
- Admin notified

---

# 5.12 Approve Refund

## Test Case: REF-002

### Steps
1. Approve refund

### Expected Result
- Refund email sent
- Status updated

---

# 5.13 Reject Refund

## Test Case: REF-003

### Steps
1. Reject refund

### Expected Result
- Rejection email sent

---

# 5.14 Low Inventory Alert

## Test Case: INV-004

### Steps
1. Reduce stock below threshold

### Expected Result
- Low stock alert sent

---

# 5.15 Payment Monitoring

## Test Case: PAY-ADMIN-001

### Steps
1. Verify payment logs

### Expected Result
- Correct payment statuses displayed

---

# 6. Email Testing

---

# 6.1 Order Confirmation Email

## Test Case: EMAIL-001

### Expected Result
- Email delivered
- Correct order details shown

---

# 6.2 Payment Success Email

## Test Case: EMAIL-002

### Expected Result
- Email triggered immediately after payment

---

# 6.3 Shipment Email

## Test Case: EMAIL-003

### Expected Result
- Tracking number included

---

# 6.4 Refund Email

## Test Case: EMAIL-004

### Expected Result
- Refund details displayed correctly

---

# 7. Security Testing

---

# 7.1 Unauthorized Admin Access

## Test Case: SEC-001

### Steps
1. Access admin URL without login

### Expected Result
- Redirect to login page

---

# 7.2 CSRF Protection

## Test Case: SEC-002

### Expected Result
- Invalid CSRF requests blocked

---

# 7.3 Payment Validation

## Test Case: SEC-003

### Expected Result
- Fake callbacks rejected

---

# 7.4 SQL Injection

## Test Case: SEC-004

### Expected Result
- Inputs sanitized

---

# 7.5 XSS Testing

## Test Case: SEC-005

### Expected Result
- Script injection prevented

---

# 8. Performance Testing

---

# 8.1 Homepage Load Speed

## Test Case: PERF-001

### Expected Result
- Fast loading under acceptable threshold

---

# 8.2 Checkout Speed

## Test Case: PERF-002

### Expected Result
- Checkout responsive

---

# 8.3 Concurrent Buyers

## Test Case: PERF-003

### Expected Result
- Inventory consistency maintained

---

# 9. Mobile Testing

---

# 9.1 Responsive Layout

## Test Case: MOB-001

### Expected Result
- UI responsive on mobile/tablet

---

# 9.2 Mobile Checkout

## Test Case: MOB-002

### Expected Result
- Payment flow usable on mobile

---

# 10. Browser Compatibility

Test on:
- Chrome
- Safari
- Firefox
- Edge

---

# 11. Regression Testing Checklist

After every major update verify:
- Cart flow
- Checkout flow
- Payment flow
- Admin order flow
- Shipment flow
- Emails
- Inventory sync

---

# 12. Final QA Approval Checklist

| Module | Status |
|---|---|
| Homepage | ☐ |
| Shop | ☐ |
| Product Page | ☐ |
| Cart | ☐ |
| Checkout | ☐ |
| Payment | ☐ |
| Admin Dashboard | ☐ |
| Inventory | ☐ |
| Orders | ☐ |
| Emails | ☐ |
| Shipment | ☐ |
| Refunds | ☐ |
| Security | ☐ |
| Mobile | ☐ |

---
