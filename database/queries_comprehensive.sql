-- =====================================================
-- BLOOD BANK MANAGEMENT SYSTEM
-- COMPREHENSIVE SQL QUERIES (70+ queries for academic submission)
-- =====================================================
-- These queries demonstrate:
-- - Simple SELECT, JOIN, WHERE filtering
-- - UPDATE and DELETE operations
-- - GROUP BY, HAVING, ORDER BY
-- - Aggregate functions (SUM, COUNT, AVG, MAX, MIN)
-- - Subqueries and nested queries
-- - Complex multi-table JOINs
-- =====================================================

USE blood_bank_system;

-- =====================================================
-- SECTION 1: BASIC SELECT QUERIES (10 queries)
-- =====================================================

-- Q1: Retrieve all active donors with their blood types
SELECT 
    d.id,
    d.full_name,
    d.email,
    d.phone,
    bt.blood_type,
    d.city,
    d.donation_count,
    d.last_donation_date
FROM donors d
JOIN blood_types bt ON d.blood_type_id = bt.id
WHERE d.status = 'active'
ORDER BY d.full_name;

-- Q2: List all blood types with plasma description
SELECT 
    id,
    blood_type,
    description,
    rh_factor
FROM blood_types
ORDER BY blood_type;

-- Q3: Get all active admin users with their departments
SELECT 
    id,
    username,
    full_name,
    email,
    phone,
    role,
    department
FROM admin_users
WHERE status = 'active'
ORDER BY full_name;

-- Q4: Retrieve all active patients in hospitals
SELECT 
    p.id,
    p.patient_name,
    p.phone,
    bt.blood_type,
    p.hospital_name,
    p.ward_number,
    p.medical_condition,
    p.doctor_name
FROM patients p
JOIN blood_types bt ON p.blood_type_id = bt.id
WHERE p.status = 'active'
ORDER BY p.hospital_name, p.patient_name;

-- Q5: Display blood stock inventory with blood types
SELECT 
    bs.id,
    bt.blood_type,
    bt.description,
    bs.quantity_units,
    bs.quantity_ml,
    bs.minimum_threshold,
    bs.maximum_capacity,
    bs.last_updated,
    CASE 
        WHEN bs.quantity_units <= bs.minimum_threshold THEN 'CRITICAL'
        WHEN bs.quantity_units <= (bs.minimum_threshold * 2) THEN 'LOW'
        ELSE 'IN STOCK'
    END AS stock_status
FROM blood_stock bs
JOIN blood_types bt ON bs.blood_type_id = bt.id
ORDER BY stock_status DESC, bt.blood_type;

-- Q6: List all approved donations
SELECT 
    d.id,
    d.donation_date,
    d.quantity_ml,
    d.expiry_date,
    don.full_name as donor_name,
    bt.blood_type,
    d.hemoglobin_level,
    d.blood_pressure,
    d.status
FROM donations d
JOIN donors don ON d.donor_id = don.id
JOIN blood_types bt ON d.blood_type_id = bt.id
WHERE d.status = 'approved'
ORDER BY d.donation_date DESC;

-- Q7: Show all pending blood requests
SELECT 
    br.id,
    br.request_date,
    p.patient_name,
    p.hospital_name,
    bt.blood_type,
    br.units_required,
    br.urgency_level,
    p.doctor_name,
    p.doctor_phone
FROM blood_requests br
JOIN patients p ON br.patient_id = p.id
JOIN blood_types bt ON br.blood_type_id = bt.id
WHERE br.status = 'pending'
ORDER BY br.urgency_level DESC, br.request_date;

-- Q8: Get all receipts with transaction details
SELECT 
    receipt_number,
    transaction_type,
    blood_type,
    quantity_ml,
    receipt_date,
    CASE 
        WHEN transaction_type = 'donation' THEN 'Blood Donated'
        WHEN transaction_type = 'request' THEN 'Blood Distributed'
        WHEN transaction_type = 'transfer' THEN 'Blood Transferred'
        WHEN transaction_type = 'discard' THEN 'Blood Discarded'
    END AS transaction_description
FROM receipts
ORDER BY receipt_date DESC;

-- Q9: List donor eligibility status
SELECT 
    id,
    full_name,
    phone,
    eligibility_status,
    last_donation_date,
    CASE 
        WHEN eligibility_status = 'eligible' THEN 'Can donate'
        WHEN eligibility_status = 'ineligible' THEN 'Cannot donate'
        WHEN eligibility_status = 'deferred' THEN 'Defer for now'
        WHEN eligibility_status = 'suspended' THEN 'Suspended'
    END AS eligibility_description
FROM donors
WHERE status = 'active'
ORDER BY eligibility_status;

-- Q10: Show all donation records with detailed information
SELECT 
    d.id,
    d.donation_date,
    don.full_name,
    don.phone,
    don.city,
    bt.blood_type,
    d.quantity_ml,
    d.expiry_date,
    d.test_result,
    d.status
