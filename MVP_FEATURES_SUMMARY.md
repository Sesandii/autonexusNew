# AutoNexus Customer Portal - MVP Features Summary

## Executive Summary

This document provides a comprehensive analysis of the AutoNexus customer portal's current implementation status, focusing on the **'File a Complaint'** and **'Rate Your Service'** features, along with common user-related and feedback functionality. Based on this analysis, we outline the remaining work required to deliver a **minimal, robust MVP version**.

**Current Status**: The portal has strong foundational features for service booking, tracking, and feedback submission. However, critical gaps exist in:
- Direct customer complaint filing (currently receptionist-only)
- Customer visibility into their submitted feedback/complaints
- Payment and invoice management
- Notification systems
- Two-way communication features

---

## 1. 'File a Complaint' Feature Analysis

### ✅ Current Implementation (Receptionist-Centric)

**Implemented Functionality:**
- ✅ Receptionist can file complaints on behalf of customers
- ✅ Phone lookup to auto-populate customer information
- ✅ Vehicle selection and linkage to complaints
- ✅ Priority levels (Low, Medium, High)
- ✅ Status tracking (Open, In Progress, Resolved, Canceled)
- ✅ Search and filter complaints by status, priority, description
- ✅ Edit and delete complaints
- ✅ View customer complaint history
- ✅ Supervisor and manager read-only views

**Technical Stack:**
- **Controllers**: `Receptionist/ComplaintController.php`, `supervisor/ComplaintsController.php`, `manager/ComplaintsController.php`
- **Models**: `ComplaintModel.php` (CRUD + filtering), `supervisor/Complaint.php` (read-only)
- **Views**: 5 receptionist views (list, create, edit, detail, history)
- **Database**: `complaints` table with customer_id, vehicle_id, priority, status, description

### ❌ Critical Missing Features for Customer MVP

#### **P0 - Essential for Launch**
1. **Customer Self-Service Complaint Filing**
   - No customer-facing complaint controller
   - No complaint submission form in customer portal
   - Customer cannot file complaints directly
   - **Action Required**: Create `customer/ComplaintController.php` with `create()` and `store()` methods

2. **Customer Complaint Tracking Dashboard**
   - Customer cannot view their own submitted complaints
   - No complaint status updates visible to customers
   - **Action Required**: Add `index()` method to show customer's complaints with status

3. **Complaint Status Notifications**
   - No alerts when complaint status changes
   - Customer unaware of resolution progress
   - **Action Required**: Implement email/in-app notifications on status updates

4. **Complaint Response/Comments System**
   - One-way communication only
   - Customer cannot respond to staff updates
   - No activity log visible to customer
   - **Action Required**: Add comments/notes system with timestamps and user tracking

#### **P1 - Important for User Experience**
5. **Complaint Categorization**
   - Only free-text description field
   - No predefined complaint types (Service Quality, Billing, Scheduling, Staff Behavior, etc.)
   - **Action Required**: Add `complaint_category` enum field to database and dropdown in form

6. **File Attachments**
   - Cannot upload photos of issues (damaged parts, incorrect work, etc.)
   - **Action Required**: Add file upload functionality with image preview

7. **Complaint Resolution Feedback**
   - No way for customer to confirm issue is resolved
   - No satisfaction rating after resolution
   - **Action Required**: Add "Mark as Resolved" button and optional satisfaction rating

8. **Expected Resolution Timeline**
   - No SLA tracking
   - Customer has no expectation of when complaint will be addressed
   - **Action Required**: Add `expected_resolution_date` field and display in customer view

#### **P2 - Nice to Have**
9. Complaint escalation path if unresolved beyond SLA
10. Auto-assignment logic based on complaint category
11. Complaint analytics dashboard for management
12. Integration with service appointments (link complaint to specific service)

---

## 2. 'Rate Your Service' (Feedback) Feature Analysis

### ✅ Current Implementation (Customer Submission + Admin Reply)

**Implemented Functionality:**
- ✅ Customer can rate completed services (1-5 stars)
- ✅ Optional written feedback/comments
- ✅ Prevents duplicate ratings (UNIQUE constraint on appointment)
- ✅ Security: Verifies appointment belongs to customer
- ✅ Admin reply system with tracking (reply_text, replied_at, replied_by)
- ✅ Admin filtering by rating, reply status, date, search text
- ✅ Average rating calculation across all feedback
- ✅ Visual feedback cards with color-coded ratings
- ✅ Supervisor read-only view

