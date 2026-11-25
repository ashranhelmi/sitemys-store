# 🧑‍💼 Seller Flow & Admin Lifecycle Requirements (eCommerce Apparel)

## 1. Overview

This document defines the complete seller/admin operational flow for managing an apparel eCommerce platform.

It includes:

- Product lifecycle
- Inventory management
- Order management
- Payment monitoring
- Shipment & delivery handling
- Refund & cancellation handling
- Stock synchronization
- Operational edge cases

The flow is inspired by WooCommerce operational behavior, adapted for a lean Laravel-based implementation.

---

# 2. Seller Operational Lifecycle

The seller/admin lifecycle consists of:

1. Product Creation
2. Inventory Management
3. Product Publishing
4. Order Monitoring
5. Payment Verification
6. Packing & Fulfillment
7. Shipment Handling
8. Delivery Monitoring
9. Refund / Cancellation Management
10. Reporting & Analytics

---

# 3. Admin Authentication Flow

## 3.1 Admin Login
- Email
- Password
- Optional 2FA (future)

---

## 3.2 Roles (Optional Future)
- Super Admin
- Staff
- Warehouse Staff
- Customer Support

---

# 4. Product Management Flow

## 4.1 Create Product

Admin creates:
- Product name
- Slug
- Description
- Short description
- Category
- Pricing
- Product images
- Sizes / variants
- SKU
- Stock quantity
- Weight (optional)
- Shipping dimensions (optional)

---

## 4.2 Product States

Products can exist in:

- Draft
- Published
- Hidden
- Out of Stock
- Archived

---

## 4.3 Product Publishing Flow

1. Create product
2. Save as draft
3. Upload images
4. Set inventory
5. Publish product
6. Product visible on storefront

---

## 4.4 Product Editing

Admin can update:
- Price
- Images
- Description
- Stock
- Variants

Conditions:
- Price changes affect future purchases only
- Existing orders preserve original pricing

---

## 4.5 Product Archiving

Instead of deleting:
- Mark as archived
- Hide from storefront
- Preserve historical order data

---

# 5. Inventory & Stock Management

## 5.1 Inventory Tracking

Track:
- Available stock
- Reserved stock
- Sold stock

---

## 5.2 Stock Deduction Flow

### Option A (Recommended)
Deduct stock only AFTER successful payment.

### Option B
Reserve stock immediately after checkout.

---

## 5.3 Stock Reservation Logic

When order created:
- Reserve inventory temporarily

If:
- Payment successful → deduct permanently
- Payment expired/cancelled → release stock

---

## 5.4 Out of Stock Handling

If stock reaches 0:
- Product marked:
    - Out of Stock
- Disable Add to Cart button

---

## 5.5 Overselling Prevention

Before payment:
- Revalidate inventory

Prevent:
- Multiple users buying same last item simultaneously

---

# 6. Order Management Flow

## 6.1 New Order Flow

After checkout:
- Create order
- Status:
    - Pending Payment

Admin dashboard updates automatically.

---

## 6.2 Order States

Orders can exist in:

- Pending Payment
- Paid
- Processing
- Packed
- Shipped
- Delivered
- Cancelled
- Expired
- Refunded

---

## 6.3 Order Detail View

Admin can view:
- Customer details
- Shipping address
- Purchased items
- Payment status
- Timeline/history
- Notes

---

## 6.4 Manual Order Notes

Admin can add:
- Internal notes
- Tracking updates
- Fulfillment notes

---

# 7. Payment Monitoring Flow

## 7.1 Payment Status Monitoring

Admin can monitor:
- Paid orders
- Pending payments
- Failed payments
- Expired payments

---

## 7.2 Billplz Webhook Handling

Upon successful callback:
- Verify signature
- Verify amount
- Mark order as Paid

---

## 7.3 Failed Payment Handling

If payment fails:
- Keep order as:
    - Failed OR Pending Payment

Admin can:
- Resend payment link
- Cancel order manually

---

## 7.4 Duplicate Payment Prevention

System must:
- Validate unique transaction IDs
- Prevent duplicate order processing

---