FROM donations d
JOIN donors don ON d.donor_id = don.id
JOIN blood_types bt ON d.blood_type_id = bt.id
ORDER BY d.donation_date DESC;

-- =====================================================
-- SECTION 2: JOIN QUERIES (10 queries - Multiple Tables)
-- =====================================================

-- Q11: Donor with their donations and blood types
SELECT 
    d.full_name as donor_name,
    d.phone,
    d.city,
    COUNT(don.id) as total_donations,
    bt.blood_type,
    d.donation_count,
    d.last_donation_date
FROM donors d
LEFT JOIN donations don ON d.id = don.donor_id
JOIN blood_types bt ON d.blood_type_id = bt.id
GROUP BY d.id, d.full_name, d.phone, d.city, bt.blood_type, d.donation_count, d.last_donation_date
ORDER BY d.full_name;

-- Q12: Patient with requested blood types and approval status
SELECT 
    p.patient_name,
    p.hospital_name,
    p.ward_number,
    bt.blood_type,
    br.units_required,
    br.units_fulfilled,
    br.status,
    br.request_date,
    br.approval_date,
    au.full_name as approved_by
FROM blood_requests br
JOIN patients p ON br.patient_id = p.id
JOIN blood_types bt ON br.blood_type_id = bt.id
LEFT JOIN admin_users au ON br.approved_by = au.id
ORDER BY br.request_date DESC;

-- Q13: Donation tracking with collector information
SELECT 
    d.id,
    d.donation_date,
    don.full_name as donor_name,
    don.phone,
    au.full_name as collected_by,
    bt.blood_type,
    d.quantity_ml,
    d.hemoglobin_level,
    d.status
FROM donations d
JOIN donors don ON d.donor_id = don.id
JOIN admin_users au ON d.collected_by = au.id
JOIN blood_types bt ON d.blood_type_id = bt.id
WHERE d.status IN ('approved', 'discarded')
ORDER BY d.donation_date DESC;

-- Q14: Detailed receipt information with donor/patient names
SELECT 
    r.receipt_number,
    r.receipt_date,
    CASE 
        WHEN r.transaction_type = 'donation' THEN d.full_name
        WHEN r.transaction_type = 'request' THEN p.patient_name
        ELSE 'N/A'
    END AS person_name,
    CASE 
        WHEN r.transaction_type = 'donation' THEN d.phone
        WHEN r.transaction_type = 'request' THEN p.phone
        ELSE 'N/A'
    END AS phone,
    r.blood_type,
    r.quantity_ml,
    r.transaction_type,
    au.full_name as issued_by
FROM receipts r
LEFT JOIN donors d ON r.donor_id = d.id
LEFT JOIN patients p ON r.patient_id = p.id
LEFT JOIN admin_users au ON r.issued_by = au.id
ORDER BY r.receipt_date DESC;

-- Q15: Blood request with patient and hospital information
SELECT 
    br.id,
    p.patient_name,
    p.hospital_name,
    p.doctor_name,
    p.doctor_phone,
    bt.blood_type,
    br.units_required,
    br.units_fulfilled,
    br.urgency_level,
    br.status,
    br.rejection_reason
FROM blood_requests br
JOIN patients p ON br.patient_id = p.id
JOIN blood_types bt ON br.blood_type_id = bt.id
WHERE br.status IN ('rejected', 'cancelled')
ORDER BY br.request_date DESC;

-- Q16: Donor eligibility with donation history
SELECT 
    d.id,
    d.full_name,
    d.city,
    d.weight_kg,
    d.hemoglobin_g_dl,
    d.eligibility_status,
    COUNT(don.id) as total_donations_made,
    SUM(CASE WHEN don.status = 'approved' THEN 1 ELSE 0 END) as approved_donations,
    d.last_donation_date
FROM donors d
LEFT JOIN donations don ON d.id = don.donor_id
WHERE d.status = 'active'
GROUP BY d.id
ORDER BY d.hemoglobin_g_dl DESC;

-- Q17: Blood stock with donation and fulfillment tracking
SELECT 
    bs.id,
    bt.blood_type,
    bs.quantity_units,
    bs.quantity_ml,
    COUNT(DISTINCT CASE WHEN d.status = 'approved' THEN d.id END) as donations_in_stock,
    COUNT(DISTINCT CASE WHEN br.status = 'fulfilled' THEN br.id END) as fulfilled_requests,
    CASE 
        WHEN bs.quantity_units = 0 THEN 'OUT OF STOCK'
        WHEN bs.quantity_units < 5 THEN 'CRITICAL'
        WHEN bs.quantity_units < 10 THEN 'LOW'
        ELSE 'ADEQUATE'
    END as status
FROM blood_stock bs
JOIN blood_types bt ON bs.blood_type_id = bt.id
LEFT JOIN donations d ON bs.blood_type_id = d.blood_type_id AND d.status = 'approved'
LEFT JOIN blood_requests br ON bs.blood_type_id = br.blood_type_id AND br.status = 'fulfilled'
GROUP BY bs.id, bt.blood_type, bs.quantity_units, bs.quantity_ml
ORDER BY bs.quantity_units ASC;