**Technical Stack:**
- **Controllers**: `customer/FeedbackController.php`, `admin/FeedbackController.php`, `supervisor/SupervisorFeedbackController.php`
- **Models**: `admin/Feedback.php` (full CRUD + advanced filtering)
- **Views**: Customer rating form, admin feedback management with reply forms
- **Database**: `feedback` table with appointment_id, user_id, rating, comment, reply system

### ❌ Critical Missing Features for Customer MVP

#### **P0 - Essential for Launch**
1. **Customer View of Own Feedback**
   - Customer cannot see their submitted reviews
   - Cannot track which appointments have been rated
   - **Action Required**: Add `customer/feedback/history` view showing all customer's feedback

2. **Customer View of Admin Replies**
   - Admin replies exist but customer has no visibility
   - No notification when admin responds
   - **Action Required**: Display admin replies in customer feedback history

3. **Email Notification on Admin Reply**
   - Customer unaware when their feedback receives a response
   - **Action Required**: Trigger email when admin posts reply

#### **P1 - Important for User Experience**
4. **Edit/Update Review**
   - No way to modify submitted feedback
   - **Action Required**: Add edit functionality within 24-48 hours of submission

5. **Feedback Prompts/Reminders**
   - No automated prompts after service completion
   - Relies on customer remembering to rate
   - **Action Required**: Email/SMS 24 hours after appointment completion

6. **Multi-Dimensional Ratings**
   - Only single overall rating
   - No breakdown by staff, cleanliness, timeliness, pricing, quality
   - **Action Required**: Add category-specific ratings (optional enhancement)

7. **Photo Attachments**
   - Cannot attach before/after photos
   - **Action Required**: Add image upload to feedback form

8. **Public Reviews Display**
   - No customer-facing page showing service ratings
   - Cannot help new customers choose services
   - **Action Required**: Create public testimonials page with approved reviews

#### **P2 - Analytics & Operations**
9. **Rating Analytics Dashboard**
   - No trend analysis (rating changes over time)
   - No service/branch performance comparison
   - No staff attribution
   - **Action Required**: Create reports showing rating trends, average by service/branch

10. **Low Rating Escalation**
    - No workflow for 1-2 star ratings
    - Critical issues may go unnoticed
    - **Action Required**: Automatic manager notification for ratings below 3 stars

11. **Customer Follow-up for Negative Reviews**
    - No proactive outreach for dissatisfied customers
    - **Action Required**: Flag low ratings for follow-up contact

12. **NPS (Net Promoter Score) Calculation**
    - No standardized customer loyalty metric
    - **Action Required**: Add NPS survey and scoring

---

## 3. Common User & Feedback Functionality Analysis

### ✅ Current Customer Portal Features

**User Management:**
- ✅ Customer registration and login
- ✅ Role-based access control (customer/admin/receptionist/supervisor/manager)
- ✅ Profile management (edit name, email, phone, address)
- ✅ Multi-vehicle management per customer
- ✅ Multi-language support (English, Tamil, Sinhala)

**Core Portal Features:**
- ✅ Dashboard with next appointment, service reminders, pending feedback
- ✅ Appointment booking (branch → vehicle → service → datetime)
- ✅ Appointment viewing and cancellation
- ✅ Service history with technician info and pricing
- ✅ Real-time service tracking with search/filter
- ✅ Mileage-based service reminders
- ✅ Available services browsing by branch

**UI/UX:**
- ✅ Responsive design with sidebar navigation
- ✅ Flash message notifications
- ✅ Font Awesome icons
- ✅ Professional styling with brand colors

### ❌ Critical Missing Features for Customer Portal MVP

#### **P0 - Must Have for MVP**

**1. Payment & Invoice System** (Highest Priority)
- ❌ No customer invoice viewing
- ❌ No payment processing (no gateway integration)
- ❌ No payment status tracking
- ❌ No invoice download/print
- ❌ No payment history
- **Impact**: Customers cannot complete the service lifecycle
- **Action Required**: 
  - Create `customer/InvoiceController.php` with `index()` and `view()` methods
  - Integrate Stripe/PayPal for online payments
  - Display invoices linked to completed appointments
  - Enable PDF invoice download

**2. Notification System** (Critical Gap)
- ❌ No push notifications
- ❌ No appointment confirmation emails (framework exists but not wired)
- ❌ No service status update emails
- ❌ No payment receipt emails
- ❌ No SMS notifications
- ❌ No in-app notification center
- **Impact**: Poor customer experience, missed appointments, lack of transparency
- **Action Required**:
  - Wire existing PHPMailer to trigger on key events
  - Create email templates for: booking confirmation, service updates, completion, reminders
  - Add notification preferences page
  - Build in-app notification center

