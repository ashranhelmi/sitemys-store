# 🧭 Buyer Flow & Lifecycle Requirements (eCommerce Apparel)

## 1. Overview

This document defines the full buyer journey and system behavior from product browsing to payment completion, including all edge cases such as abandoned carts, failed payments, and session expiry.

The flow is inspired by standard WooCommerce practices, adapted for a lightweight Laravel-based system.

---

## 2. Buyer Lifecycle States

A buyer/order can exist in the following states:

- Browsing
- Cart Active
- Checkout Started
- Pending Payment
- Paid
- Failed Payment
- Cancelled
- Expired
- Abandoned

---

## 3. Primary Flow (Happy Path)

1. User lands on site
2. User browses products
3. User adds item(s) to cart
4. User opens cart
5. User proceeds to checkout
6. User fills in details
7. User selects payment method (Billplz)
8. System creates order (status: Pending Payment)
9. System generates Billplz bill
10. User is redirected to payment gateway
11. User completes payment
12. Billplz callback/webhook received
13. System verifies payment
14. Order updated to **Paid**
15. User redirected to Thank You page
16. Confirmation email sent

---

## 4. Add to Cart Flow

### 4.1 Actions
- Add product to cart
- Update quantity
- Remove item

### 4.2 Conditions
- If same product added → increase quantity
- If stock exceeded → show error
- Cart persists via:
    - Session (guest)
    - Database (logged-in user)

---

## 5. Cart Behavior

### 5.1 Persistence
- Guest: session-based
- Logged-in: stored in database

### 5.2 Expiry
- Cart expires after X hours (e.g. 24h inactivity)

### 5.3 Refresh Behavior
- Page refresh → cart remains
- Session expired → cart cleared

---

## 6. Checkout Flow

### 6.1 Entry Conditions
- Cart must not be empty
- All items must be in stock

### 6.2 Steps
1. Enter personal details
2. Enter shipping address
3. Review order
4. Select payment method
5. Submit checkout

---

## 7. Order Creation Logic

When user clicks "Pay Now":

- Create Order:
    - Status: `Pending Payment`
    - Generate unique Order ID
- Store:
    - Customer details
    - Items
    - Total amount
- Lock stock (optional but recommended)

---

## 8. Payment Flow (Billplz)

### 8.1 Payment Initialization
- Create bill via Billplz API
- Store:
    - Bill ID
    - Payment URL

### 8.2 Redirect
- User redirected to Billplz payment page

---

## 9. Payment Outcomes

## 9.1 Success

- Billplz sends webhook callback
- Validate:
    - Amount
    - Signature
- Update order:
    - Status → Paid
- Reduce stock
- Send confirmation email
- Redirect user to Thank You page

---

## 9.2 Failed Payment

- User payment fails (insufficient funds, etc.)

System:
- Order remains `Pending Payment` OR mark as `Failed`
- Show message:
    - “Payment failed. Try again.”

User options:
- Retry payment
- Return to checkout

---

## 9.3 User Cancels Payment

- User exits payment page manually

System:
- Order status remains `Pending Payment`
- Allow retry

---

## 9.4 Payment Timeout (No Action)

- User does not complete payment within time (e.g. 15–30 mins)

System:
- Mark order as:
    - `Expired` OR keep as `Pending Payment`
- Release reserved stock (if implemented)

---

## 9.5 Abandoned at Payment Page

- User closes tab after redirect

System:
- Order remains `Pending Payment`
- Recovery options:
    - Email reminder (optional)
    - Resume payment link

---

## 10. Retry Payment Flow

- User revisits checkout or payment link

Conditions:
- If order still valid:
    - Generate new Billplz bill (recommended)
- Redirect again to payment

---

## 11. Edge Cases

### 11.1 Double Payment Attempt
- Prevent duplicate processing
- Validate using Billplz reference ID

---

### 11.2 Callback Delay
- Payment successful but callback delayed

Handling:
- Use:
    - Webhook (primary)
    - Manual verification endpoint (backup)

---

### 11.3 User Refresh During Checkout

- If before payment:
    - Keep form data (optional via local storage)
- If after order created:
    - Prevent duplicate orders

---

### 11.4 Stock Changes During Checkout

- If item becomes out of stock:
    - Block checkout
    - Notify user

---

### 11.5 Price Change During Checkout

- Lock price at checkout time

---

## 12. Cart Recovery Flow

### 12.1 Abandoned Cart

Trigger:
- User leaves site without checkout

Optional system:
- Save cart (email-based)
- Send reminder after X hours

---

### 12.2 Return User

- Restore cart if:
    - Session still active OR
    - Logged-in user

---

## 13. Session & Timeout Rules

| Scenario | Behavior |
|--------|--------|
| Session active | Cart persists |
| Session expired | Cart cleared |
| Order created | Stored in DB |
| Payment timeout | Order marked expired |

---

## 14. Order Status Lifecycle

- Pending Payment
- Paid
- Failed
- Cancelled
- Expired
- Refunded (future)

---

## 15. Email Notifications

### Trigger Events:

- Order Created → "Pending Payment"
- Payment Success → Confirmation
- Payment Failed (optional)
- Abandoned Cart (optional)

---

## 16. UX Considerations

- Show clear progress (Cart → Checkout → Payment → Done)
- Prevent duplicate clicks (disable button on submit)
- Provide retry payment option
- Display clear error states
- Mobile-friendly checkout

---

## 17. Suggested Improvements (Optional)

- Guest checkout only (no forced login)
- Magic login after purchase
- Save address for returning users
- One-page checkout (future optimization)

---