-- Q18: Active donors by city with blood type distribution
SELECT 
    d.city,
    bt.blood_type,
    COUNT(d.id) as donor_count,
    SUM(d.donation_count) as total_donations_from_city,
    ROUND(AVG(d.hemoglobin_g_dl), 2) as avg_hemoglobin
FROM donors d
JOIN blood_types bt ON d.blood_type_id = bt.id
WHERE d.status = 'active'
GROUP BY d.city, bt.blood_type
ORDER BY d.city, bt.blood_type;

-- Q19: Request approval workflow
SELECT 
    br.id,
    br.request_date,
    br.approval_date,
    br.fulfilled_date,
    DATEDIFF(br.approval_date, br.request_date) as approval_days,
    DATEDIFF(br.fulfilled_date, br.approval_date) as fulfillment_days,
    p.patient_name,
    bt.blood_type,
    br.units_required,
    au.full_name as approved_by
FROM blood_requests br
JOIN patients p ON br.patient_id = p.id
JOIN blood_types bt ON br.blood_type_id = bt.id
LEFT JOIN admin_users au ON br.approved_by = au.id
WHERE br.status = 'fulfilled'
ORDER BY br.fulfilled_date DESC;

-- Q20: Staff performance in donations and requests
SELECT 
    au.id,
    au.full_name,
    au.department,
    COUNT(DISTINCT d.id) as donations_collected,
    COUNT(DISTINCT br.id) as requests_approved,
    SUM(CASE WHEN d.status = 'approved' THEN d.quantity_ml ELSE 0 END) as total_ml_collected
FROM admin_users au
LEFT JOIN donations d ON au.id = d.collected_by
LEFT JOIN blood_requests br ON au.id = br.approved_by
WHERE au.status = 'active'
GROUP BY au.id, au.full_name, au.department
ORDER BY au.full_name;

-- =====================================================
-- SECTION 3: WHERE FILTERING QUERIES (10 queries)
-- =====================================================

-- Q21: Donors who have donated more than 3 times
SELECT 
    id,
    full_name,
    phone,
    city,
    donation_count,
    last_donation_date
FROM donors
WHERE donation_count > 3 AND status = 'active'
ORDER BY donation_count DESC;

-- Q22: Blood requests for emergency cases
SELECT 
    br.id,
    br.request_date,
    p.patient_name,
    p.hospital_name,
    bt.blood_type,
    br.units_required,
    br.status
FROM blood_requests br
JOIN patients p ON br.patient_id = p.id
JOIN blood_types bt ON br.blood_type_id = bt.id
WHERE br.urgency_level = 'emergency' AND br.status != 'fulfilled'
ORDER BY br.request_date ASC;

-- Q23: Donations that will expire within 7 days
SELECT 
    d.id,
    d.donation_date,
    d.expiry_date,
    DATEDIFF(d.expiry_date, CURDATE()) as days_until_expiry,
    don.full_name,
    bt.blood_type,
    d.quantity_ml
FROM donations d
JOIN donors don ON d.donor_id = don.id
JOIN blood_types bt ON d.blood_type_id = bt.id
WHERE d.status = 'approved' 
    AND d.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    AND d.expiry_date > CURDATE()
ORDER BY d.expiry_date ASC;

-- Q24: Patients admitted in the last 30 days
SELECT 
    p.id,
    p.patient_name,
    p.hospital_name,
    p.admitted_date,
    DATEDIFF(CURDATE(), p.admitted_date) as days_admitted,
    bt.blood_type,
    p.medical_condition,
    p.doctor_name
FROM patients p
JOIN blood_types bt ON p.blood_type_id = bt.id
WHERE p.admitted_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    AND p.status = 'active'
ORDER BY p.admitted_date DESC;

-- Q25: Blood stock below minimum threshold
SELECT 
    bt.blood_type,
    bs.quantity_units,
    bs.quantity_ml,
    bs.minimum_threshold,
    bs.maximum_capacity,
    (bs.minimum_threshold - bs.quantity_units) as units_needed_to_reach_min
FROM blood_stock bs
JOIN blood_types bt ON bs.blood_type_id = bt.id
WHERE bs.quantity_units < bs.minimum_threshold
ORDER BY quantity_units ASC;

-- Q26: Donors in specific cities (Dhaka, Chittagong, Sylhet)
SELECT 
    full_name,
    phone,
    city,
    donation_count,
    eligibility_status
FROM donors
WHERE city IN ('ঢাকা', 'চট্টগ্রাম', 'সিলেট') AND status = 'active'
ORDER BY city, full_name;

-- Q27: Donations from specific date range (February 2024)
SELECT 
    d.donation_date,
    don.full_name,
    don.phone,
    bt.blood_type,
    d.quantity_ml,
    d.status