**3. Two-Way Communication** (Important for Support)
- ❌ No messaging/chat with service center
- ❌ No support ticket system
- ❌ Customer complaints require receptionist intermediary
- **Impact**: Customer frustration, delayed issue resolution
- **Action Required**:
  - Implement customer complaint submission (see Section 1)
  - Add basic contact/inquiry form
  - Optional: Integrate live chat widget (Tawk.to, Crisp)

**4. Password Management** (Security Basic)
- ❌ No password change functionality
- ❌ No password reset via email
- ❌ No email verification on registration
- **Impact**: Security vulnerability, account recovery issues
- **Action Required**:
  - Add "Change Password" in profile settings
  - Implement password reset with email token
  - Add email verification on new registrations

#### **P1 - Should Have for Better UX**

**5. Appointment Enhancements**
- ❌ No rescheduling (only cancel + rebook)
- ❌ No special requests/notes field
- ❌ No booking confirmation email
- ❌ No service cost estimates during booking
- **Action Required**:
  - Add `reschedule()` method to AppointmentsController
  - Add `customer_notes` field to appointments table
  - Display service pricing during booking flow

**6. Enhanced Dashboard**
- ❌ Placeholder statistics only
- ❌ No spending summary
- ❌ No appointment statistics
- **Action Required**:
  - Calculate and display: total spent, services this year, average service cost
  - Add quick action buttons (Book Now, View Invoices, File Complaint)

**7. Service Recommendations**
- ❌ No AI/rule-based service suggestions
- ❌ No maintenance schedule based on vehicle age/mileage
- **Action Required**:
  - Create recommendation engine based on vehicle type and last service date
  - Display on dashboard: "Recommended: Oil Change (due at 5000 km)"

#### **P2 - Nice to Have**

8. Digital warranty tracking
9. Parts replacement history
10. Service package bundles (e.g., "Complete Tune-Up Package")
11. Loyalty program/points system
12. Referral system
13. GDPR data export
14. Two-factor authentication
15. Social media login (Google, Facebook)

---

## 4. Minimal MVP Implementation Checklist

### **Phase 1: Critical Customer-Facing Features** (4-6 weeks)

#### Week 1-2: Complaint System
- [ ] Create `app/controllers/customer/ComplaintController.php`
  - [ ] `index()` - List customer's complaints with status
  - [ ] `create()` - Show complaint submission form
  - [ ] `store()` - Save complaint with validation
  - [ ] `show($id)` - View single complaint with activity log
- [ ] Create `app/views/customer/complaints/` directory with views:
  - [ ] `index.php` - List view with filters
  - [ ] `create.php` - Complaint form (category, description, vehicle, priority)
  - [ ] `show.php` - Detail view with status timeline
- [ ] Add complaint category dropdown (enum in database)
- [ ] Add navigation link in customer sidebar
- [ ] Email notification when complaint status changes
- [ ] Add comments system for two-way communication

#### Week 2-3: Feedback Visibility & Reply System
- [ ] Create `customer/feedback/history` view
  - [ ] Show all customer's submitted feedback
  - [ ] Display admin replies with timestamps
  - [ ] Show which appointments have been rated
- [ ] Email notification when admin replies to feedback
- [ ] Edit feedback functionality (within 48 hours)
- [ ] Auto-prompt email 24 hours after service completion

#### Week 3-4: Payment & Invoice System
- [ ] Create `app/controllers/customer/InvoiceController.php`
  - [ ] `index()` - List all customer invoices
  - [ ] `view($id)` - Display single invoice with line items
  - [ ] `download($id)` - Generate PDF for download
- [ ] Create `app/views/customer/invoices/` directory
  - [ ] `index.php` - Invoice list with payment status
  - [ ] `view.php` - Invoice detail view
- [ ] Integrate payment gateway (Stripe recommended)
  - [ ] Add payment form to invoice view
  - [ ] Process payment and update invoice status
  - [ ] Email payment receipt
- [ ] Add "Invoices & Payments" to customer sidebar

#### Week 4-5: Notification System
- [ ] Wire PHPMailer to customer portal events:
  - [ ] Appointment booking confirmation
  - [ ] Service status updates (Confirmed, In Progress, Completed)
  - [ ] Feedback submission confirmation
  - [ ] Admin reply to feedback
  - [ ] Complaint status updates
  - [ ] Payment receipt
