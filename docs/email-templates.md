# 📧 eCommerce Email Templates & Notification Flow Requirements

## 1. Overview

This document defines all possible transactional email templates and notification flows between:

- Buyer ↔ Platform
- Admin ↔ Platform
- Seller/Admin ↔ Buyer

The system should support automated email triggering based on order lifecycle events, payment states, shipment status, customer account actions, and operational edge cases.

---

# 2. Email System Objectives

The email system should:

- Keep buyers informed
- Notify admin of operational actions
- Reduce abandoned carts
- Provide payment confidence
- Improve fulfillment transparency
- Maintain professional brand communication

---

# 3. Email Categories

## Buyer Emails
1. Account Emails
2. Cart Emails
3. Checkout Emails
4. Payment Emails
5. Shipment Emails
6. Delivery Emails
7. Refund & Return Emails
8. Support Emails

---

## Admin Emails
1. New Order Alerts
2. Payment Notifications
3. Refund Notifications
4. Shipment Alerts
5. Inventory Alerts
6. System Alerts

---

# 4. Buyer Email Templates

---

# 4.1 Welcome / Account Creation Email

## Trigger
- Auto account creation after first purchase

## Purpose
- Welcome customer
- Provide login access

## Includes
- Welcome message
- Login link
- Account dashboard link
- Order reference

---

# 4.2 Order Received Email

## Trigger
- Checkout submitted
- Order created

## Purpose
- Confirm order creation

## Includes
- Order ID
- Purchased items
- Amount
- Payment status:
    - Pending Payment
- Payment instructions
- Payment link

---

# 4.3 Payment Pending Reminder Email

## Trigger
- Payment not completed after X minutes/hours

## Purpose
- Recover abandoned payments

## Includes
- Pending payment reminder
- Order summary
- Payment link
- Expiry warning

---

# 4.4 Payment Successful Email

## Trigger
- Successful Billplz payment callback

## Purpose
- Confirm payment received

## Includes
- Payment confirmation
- Receipt summary
- Order details
- Shipping timeline expectations

---

# 4.5 Payment Failed Email

## Trigger
- Payment failed

## Purpose
- Inform customer
- Encourage retry

## Includes
- Failure notice
- Retry payment button
- Support contact

---

# 4.6 Payment Expired Email

## Trigger
- Payment session expired

## Purpose
- Inform order expired

## Includes
- Expiry notice
- Reorder suggestion
- Cart recovery link (optional)

---

# 4.7 Order Processing Email

## Trigger
- Admin starts fulfillment

## Purpose
- Inform customer order being prepared

## Includes
- Order status:
    - Processing
- Estimated shipping timeline

---

# 4.8 Shipment Confirmation Email

## Trigger
- Order marked shipped

## Purpose
- Inform shipment dispatched

## Includes
- Courier name
- Tracking number
- Tracking link
- Shipping summary

---

# 4.9 Out for Delivery Email (Optional)

## Trigger
- Courier update received

## Purpose
- Inform customer package arriving soon

## Includes
- Delivery reminder
- Tracking link

---

# 4.10 Delivered Email

## Trigger
- Order marked delivered

## Purpose
- Confirm successful delivery

## Includes
- Delivery confirmation
- Support instructions if issue exists
- Review request (future)

---

# 4.11 Cancellation Confirmation Email

## Trigger
- Order cancelled

## Purpose
- Confirm cancellation

## Includes
- Cancellation reason
- Refund info (if applicable)

---

# 4.12 Refund Approved Email

## Trigger
- Refund approved

## Purpose
- Inform customer refund initiated

## Includes
- Refund amount
- Refund timeline
- Order reference

---

# 4.13 Refund Rejected Email

## Trigger
- Refund denied

## Purpose
- Explain rejection

## Includes
- Reason for rejection
- Support escalation option

---

# 4.14 Return Request Received Email

## Trigger
- Customer submits return request

## Purpose
- Acknowledge request

## Includes
- Return ticket ID
- Next steps

---

# 4.15 Return Approved Email

## Trigger
- Return approved

## Purpose
- Provide return instructions

## Includes
- Return address
- Return instructions
- Timeline

---

# 4.16 Password Reset Email

## Trigger
- User requests password reset