FROM donations d
JOIN donors don ON d.donor_id = don.id
JOIN blood_types bt ON d.blood_type_id = bt.id
WHERE d.donation_date >= '2024-02-01' AND d.donation_date <= '2024-02-29'
ORDER BY d.donation_date DESC;

-- Q28: Rejected or discarded donations
SELECT 
    d.id,
    d.donation_date,
    don.full_name,
    bt.blood_type,
    d.status,
    d.test_result,
    d.notes,
    DATEDIFF(CURDATE(), d.donation_date) as days_ago
FROM donations d
JOIN donors don ON d.donor_id = don.id
JOIN blood_types bt ON d.blood_type_id = bt.id
WHERE d.status IN ('rejected', 'discarded')
ORDER BY d.donation_date DESC;

-- Q29: Fulfilled blood requests with high urgency
SELECT 
    br.id,
    br.request_date,
    br.fulfilled_date,
    p.patient_name,
    p.hospital_name,
    bt.blood_type,
    br.units_required,
    br.units_fulfilled
FROM blood_requests br
JOIN patients p ON br.patient_id = p.id
JOIN blood_types bt ON br.blood_type_id = bt.id
WHERE br.urgency_level IN ('emergency', 'urgent') 
    AND br.status = 'fulfilled'
    AND br.fulfilled_date IS NOT NULL
ORDER BY br.fulfilled_date DESC;

-- Q30: Staff members with specific roles
SELECT 
    id,
    username,
    full_name,
    email,
    phone,
    role,
    department
FROM admin_users
WHERE role IN ('admin', 'supervisor', 'manager') AND status = 'active'
ORDER BY role, full_name;

-- =====================================================
-- SECTION 4: UPDATE QUERIES (10 queries)
-- =====================================================

-- Q31: Update donor eligibility status (simulation - DO NOT EXECUTE ON PRODUCTION)
-- UPDATE donors 
-- SET eligibility_status = 'eligible'
-- WHERE id = 5 AND status = 'active';

-- Q32: Update donation status to tested
-- UPDATE donations 
-- SET test_result = 'negative', status = 'tested'
-- WHERE id = 1 AND status = 'collected';

-- Q33: Update blood stock quantity
-- UPDATE blood_stock
-- SET quantity_units = 20, quantity_ml = 9000
-- WHERE blood_type_id = 1;

-- Q34: Batch update donor eligibility based on hemoglobin level
-- UPDATE donors
-- SET eligibility_status = 'ineligible'
-- WHERE hemoglobin_g_dl < 12.5 AND status = 'active';

-- Q35: Mark fulfilled blood request with approval
-- UPDATE blood_requests
-- SET status = 'fulfilled', units_fulfilled = units_required, fulfilled_date = NOW()
-- WHERE id = 1 AND status = 'approved';

-- Q36: Update patient status to discharged
-- UPDATE patients
-- SET status = 'discharged'
-- WHERE id = 1 AND status = 'active';

-- Q37: Update last donation date for donors
-- UPDATE donors
-- SET last_donation_date = CURDATE()
-- WHERE id = 3;

-- Q38: Batch update pending requests to approved
-- UPDATE blood_requests
-- SET status = 'approved', approved_by = 1, approval_date = NOW()
-- WHERE status = 'pending' AND urgency_level = 'emergency';

-- Q39: Update admin user password and email
-- UPDATE admin_users
-- SET email = 'newemail@bloodbank.com'
-- WHERE id = 1;

-- Q40: Batch update donation status to approved
-- UPDATE donations
-- SET status = 'approved'
-- WHERE status = 'tested' AND test_result = 'negative';

-- =====================================================
-- SECTION 5: DELETE QUERIES (5 queries - WITH CAUTION)
-- =====================================================

-- Q41: Delete inactive donor records (older than 2 years)
-- DELETE FROM donors
-- WHERE status = 'inactive' AND updated_at < DATE_SUB(CURDATE(), INTERVAL 2 YEAR);

-- Q42: Delete expired donations
-- DELETE FROM donations
-- WHERE status = 'discarded' AND expiry_date < CURDATE();

-- Q43: Delete cancelled blood requests
-- DELETE FROM blood_requests
-- WHERE status = 'cancelled' AND request_date < DATE_SUB(CURDATE(), INTERVAL 6 MONTH);

-- Q44: Remove audit logs older than 1 year
-- DELETE FROM audit_logs
-- WHERE timestamp < DATE_SUB(CURDATE(), INTERVAL 1 YEAR);

-- Q45: Delete test donations (marked for deletion)
-- DELETE FROM donations
-- WHERE id IN (SELECT id FROM donations WHERE notes LIKE '%test%' AND status = 'discarded');

-- =====================================================
-- SECTION 6: GROUP BY QUERIES (10 queries)
-- =====================================================

-- Q46: Count donors by blood type
SELECT 
    bt.blood_type,
    COUNT(d.id) as total_donors,
    SUM(d.donation_count) as total_donations,
    ROUND(AVG(d.hemoglobin_g_dl), 2) as avg_hemoglobin
