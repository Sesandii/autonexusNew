# File a Complaint Feature - Implementation Guide

This document provides complete beginner-friendly instructions for the "File a Complaint" feature that has been added to the AutoNexus customer portal.

## Overview

The File a Complaint feature allows customers to submit complaints about completed service appointments. This feature includes:
- A form with an appointments dropdown
- Auto-populated appointment details
- A text area for describing the complaint
- Integration with existing customer navigation

---

## Files Created

### 1. **Controller: `app/controllers/customer/ComplaintController.php`**

**Purpose:** Handles the logic for displaying and processing complaints.

**Methods:**
- `file()` - Displays the complaint form and loads completed appointments
- `submit()` - Processes the complaint submission (currently stubbed for future implementation)

**Key Features:**
- Session-based authentication (checks if user is logged in as customer)
- Fetches completed appointments from database
- Passes data to view for rendering
- Includes extensive comments for beginners

---

### 2. **View: `app/views/customer/file-complaint.php`**

**Purpose:** The HTML template that displays the complaint form to customers.

**Features:**
- Appointments dropdown (populated from controller data)
- Auto-filled readonly fields (vehicle, service, date, branch)
- Textarea for complaint description
- JavaScript that auto-fills details when appointment is selected
- Follows the same design pattern as other customer pages
- Extensive inline comments explaining each section

**Form Flow:**
1. Customer selects an appointment from dropdown
2. JavaScript automatically fills in appointment details
3. Customer writes complaint description
4. Form submits via POST to `/customer/complaints/submit`

---

### 3. **Stylesheet: `public/css/complaint.css`**

**Purpose:** Provides styling for the complaint form page.

**Features:**
- Matches the design of other customer pages (consistent look and feel)
- Uses CSS Grid for responsive layout
- Includes CSS variables (design tokens) for easy theming
- Fully commented for learning purposes
- Responsive design (works on mobile and desktop)

**Key Styles:**
- Form layout with grid system
- Input field styling (text, select, textarea)
- Button hover effects
- Readonly field styling (visual distinction)
- Flash message styling (for success/error messages)

---

### 4. **Navigation Update: `app/views/layouts/customer-sidebar.php`**

**What Changed:** Added a new menu item for "File a Complaint"

**Location:** Between "Reviews" and "Profile" menu items

**Code Added:**
```php
<!-- File a Complaint (Added for customer complaint feature) -->
<li><a<?= isActive('/customer/file-complaint', $currentPath) ?> href="<?= $base ?>/customer/file-complaint"><i class="fa-solid fa-comment-dots"></i> File a Complaint</a></li>
```

**Features:**
- Uses Font Awesome icon (`fa-comment-dots`)
- Active state highlighting when on complaint page
- Consistent with other menu items

---

## Routing Configuration

### Where Routes Are Defined

**File:** `public/index.php` (around line 347-352)

### Routes Added

```php
// Customer Complaints - File a Complaint feature
use app\controllers\customer\ComplaintController as CustomerComplaintController;

$router->get('/customer/file-complaint', [CustomerComplaintController::class, 'file']);
$router->post('/customer/complaints/submit', [CustomerComplaintController::class, 'submit']);
```

### Route Details

1. **GET `/customer/file-complaint`**
   - Displays the complaint form
   - Calls `ComplaintController::file()` method
   - Requires customer authentication

2. **POST `/customer/complaints/submit`**
   - Processes complaint submission
   - Calls `ComplaintController::submit()` method
   - Currently returns a placeholder message

### Important Note About Namespace Alias

We use `as CustomerComplaintController` because there's already a `ComplaintController` imported from the `Receptionist` namespace. This alias prevents naming conflicts.

---

## How It Works (Step-by-Step)

### For End Users (Customers):

1. **Login:** Customer logs into their account
2. **Navigate:** Click "File a Complaint" in the sidebar
3. **Select Appointment:** Choose a completed appointment from dropdown
4. **View Details:** Appointment details auto-fill (vehicle, service, date)
5. **Write Complaint:** Type complaint description in the text area
6. **Submit:** Click "Submit Complaint" button
7. **Confirmation:** See success message (after submit method is fully implemented)

