-- Sample Service History Data for jaysandini@gmail.com
-- Run this in phpMyAdmin or MySQL command line

-- Step 1: Get the user_id and customer_id for jaysandini@gmail.com
-- Replace these variables with actual IDs after checking your database
SET @user_id = (SELECT user_id FROM users WHERE email = 'jaysandini@gmail.com' LIMIT 1);
SET @customer_id = (SELECT customer_id FROM customers WHERE user_id = @user_id LIMIT 1);

-- If customer doesn't exist, you may need to register first
-- You can check: SELECT * FROM users WHERE email = 'jaysandini@gmail.com';
-- And: SELECT * FROM customers WHERE user_id = @user_id;

-- Step 2: Create a sample vehicle if it doesn't exist
INSERT INTO vehicles (customer_id, make, model, year, license_plate, vin, created_at)
SELECT @customer_id, 'Toyota', 'Corolla', 2020, 'ABC-1234', 'VIN123456789', NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM vehicles WHERE customer_id = @customer_id AND license_plate = 'ABC-1234'
);

SET @vehicle_id = (SELECT vehicle_id FROM vehicles WHERE customer_id = @customer_id AND license_plate = 'ABC-1234' LIMIT 1);

-- Step 3: Get or create service types
SET @service_oil_change = (SELECT service_id FROM services WHERE name LIKE '%Oil Change%' LIMIT 1);
SET @service_brake = (SELECT service_id FROM services WHERE name LIKE '%Brake%' LIMIT 1);
SET @service_tire = (SELECT service_id FROM services WHERE name LIKE '%Tire%' LIMIT 1);

-- If services don't exist, use default service_id = 1 or create them
-- You can check: SELECT * FROM services LIMIT 5;

-- Step 4: Get a branch
SET @branch_id = (SELECT branch_id FROM branches LIMIT 1);

-- Step 5: Get a mechanic (optional)
SET @mechanic_id = (SELECT mechanic_id FROM mechanics LIMIT 1);

-- Step 6: Create sample appointments
INSERT INTO appointments (customer_id, branch_id, vehicle_id, service_id, appointment_date, appointment_time, status, created_at)
VALUES 
    (@customer_id, @branch_id, @vehicle_id, @service_oil_change, '2026-02-15', '10:00:00', 'completed', NOW()),
    (@customer_id, @branch_id, @vehicle_id, @service_brake, '2026-01-20', '14:00:00', 'completed', NOW()),
    (@customer_id, @branch_id, @vehicle_id, @service_tire, '2025-12-10', '09:30:00', 'completed', NOW());

-- Get the appointment IDs
SET @apt_id_1 = LAST_INSERT_ID();
SET @apt_id_2 = @apt_id_1 + 1;
SET @apt_id_3 = @apt_id_1 + 2;

-- Step 7: Create work orders for these appointments
INSERT INTO work_orders (appointment_id, mechanic_id, service_summary, status, total_cost, created_at, completed_at)
VALUES
    (
        @apt_id_1, 
        @mechanic_id, 
        'Completed full synthetic oil change. Replaced oil filter. Checked all fluid levels and tire pressure. Vehicle is in good condition.',
        'completed',
        4500.00,
        '2026-02-15 10:00:00',
        '2026-02-15 11:30:00'
    ),
    (
        @apt_id_2,
        @mechanic_id,
        'Replaced front brake pads and rotors. Cleaned and lubricated brake calipers. Performed brake system inspection. All brake components working properly.',
        'completed',
        12500.00,
        '2026-01-20 14:00:00',
        '2026-01-20 16:00:00'
    ),
    (
        @apt_id_3,
        @mechanic_id,
        'Rotated all four tires. Balanced wheels. Checked tire tread depth and pressure. Aligned front wheels. Tires in good condition.',
        'completed',
        3500.00,
        '2025-12-10 09:30:00',
        '2025-12-10 11:00:00'
    );

-- Verify the data
SELECT 'User and Customer Info:' as Info;
SELECT u.user_id, u.email, u.first_name, u.last_name, c.customer_id
FROM users u
LEFT JOIN customers c ON c.user_id = u.user_id
WHERE u.email = 'jaysandini@gmail.com';

SELECT 'Appointments Created:' as Info;
SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status, s.name as service
FROM appointments a
LEFT JOIN services s ON s.service_id = a.service_id
WHERE a.customer_id = @customer_id
ORDER BY a.appointment_date DESC;

SELECT 'Work Orders Created:' as Info;
SELECT w.work_order_id, w.status, w.total_cost, w.created_at, w.completed_at
FROM work_orders w
JOIN appointments a ON a.appointment_id = w.appointment_id
WHERE a.customer_id = @customer_id
ORDER BY w.work_order_id DESC;