FROM donors d
JOIN blood_types bt ON d.blood_type_id = bt.id
WHERE d.status = 'active'
GROUP BY d.blood_type_id, bt.blood_type
ORDER BY COUNT(d.id) DESC;

-- Q47: Blood type distribution across cities
SELECT 
    d.city,
    bt.blood_type,
    COUNT(d.id) as donors_count
FROM donors d
JOIN blood_types bt ON d.blood_type_id = bt.id
WHERE d.status = 'active'
GROUP BY d.city, d.blood_type_id, bt.blood_type
ORDER BY d.city, bt.blood_type;

-- Q48: Donation count by month
SELECT 
    DATE_FORMAT(d.donation_date, '%Y-%m') as month,
    COUNT(d.id) as total_donations,
    SUM(d.quantity_ml) as total_ml_collected,
    COUNT(DISTINCT d.donor_id) as unique_donors
FROM donations d
WHERE d.status = 'approved'
GROUP BY DATE_FORMAT(d.donation_date, '%Y-%m')
ORDER BY month DESC;

-- Q49: Request status summary
SELECT 
    status,
    COUNT(*) as total_requests,
    SUM(units_required) as total_units_requested,
    SUM(units_fulfilled) as total_units_fulfilled
FROM blood_requests
GROUP BY status
ORDER BY COUNT(*) DESC;

-- Q50: Hospital-wise patient and request statistics
SELECT 
    p.hospital_name,
    COUNT(DISTINCT p.id) as total_patients,
    COUNT(DISTINCT br.id) as total_requests,
    SUM(br.units_required) as total_units_required,
    COUNT(DISTINCT CASE WHEN br.status = 'fulfilled' THEN br.id END) as fulfilled_requests
FROM patients p
LEFT JOIN blood_requests br ON p.id = br.patient_id
WHERE p.status = 'active'
GROUP BY p.hospital_name
ORDER BY COUNT(DISTINCT br.id) DESC;

-- Q51: Staff departmentwise statistics
SELECT 
    au.department,
    COUNT(DISTINCT au.id) as staff_count,
    COUNT(DISTINCT d.id) as donations_collected,
    SUM(d.quantity_ml) as total_ml_collected,
    COUNT(DISTINCT br.id) as requests_approved
FROM admin_users au
LEFT JOIN donations d ON au.id = d.collected_by
LEFT JOIN blood_requests br ON au.id = br.approved_by
WHERE au.status = 'active'
GROUP BY au.department
ORDER BY department;

-- Q52: Receipt transaction summary by type
SELECT 
    transaction_type,
    COUNT(*) as total_transactions,
    SUM(quantity_ml) as total_ml_transacted,
    AVG(quantity_ml) as avg_quantity
FROM receipts
GROUP BY transaction_type
ORDER BY COUNT(*) DESC;

-- Q53: Donor donation frequency analysis
SELECT 
    donation_count as donation_frequency,
    COUNT(id) as number_of_donors,
    ROUND(AVG(hemoglobin_g_dl), 2) as avg_hemoglobin,
    ROUND(AVG(weight_kg), 2) as avg_weight
FROM donors
WHERE status = 'active'
GROUP BY donation_count
ORDER BY donation_count DESC;

-- Q54: Blood type wise request vs stock analysis
SELECT 
    bt.blood_type,
    bs.quantity_units as current_stock,
    COUNT(CASE WHEN br.status IN ('pending', 'approved') THEN 1 END) as pending_requests,
    COUNT(CASE WHEN br.status = 'fulfilled' THEN 1 END) as fulfilled_requests
FROM blood_types bt
LEFT JOIN blood_stock bs ON bt.id = bs.blood_type_id
LEFT JOIN blood_requests br ON bt.id = br.blood_type_id
GROUP BY bt.id, bt.blood_type, bs.quantity_units
ORDER BY bt.blood_type;

-- Q55: Donor eligibility status distribution
SELECT 
    eligibility_status,
    COUNT(*) as total_donors,
    SUM(donation_count) as total_donations_from_group,
    ROUND(AVG(hemoglobin_g_dl), 2) as avg_hemoglobin
FROM donors
WHERE status = 'active'
GROUP BY eligibility_status
ORDER BY COUNT(*) DESC;

-- =====================================================
-- SECTION 7: HAVING QUERIES (5 queries)
-- =====================================================

-- Q56: Donors with more than 5 successful donations
SELECT 
    d.full_name,
    d.city,
    d.phone,
    COUNT(d.id) as donation_count,
    SUM(CASE WHEN don.status = 'approved' THEN 1 ELSE 0 END) as approved_donations
FROM donors d
LEFT JOIN donations don ON d.id = don.donor_id
WHERE d.status = 'active'
GROUP BY d.id, d.full_name, d.city, d.phone
HAVING COUNT(don.id) >= 5
ORDER BY approved_donations DESC;