### For Developers:

1. **Route Matching:** When user visits `/customer/file-complaint`, the router matches it to `ComplaintController::file()`

2. **Authentication:** Controller checks if user is logged in as customer using `requireCustomer()`

3. **Data Fetching:** Controller fetches completed appointments:
   ```php
   $appointmentModel = new Appointments();
   $appointments = $appointmentModel->completedByUser($uid);
   ```

4. **View Rendering:** Controller passes data to view:
   ```php
   $this->view('customer/file-complaint', [
       'title' => 'File a Complaint',
       'appointments' => $appointments,
   ]);
   ```

5. **Form Display:** View renders HTML form with appointments dropdown

6. **JavaScript Interaction:** When user selects appointment, JavaScript:
   - Gets data attributes from selected option
   - Fills readonly input fields
   - Formats the date nicely

7. **Form Submission:** When submitted, form posts to `/customer/complaints/submit`

8. **Processing:** `ComplaintController::submit()` processes the complaint (to be implemented)

---

## Future Implementation Tasks

The `submit()` method in `ComplaintController.php` is currently a stub. Here's what needs to be implemented:

### Step 1: Validate Input
```php
// Check that appointmentId is valid (> 0)
if ($appointmentId <= 0) {
    $_SESSION['flash'] = 'Please select a valid appointment.';
    header('Location: ' . rtrim(BASE_URL, '/') . '/customer/file-complaint');
    exit;
}

// Check that description is not empty
if (empty($description)) {
    $_SESSION['flash'] = 'Please provide a complaint description.';
    header('Location: ' . rtrim(BASE_URL, '/') . '/customer/file-complaint');
    exit;
}
```

### Step 2: Verify Ownership
```php
// Create Appointments model instance
$appointmentModel = new Appointments();

// Verify appointment belongs to this customer
if (!$appointmentModel->appointmentBelongsToUserAndCompleted($uid, $appointmentId)) {
    $_SESSION['flash'] = 'You can only file complaints for your own completed appointments.';
    header('Location: ' . rtrim(BASE_URL, '/') . '/customer/file-complaint');
    exit;
}
```

### Step 3: Get Additional Data
```php
// Get customer_id and vehicle_id from appointment
$pdo = db();
$stmt = $pdo->prepare("
    SELECT customer_id, vehicle_id 
    FROM appointments 
    WHERE appointment_id = :aid
");
$stmt->execute(['aid' => $appointmentId]);
$appointmentData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointmentData) {
    $_SESSION['flash'] = 'Appointment not found.';
    header('Location: ' . rtrim(BASE_URL, '/') . '/customer/file-complaint');
    exit;
}

$customerId = $appointmentData['customer_id'];
$vehicleId = $appointmentData['vehicle_id'];
```

### Step 4: Insert Complaint
```php
// Create ComplaintModel instance
$complaintModel = new \app\model\Receptionist\ComplaintModel();

// Prepare complaint data
$complaintData = [
    'customer_id' => $customerId,
    'vehicle_id' => $vehicleId,
    'complaint_date' => date('Y-m-d'),
    'complaint_time' => date('H:i:s'),
    'description' => $description,
    'priority' => 'Medium', // Default priority
    'status' => 'Open',     // Initial status
    'assigned_to' => null   // Not assigned yet
];

// Insert complaint
try {
    $complaintId = $complaintModel->create($complaintData);
    $_SESSION['flash'] = 'Your complaint has been submitted successfully! Complaint ID: ' . $complaintId;
} catch (\Exception $e) {
    $_SESSION['flash'] = 'Error submitting complaint: ' . $e->getMessage();
}
```

### Step 5: Redirect
```php
// Redirect back to complaint form
header('Location: ' . rtrim(BASE_URL, '/') . '/customer/file-complaint');
exit;
```

---

## Database Schema

The feature uses the existing `complaints` table which has the following structure:

```sql
CREATE TABLE complaints (
    complaint_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    complaint_date DATE,
    complaint_time TIME,
    description TEXT,
    priority ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    status ENUM('Open', 'In Progress', 'Resolved', 'Closed') DEFAULT 'Open',
    assigned_to INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(vehicle_id),
    FOREIGN KEY (assigned_to) REFERENCES users(user_id)
);
```