## Includes
- Secure reset link
- Expiry notice

---

# 4.17 Account Verification Email (Optional)

## Trigger
- Email verification enabled

## Includes
- Verification link

---

# 4.18 Newsletter / Marketing Email (Future)

## Trigger
- Promotional campaigns

## Includes
- New arrivals
- Discounts
- Campaigns

---

# 4.19 Abandoned Cart Email (Optional)

## Trigger
- User leaves cart inactive

## Purpose
- Recover cart conversion

## Includes
- Cart items
- Checkout link
- Incentive/discount (optional)

---

# 4.20 Low Stock Warning to Customer (Optional)

## Trigger
- Limited stock remaining

## Includes
- “Only X left” urgency messaging

---

# 5. Admin / Seller Email Templates

---

# 5.1 New Order Notification

## Trigger
- New order created

## Includes
- Order ID
- Customer name
- Amount
- Payment status

---

# 5.2 Payment Successful Notification

## Trigger
- Payment completed

## Includes
- Paid order details
- Customer info
- Fulfillment reminder

---

# 5.3 Failed Payment Notification

## Trigger
- Payment failed

## Includes
- Order reference
- Customer info

---

# 5.4 Order Cancelled Notification

## Trigger
- Buyer/admin cancellation

## Includes
- Cancellation reason
- Refund status

---

# 5.5 Refund Request Notification

## Trigger
- Customer submits refund request

## Includes
- Order details
- Customer reason
- Attached images (optional)

---

# 5.6 Return Request Notification

## Trigger
- Return request submitted

## Includes
- Return reason
- Order reference

---

# 5.7 Shipment Reminder Notification

## Trigger
- Paid order not shipped after X hours/days

## Purpose
- Prevent fulfillment delay

---

# 5.8 Low Inventory Alert

## Trigger
- Stock below threshold

## Includes
- Product name
- Remaining quantity

---

# 5.9 Out of Stock Alert

## Trigger
- Stock reaches zero

## Includes
- Product details
- Restock reminder

---

# 5.10 Abandoned Cart Alert (Optional)

## Trigger
- High-value cart abandoned

## Includes
- Customer info
- Cart value

---

# 5.11 Daily Sales Summary (Optional)

## Trigger
- Daily scheduled report

## Includes
- Orders count
- Revenue
- Top products

---

# 5.12 Weekly / Monthly Report (Optional)

## Includes
- Revenue analytics
- Conversion
- Inventory movement

---

# 6. Email Trigger Matrix

| Event | Buyer Email | Admin Email |
|---|---|---|
| Account created | ✅ | ❌ |
| Order created | ✅ | ✅ |
| Payment pending | ✅ | ❌ |
| Payment success | ✅ | ✅ |
| Payment failed | ✅ | ✅ |
| Payment expired | ✅ | ❌ |
| Order processing | ✅ | ❌ |
| Shipment created | ✅ | ❌ |
| Delivered | ✅ | ❌ |
| Cancellation | ✅ | ✅ |
| Refund requested | ❌ | ✅ |
| Refund approved | ✅ | ✅ |
| Return requested | ✅ | ✅ |
| Low stock | ❌ | ✅ |
| Out of stock | ❌ | ✅ |

---

# 7. Email Design Requirements

## Layout Structure

All emails should include:
- Logo
- Header
- Body content
- CTA button
- Footer
- Support contact

---

## Mobile Optimization

Emails must:
- Be responsive
- Support dark mode compatibility
- Use readable typography

---

## Branding

Use:
- Brand colors
- Consistent typography
- Consistent button styling

---

# 8. Technical Email Requirements

## Email Queue System

Recommended:
- Queue email sending
- Avoid blocking checkout flow

---

## Delivery Service

Suggested:
- SMTP
- Mailgun
- Postmark
- Amazon SES

---

## Logging

Store:
- Email sent status
- Delivery timestamps
- Failed delivery logs

---

# 9. Email Security Considerations

- Secure password reset tokens
- Signed verification URLs
- Prevent duplicate sending
- Rate limit email triggers

---

# 10. Future Enhancements

- WhatsApp notifications
- SMS notifications
- Push notifications
- Multi-language email templates
- Dynamic recommendations
- AI-generated marketing campaigns

---