- [ ] Create email templates for each notification type
- [ ] Add in-app notification center (bell icon in header)
  - [ ] Store notifications in `notifications` table
  - [ ] Mark as read functionality
  - [ ] Dropdown showing recent 5 notifications
- [ ] Add notification preferences page (opt-in/out per category)

#### Week 5-6: Essential Security & Account Management
- [ ] Password change functionality in profile settings
- [ ] Password reset with email token
- [ ] Email verification on registration
- [ ] Appointment rescheduling (vs. cancel + rebook)
- [ ] Add `customer_notes` field to booking form
- [ ] Display service pricing during booking

### **Phase 2: Enhanced UX** (2-3 weeks)

#### Week 7: Dashboard Enhancements
- [ ] Calculate real statistics (total spent, service count, etc.)
- [ ] Add quick action buttons
- [ ] Service recommendations based on mileage/date

#### Week 8: Complaint & Feedback Enhancements
- [ ] File attachments for complaints
- [ ] Photo upload for feedback
- [ ] Low rating (1-2 stars) automatic escalation to manager
- [ ] Complaint SLA tracking with expected resolution date
- [ ] Public testimonials page (approved 4-5 star reviews)

#### Week 9: Analytics & Reports
- [ ] Admin analytics dashboard for feedback trends
- [ ] Complaint resolution time metrics
- [ ] Service performance by branch/technician
- [ ] Customer satisfaction score (CSAT) calculation

---

## 5. Database Schema Changes Required

### New Tables

```sql
-- Customer notifications
CREATE TABLE notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL, -- 'appointment', 'service_update', 'feedback_reply', 'complaint_update', 'payment'
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Complaint comments/activity log
CREATE TABLE complaint_comments (
    comment_id INT PRIMARY KEY AUTO_INCREMENT,
    complaint_id INT NOT NULL,
    user_id INT NOT NULL,
    user_role VARCHAR(50) NOT NULL, -- 'customer', 'receptionist', 'supervisor'
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(complaint_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Payment transactions
CREATE TABLE payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    customer_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50), -- 'stripe', 'paypal', 'cash', 'card'
    transaction_id VARCHAR(255), -- Gateway transaction ID
    status VARCHAR(50) DEFAULT 'pending', -- 'pending', 'completed', 'failed', 'refunded'
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(invoice_id),
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);
```

### Table Modifications

```sql
-- Add customer-facing complaint features
ALTER TABLE complaints 
ADD COLUMN complaint_category VARCHAR(100), -- 'Service Quality', 'Billing', 'Scheduling', 'Staff', 'Other'
ADD COLUMN expected_resolution_date DATE,
ADD COLUMN resolved_at TIMESTAMP NULL,
ADD COLUMN attachment_path VARCHAR(255);

-- Add feedback edit tracking
ALTER TABLE feedback
ADD COLUMN edited_at TIMESTAMP NULL,
ADD COLUMN is_public BOOLEAN DEFAULT TRUE; -- For testimonials page

-- Add appointment enhancements
ALTER TABLE appointments
ADD COLUMN customer_notes TEXT,
ADD COLUMN estimated_cost DECIMAL(10,2);

-- Add payment tracking to invoices (if not already present)
ALTER TABLE invoices
ADD COLUMN payment_status VARCHAR(50) DEFAULT 'unpaid', -- 'unpaid', 'paid', 'partial'
ADD COLUMN payment_method VARCHAR(50),
ADD COLUMN payment_date TIMESTAMP NULL;

-- Add notification preferences to users
ALTER TABLE users
ADD COLUMN email_notifications BOOLEAN DEFAULT TRUE,
ADD COLUMN sms_notifications BOOLEAN DEFAULT FALSE;
```

---

## 6. API Endpoints to Implement (for Mobile App Future)

While not critical for web MVP, consider RESTful structure:

```
POST   /api/customer/complaints                 - Create complaint
GET    /api/customer/complaints                 - List complaints
GET    /api/customer/complaints/{id}            - View complaint
POST   /api/customer/complaints/{id}/comment    - Add comment

GET    /api/customer/feedback                   - List own feedback
POST   /api/customer/feedback                   - Submit feedback
PUT    /api/customer/feedback/{id}              - Edit feedback

GET    /api/customer/invoices                   - List invoices
GET    /api/customer/invoices/{id}              - View invoice
POST   /api/customer/invoices/{id}/pay          - Process payment

GET    /api/customer/notifications              - Get notifications
PUT    /api/customer/notifications/{id}/read    - Mark as read
```

---

## 7. Testing Requirements