# 8. Fulfillment & Packing Flow

## 8.1 Processing Flow

After payment success:

1. Admin receives notification
2. Order enters:
    - Processing

---

## 8.2 Packing Flow

Admin:
- Picks items
- Verifies quantity
- Packs order

Update status:
- Packed

---

## 8.3 Packing Error Handling

If item unavailable:
- Contact customer
- Partial refund OR cancel order

---

# 9. Shipment Flow

## 9.1 Shipment Creation

Admin enters:
- Courier name
- Tracking number
- Shipping method

Update order:
- Status → Shipped

---

## 9.2 Customer Notification

Customer receives:
- Shipment email
- Tracking number

---

## 9.3 Shipment Tracking (Optional Future)

Integrate:
- NinjaVan
- J&T
- DHL
- PosLaju

---

# 10. Delivery Flow

## 10.1 Delivered Status

Admin manually marks:
- Delivered

OR

Courier integration auto-updates status.

---

## 10.2 Failed Delivery

If courier fails:
- Retry delivery
- Return to sender
- Contact customer

---

# 11. Cancellation Flow

## 11.1 Buyer Cancellation

Allowed:
- Before shipment

Admin actions:
- Approve cancellation
- Restore stock
- Refund payment if applicable

---

## 11.2 Seller Cancellation

Reasons:
- Out of stock
- Fraud detection
- Invalid address

Actions:
- Notify customer
- Refund payment

---

# 12. Refund Flow

## 12.1 Refund Conditions

Possible reasons:
- Damaged item
- Wrong item
- Lost shipment

---

## 12.2 Refund Process

1. Review request
2. Approve/reject
3. Update order:
    - Refunded
4. Restore inventory (optional)

---

# 13. Return Management

## 13.1 Return Request

Customer submits:
- Reason
- Images (optional)

---

## 13.2 Return States

- Requested
- Approved
- Rejected
- Item Received
- Refunded

---

# 14. Abandoned Order Handling

## 14.1 Pending Payment Orders

If payment incomplete:
- Keep pending for X minutes/hours

After timeout:
- Mark Expired
- Release stock

---

## 14.2 Admin Monitoring

Admin can:
- View abandoned orders
- Resend payment links
- Delete expired orders

---

# 15. Customer Management

## 15.1 Customer Accounts

Admin can view:
- Customer list
- Order history
- Spending history

---

## 15.2 Auto Registration

Customers automatically registered after purchase.

---

# 16. Notifications System

## 16.1 Admin Notifications

Notify admin:
- New order
- Payment success
- Refund request
- Failed payment

---

## 16.2 Customer Notifications

Notify customer:
- Order received
- Payment success
- Shipment update
- Refund update

---

# 17. Analytics & Reporting

## 17.1 Dashboard Metrics

Display:
- Revenue
- Orders
- Conversion
- Top-selling products

---

## 17.2 Inventory Reports

Track:
- Low stock
- Best sellers
- Unsold inventory

---

# 18. Security & Fraud Prevention

## 18.1 Payment Validation

Verify:
- Billplz signatures
- Amount matching

---

## 18.2 Spam Prevention

Protect:
- Checkout abuse
- Fake orders
- Bot submissions

---

# 19. Operational Edge Cases

## 19.1 Payment Success But Webhook Delayed

Handling:
- Manual verification endpoint
- Scheduled payment reconciliation

---

## 19.2 Stock Sold During Checkout

System:
- Revalidate stock before payment completion

---

## 19.3 Duplicate Order Submission

Prevent:
- Double-click submissions
- Page refresh duplicate orders

---

## 19.4 Partial Shipment

Future feature:
- Split shipments
- Partial fulfillment

---

# 20. Suggested Admin Modules

## Core Modules

- Dashboard
- Products
- Inventory
- Orders
- Customers
- Payments
- Shipping
- Refunds
- Settings

---

# 21. Suggested Future Enhancements

- Coupon management
- Multi-courier integration
- Automated invoices
- Export reports (CSV/PDF)
- Sales analytics
- Warehouse management
- Multi-admin permissions
- WhatsApp notifications
- AI product recommendations

---