-- Q57: Hospitals with more than 5 pending blood requests
SELECT 
    p.hospital_name,
    COUNT(br.id) as pending_requests,
    SUM(br.units_required) as total_units_needed
FROM blood_requests br
JOIN patients p ON br.patient_id = p.id
WHERE br.status = 'pending'
GROUP BY p.hospital_name
HAVING COUNT(br.id) > 5
ORDER BY pending_requests DESC;

-- Q58: Blood types with stock below average
SELECT 
    bt.blood_type,
    bs.quantity_units,
    ROUND(AVG(bs.quantity_units) OVER (), 2) as avg_stock
FROM blood_stock bs
JOIN blood_types bt ON bs.blood_type_id = bt.id
WHERE bs.quantity_units < (SELECT AVG(quantity_units) FROM blood_stock)
ORDER BY bs.quantity_units ASC;

-- Q59: Cities with significant donor populations
SELECT 
    d.city,
    COUNT(d.id) as donor_count,
    SUM(d.donation_count) as total_donations
FROM donors d
WHERE d.status = 'active'
GROUP BY d.city
HAVING COUNT(d.id) >= 3
ORDER BY donor_count DESC;

-- Q60: Staff departments that have processed more than 10 donations
SELECT 
    au.department,
    COUNT(DISTINCT d.id) as donations_processed,
    SUM(d.quantity_ml) as total_ml,
    ROUND(AVG(d.quantity_ml), 2) as avg_donation_ml
FROM donations d
JOIN admin_users au ON d.collected_by = au.id
WHERE d.status = 'approved'
GROUP BY au.department
HAVING COUNT(DISTINCT d.id) >= 10
ORDER BY donations_processed DESC;

-- =====================================================
-- SECTION 8: AGGREGATE FUNCTIONS (5 queries)
-- =====================================================

-- Q61: Overall blood bank statistics
SELECT 
    COUNT(DISTINCT CASE WHEN status = 'active' THEN id END) as active_donors,
    COUNT(DISTINCT CASE WHEN status = 'inactive' THEN id END) as inactive_donors,
    (SELECT COUNT(*) FROM donations WHERE status = 'approved') as approved_donations,
    (SELECT SUM(quantity_ml) FROM donations WHERE status = 'approved') as total_ml_in_stock,
    (SELECT COUNT(*) FROM blood_requests WHERE status = 'fulfilled') as fulfilled_requests,
    (SELECT COUNT(*) FROM patients WHERE status = 'active') as active_patients
FROM donors;

-- Q62: Blood inventory summary with min/max/avg
SELECT 
    'Blood Stock Summary' as summary_type,
    COUNT(*) as total_blood_types,
    SUM(quantity_units) as total_units,
    SUM(quantity_ml) as total_ml,
    ROUND(AVG(quantity_units), 2) as avg_units,
    MIN(quantity_units) as min_units,
    MAX(quantity_units) as max_units
FROM blood_stock;

-- Q63: Donation statistics by month
SELECT 
    DATE_FORMAT(donation_date, '%Y-%m') as month,
    COUNT(*) as total_collected,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
    SUM(quantity_ml) as total_ml_collected
FROM donations
WHERE donation_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
GROUP BY DATE_FORMAT(donation_date, '%Y-%m')
ORDER BY month DESC;