### Manual Testing Checklist
- [ ] Customer can file complaint from dashboard
- [ ] Customer sees own complaints with status updates
- [ ] Customer receives email when complaint status changes
- [ ] Customer can submit feedback for completed services
- [ ] Customer sees feedback history with admin replies
- [ ] Customer receives email when admin replies
- [ ] Customer can view all invoices
- [ ] Customer can pay invoice via Stripe
- [ ] Customer receives payment confirmation email
- [ ] Customer sees notifications in bell icon
- [ ] Customer can change password
- [ ] Customer can reschedule appointment
- [ ] All forms validate input correctly
- [ ] Mobile responsive design works on all pages

### Automated Testing (if implementing)
- Unit tests for models (complaint CRUD, feedback validation)
- Integration tests for email sending
- Payment gateway sandbox testing
- Security tests (SQL injection, XSS prevention)

---

## 8. Deployment Considerations

### Configuration
- [ ] Set up payment gateway credentials (Stripe API keys)
- [ ] Configure SMTP settings for email notifications
- [ ] Set up cron job for automated reminders (daily at 9 AM)
- [ ] Configure file upload limits for attachments (max 5MB)

### Security
- [ ] Implement CSRF protection on all forms
- [ ] Validate and sanitize all user inputs
- [ ] Use parameterized queries (prevent SQL injection)
- [ ] Set appropriate file upload restrictions (image types only)
- [ ] Implement rate limiting on API endpoints

### Performance
- [ ] Index database tables (complaints.customer_id, feedback.appointment_id, notifications.user_id)
- [ ] Optimize image uploads (resize before storage)
- [ ] Cache frequently accessed data (service lists, branches)

---

## 9. Success Metrics for MVP

Track these KPIs post-launch:

**Customer Adoption:**
- % of customers who file at least one complaint
- % of completed services that receive feedback
- Average time to submit feedback after service completion

**Customer Satisfaction:**
- Average feedback rating (target: >4.0 stars)
- Complaint resolution time (target: <48 hours)
- % of customers who rate complaint resolution satisfactory

**Operational Efficiency:**
- Reduction in phone calls to receptionists (complaints now self-service)
- Admin reply rate to feedback (target: 100% within 24 hours)
- Payment collection time (online vs. manual)

---

## 10. Estimated Effort Summary

| Feature Category | Priority | Dev Time | Testing | Total |
|---|---|---|---|---|
| Customer Complaint System | P0 | 40 hours | 8 hours | 48 hours |
| Feedback Visibility & Replies | P0 | 20 hours | 4 hours | 24 hours |
| Payment & Invoice System | P0 | 60 hours | 12 hours | 72 hours |
| Notification System | P0 | 40 hours | 8 hours | 48 hours |
| Security & Account Mgmt | P0 | 24 hours | 4 hours | 28 hours |
| Dashboard Enhancements | P1 | 16 hours | 2 hours | 18 hours |
| Complaint/Feedback Enhancements | P1 | 32 hours | 6 hours | 38 hours |
| Analytics & Reports | P1 | 24 hours | 4 hours | 28 hours |
| **TOTAL MVP (P0 only)** | - | **184 hours** | **36 hours** | **220 hours** |
| **TOTAL with P1** | - | **256 hours** | **48 hours** | **304 hours** |

**Team Estimate**: 
- 1 full-stack developer: ~6-8 weeks (P0 only) or 8-10 weeks (P0 + P1)
- 2 developers: ~4-5 weeks (P0 only) or 5-6 weeks (P0 + P1)

---

## 11. Conclusion

The AutoNexus customer portal has a **solid foundation** with core booking, tracking, and profile management features implemented. However, to be considered a **complete MVP**, the following are absolutely essential:

### **Must-Have Before Launch (P0):**
1. ✅ Customer self-service complaint filing and tracking
2. ✅ Customer visibility into feedback history and admin replies
3. ✅ Payment processing and invoice management
4. ✅ Email notification system for key customer events
5. ✅ Password management and basic security features

### **Recommended for Launch (P1):**
6. ⚠️ In-app notification center
7. ⚠️ Appointment rescheduling
8. ⚠️ File attachments for complaints and feedback
9. ⚠️ Service pricing visibility during booking
10. ⚠️ Low rating escalation workflow

The current system is approximately **60-70% complete** for a minimal viable product. The **payment/invoice system is the most critical gap** (currently 0% implemented), followed by **notification infrastructure** (10% complete - email framework exists but not wired).

**Recommendation**: Prioritize Phase 1 implementation (4-6 weeks) to achieve MVP status, then iterate based on user feedback.