**Note:** The existing `ComplaintModel` in `app/model/Receptionist/ComplaintModel.php` can be reused for database operations.

---

## Testing the Feature

### Manual Testing Steps:

1. **Start the PHP server:**
   ```bash
   cd /home/runner/work/autonexusNew/autonexusNew
   php -S localhost:8080 -t public
   ```

2. **Login as a customer**
   - Navigate to `http://localhost:8080/login`
   - Use customer credentials

3. **Access complaint page:**
   - Click "File a Complaint" in sidebar
   - OR navigate to `http://localhost:8080/customer/file-complaint`

4. **Test the form:**
   - Verify appointments dropdown is populated
   - Select an appointment
   - Verify auto-fill of appointment details
   - Enter a complaint description
   - Click "Submit Complaint"
   - Check for appropriate response (currently shows placeholder)

### Expected Behavior:

- ✅ Page loads without errors
- ✅ Sidebar shows "File a Complaint" link
- ✅ Appointments dropdown is populated with completed appointments
- ✅ Selecting an appointment auto-fills vehicle, service, date, branch
- ✅ Form styling matches other customer pages
- ✅ Form submits to `/customer/complaints/submit`
- ⏳ Success message displayed (after submit method is implemented)

---

## Troubleshooting

### Common Issues:

**Issue:** "ComplaintController already in use" error
- **Cause:** Namespace conflict with Receptionist's ComplaintController
- **Solution:** Use namespace alias: `as CustomerComplaintController`

**Issue:** No appointments in dropdown
- **Cause:** Customer has no completed appointments
- **Solution:** Create test appointments with 'completed' status in database

**Issue:** Page redirects to login
- **Cause:** Not logged in or not logged in as customer
- **Solution:** Ensure you're logged in with customer credentials

**Issue:** CSS not loading (unstyled page)
- **Cause:** CSS file path incorrect
- **Solution:** Verify `public/css/complaint.css` exists and path is correct in view

**Issue:** Auto-fill not working
- **Cause:** JavaScript error or missing data attributes
- **Solution:** Check browser console for errors; verify data attributes in PHP

---

## File Paths Summary

For easy reference, here are all the file paths:

```
autonexusNew/
├── app/
│   ├── controllers/
│   │   └── customer/
│   │       └── ComplaintController.php          ← Controller (NEW)
│   ├── views/
│   │   ├── customer/
│   │   │   └── file-complaint.php               ← View (NEW)
│   │   └── layouts/
│   │       └── customer-sidebar.php             ← Modified (added menu item)
│   └── model/
│       └── Receptionist/
│           └── ComplaintModel.php               ← Existing (reused)
├── public/
│   ├── css/
│   │   └── complaint.css                        ← CSS (NEW)
│   └── index.php                                ← Modified (added routes)
└── COMPLAINT_FEATURE.md                         ← This documentation (NEW)
```

---

## Code Comments

All files include extensive inline comments suitable for beginners:

- **Controller comments** explain MVC concepts, sessions, and method purposes
- **View comments** explain HTML structure, PHP templating, and JavaScript
- **CSS comments** explain every style rule, design tokens, and layout techniques

These comments serve as learning material for developers new to MVC architecture, PHP, or web development in general.

---

## Next Steps

1. ✅ Files created and routes configured
2. ✅ Navigation menu updated
3. ⏳ Implement `submit()` method (follow instructions in "Future Implementation Tasks")
4. ⏳ Add complaint listing page (show customer's past complaints)
5. ⏳ Add complaint tracking (show complaint status/resolution)
6. ⏳ Email notifications when complaint status changes

---

## Support

For questions or issues:
- Review the inline code comments in each file
- Check the troubleshooting section above
- Verify routes are correctly defined in `public/index.php`
- Ensure database has `complaints` table with correct schema

---

**Feature Status:** ✅ READY FOR USE (form display functional; submission needs implementation)

**Last Updated:** February 4, 2026