-- Q64: Request fulfillment metrics
SELECT 
    'Request Fulfillment' as metric,
    COUNT(*) as total_requests,
    SUM(CASE WHEN status = 'fulfilled' THEN 1 ELSE 0 END) as fulfilled,
    ROUND(SUM(CASE WHEN status = 'fulfilled' THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as fulfillment_rate,
    SUM(units_required) as total_units_required,
    SUM(units_fulfilled) as total_units_fulfilled
FROM blood_requests;

-- Q65: Donor performance metrics
SELECT 
    'Top 5 Donors' as ranking,
    GROUP_CONCAT(full_name ORDER BY donation_count DESC SEPARATOR ', ') as donor_names,
    SUM(donation_count) as combined_donations
FROM (
    SELECT full_name, donation_count
    FROM donors
    WHERE status = 'active'
    ORDER BY donation_count DESC
    LIMIT 5
) as top_donors;

-- =====================================================
-- SECTION 9: SUBQUERIES (7 queries)
-- =====================================================

-- Q66: Donors who have donated the maximum number of times
SELECT 
    full_name,
    phone,
    city,
    donation_count
FROM donors
WHERE donation_count = (SELECT MAX(donation_count) FROM donors)
    AND status = 'active';

-- Q67: Blood requests above average units required
SELECT 
    patient_id,
    blood_type_id,
    units_required,
    status,
    request_date
FROM blood_requests
WHERE units_required > (SELECT AVG(units_required) FROM blood_requests)
ORDER BY units_required DESC;

-- Q68: Donors in same cities as active patients
SELECT DISTINCT 
    d.full_name,
    d.city,
    d.phone,
    d.blood_type_id
FROM donors d
WHERE d.city IN (SELECT DISTINCT city FROM patients WHERE status = 'active')
    AND d.status = 'active'
ORDER BY d.city, d.full_name;

-- Q69: Staff members who collected donations with highest quality
SELECT 
    au.full_name,
    au.department,
    COUNT(d.id) as donations_collected,
    SUM(CASE WHEN d.test_result = 'negative' THEN 1 ELSE 0 END) as negative_tests,
    ROUND(SUM(CASE WHEN d.test_result = 'negative' THEN 1 ELSE 0 END) / COUNT(d.id) * 100, 2) as quality_rate
FROM donations d
JOIN admin_users au ON d.collected_by = au.id
WHERE d.status IN ('approved', 'rejected')
GROUP BY au.id, au.full_name, au.department
HAVING COUNT(d.id) >= 5
ORDER BY quality_rate DESC;

-- Q70: Patients who received blood vs those who didn't
SELECT 
    p.patient_name,
    p.hospital_name,
    CASE 
        WHEN p.id IN (SELECT patient_id FROM blood_requests WHERE status = 'fulfilled') THEN 'Received Blood'
        ELSE 'Awaiting/No Blood'
    END as blood_received_status,
    (SELECT COUNT(*) FROM blood_requests WHERE patient_id = p.id AND status = 'fulfilled') as fulfilled_requests
FROM patients p
WHERE p.status = 'active'
ORDER BY p.hospital_name;

-- Q71: Blood types in low stock compared to average
SELECT 
    bt.blood_type,
    bs.quantity_units,
    (SELECT AVG(quantity_units) FROM blood_stock) as avg_stock,
    bs.quantity_units - (SELECT AVG(quantity_units) FROM blood_stock) as difference
FROM blood_stock bs
JOIN blood_types bt ON bs.blood_type_id = bt.id
WHERE bs.quantity_units < (SELECT AVG(quantity_units) FROM blood_stock)
ORDER BY difference ASC;

-- Q72: Donation requests by donors in high-stock blood types
SELECT 
    d.full_name,
    d.city,
    bt.blood_type,
    bs.quantity_units as current_stock,
    COUNT(br.id) as pending_requests_for_type
FROM donors d
JOIN blood_types bt ON d.blood_type_id = bt.id
JOIN blood_stock bs ON bt.id = bs.blood_type_id
LEFT JOIN blood_requests br ON bt.id = br.blood_type_id AND br.status IN ('pending', 'approved')
WHERE d.status = 'active' 
    AND bs.quantity_units > (SELECT AVG(quantity_units) FROM blood_stock)
GROUP BY d.id, d.full_name, d.city, bt.blood_type, bs.quantity_units
ORDER BY pending_requests_for_type DESC;

-- =====================================================
-- SECTION 10: COMPLEX NESTED JOINs (5 queries)
-- =====================================================

-- Q73: Comprehensive donor-donation-request flow analysis
SELECT 
    d.full_name as donor_name,
    d.city as donor_city,
    COUNT(DISTINCT don.id) as donations_made,
    SUM(don.quantity_ml) as total_ml_donated,
    COUNT(DISTINCT br.id) as related_requests,
    COUNT(DISTINCT CASE WHEN br.status = 'fulfilled' THEN br.id END) as fulfilled_from_donor_blood_type
FROM donors d
LEFT JOIN donations don ON d.id = don.donor_id AND don.status = 'approved'
LEFT JOIN blood_requests br ON d.blood_type_id = br.blood_type_id AND br.status = 'fulfilled'
WHERE d.status = 'active'
GROUP BY d.id, d.full_name, d.city
ORDER BY total_ml_donated DESC;

-- Q74: Patient request tracking with specific donor matching
SELECT 
    p.patient_name,
    p.hospital_name,
    bt.blood_type,
    COUNT(DISTINCT br.id) as total_requests,
    COUNT(DISTINCT d.id) as matching_donors_available,
    SUM(CASE WHEN d.donation_count >= 3 THEN 1 ELSE 0 END) as experienced_donors
FROM patients p
JOIN blood_types bt ON p.blood_type_id = bt.id
LEFT JOIN blood_requests br ON p.id = br.patient_id
LEFT JOIN donors d ON bt.id = d.blood_type_id AND d.status = 'active'
WHERE p.status = 'active'
GROUP BY p.id, p.patient_name, p.hospital_name, bt.blood_type, bt.id
ORDER BY p.hospital_name;

-- Q75: Complete transaction audit trail
SELECT 
    r.receipt_number,
    r.receipt_date,
    r.transaction_type,
    CASE 
        WHEN r.transaction_type = 'donation' THEN d.full_name
        WHEN r.transaction_type = 'request' THEN p.patient_name
        ELSE 'N/A'
    END as involved_person,
    bt.blood_type,
    r.quantity_ml,
    au.full_name as processed_by,
    au.department,
    CASE 
        WHEN r.donation_id IS NOT NULL THEN (SELECT status FROM donations WHERE id = r.donation_id)
        WHEN r.request_id IS NOT NULL THEN (SELECT status FROM blood_requests WHERE id = r.request_id)
        ELSE 'N/A'
    END as current_status
FROM receipts r
LEFT JOIN donors d ON r.donor_id = d.id
LEFT JOIN patients p ON r.patient_id = p.id
LEFT JOIN blood_types bt ON r.blood_type = bt.blood_type
LEFT JOIN admin_users au ON r.issued_by = au.id
ORDER BY r.receipt_date DESC
LIMIT 100;

-- Q76: Staff performance comprehensive analysis
SELECT 
    au.full_name,
    au.department,
    au.role,
    COUNT(DISTINCT d.id) as donations_processed,
    COUNT(DISTINCT br.id) as requests_approved,
    SUM(d.quantity_ml) as total_ml_processed,
    COUNT(DISTINCT CASE WHEN d.test_result = 'negative' THEN d.id END) as quality_donations,
    COUNT(DISTINCT CASE WHEN br.status = 'fulfilled' THEN br.id END) as successfully_fulfilled
FROM admin_users au
LEFT JOIN donations d ON au.id = d.collected_by
LEFT JOIN blood_requests br ON au.id = br.approved_by
WHERE au.status = 'active'
GROUP BY au.id, au.full_name, au.department, au.role
ORDER BY donations_processed DESC;

-- Q77: Real-time inventory vs demand analysis
SELECT 
    bt.blood_type,
    bs.quantity_units,
    bs.quantity_ml,
    COUNT(DISTINCT CASE WHEN br.status = 'pending' THEN br.id END) as pending_requests,
    COUNT(DISTINCT CASE WHEN br.status = 'approved' THEN br.id END) as approved_requests,
    SUM(CASE WHEN br.status IN ('pending', 'approved') THEN br.units_required ELSE 0 END) as total_units_needed,
    CASE 
        WHEN bs.quantity_units >= SUM(CASE WHEN br.status IN ('pending', 'approved') THEN br.units_required ELSE 0 END) THEN 'Sufficient'
        WHEN bs.quantity_units > 0 THEN 'Partially Available'
        ELSE 'Out of Stock'
    END as availability_status
FROM blood_types bt
JOIN blood_stock bs ON bt.id = bs.blood_type_id
LEFT JOIN blood_requests br ON bt.id = br.blood_type_id
GROUP BY bt.id, bt.blood_type, bs.quantity_units, bs.quantity_ml
ORDER BY bs.quantity_units ASC;

-- =====================================================
-- SECTION 11: ADVANCED ANALYTICS (3 queries)
-- =====================================================

-- Q78: Donor and donation efficiency score
SELECT 
    'Efficiency Report' as report_type,
    COUNT(DISTINCT d.id) as total_active_donors,
    ROUND(COUNT(DISTINCT don.id) / COUNT(DISTINCT d.id), 2) as avg_donations_per_donor,
    ROUND(SUM(don.quantity_ml) / COUNT(DISTINCT don.id), 2) as avg_ml_per_donation,
    COUNT(DISTINCT CASE WHEN don.status = 'approved' THEN don.id END) as approved_count,
    ROUND(COUNT(DISTINCT CASE WHEN don.status = 'approved' THEN don.id END) / COUNT(DISTINCT don.id) * 100, 2) as approval_rate
FROM donors d
LEFT JOIN donations don ON d.id = don.donor_id AND d.status = 'active';

-- Q79: Request fulfillment performance over time
SELECT 
    DATE_FORMAT(br.request_date, '%Y-%m') as month,
    COUNT(*) as requests_made,
    COUNT(CASE WHEN br.status = 'fulfilled' THEN 1 END) as fulfilled,
    ROUND(COUNT(CASE WHEN br.status = 'fulfilled' THEN 1 END) / COUNT(*) * 100, 2) as fulfillment_percentage,
    AVG(DATEDIFF(br.fulfilled_date, br.request_date)) as avg_days_to_fulfill
FROM blood_requests br
WHERE br.status = 'fulfilled' AND br.fulfilled_date IS NOT NULL
GROUP BY DATE_FORMAT(br.request_date, '%Y-%m')
ORDER BY month DESC;

-- Q80: Comprehensive blood bank dashboard summary
SELECT 
    'Dashboard Summary' as metric_type,
    (SELECT COUNT(*) FROM donors WHERE status = 'active') as active_donors,
    (SELECT SUM(quantity_units) FROM blood_stock) as total_blood_units_in_stock,
    (SELECT COUNT(*) FROM blood_requests WHERE status = 'pending') as pending_requests,
    (SELECT COUNT(*) FROM donations WHERE status = 'approved' AND expiry_date > CURDATE()) as usable_donations,
    (SELECT COUNT(*) FROM patients WHERE status = 'active') as active_patients,
    (SELECT COUNT(*) FROM admin_users WHERE status = 'active') as active_staff;

-- =====================================================
-- END OF QUERIES
-- Total: 80 comprehensive SQL queries covering all academic requirements
-- =====================================================
