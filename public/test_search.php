<?php
/**
 * Test Search Page - Debug version
 */
require_once __DIR__ . '/../includes/db_connect.php';

$blood_type_id = intval($_GET['blood_type_id'] ?? 2); // Default to O+ (ID 2)

echo "<h2>Testing Blood Search for Blood Type ID: $blood_type_id</h2>";
echo "<hr>";

// Get blood type name
$result = $conn->query("SELECT blood_group_id, group_name FROM blood_group WHERE blood_group_id = $blood_type_id");
if ($row = $result->fetch_assoc()) {
    $blood_type_name = $row['group_name'];
    echo "<p><strong>Blood Type:</strong> " . htmlspecialchars($blood_type_name) . "</p>";
}

// Get TOTAL aggregated units
$result = $conn->query("
    SELECT SUM(units_available) as total FROM blood_stock 
    WHERE blood_group_id = $blood_type_id AND units_available > 0
");
$row = $result->fetch_assoc();
$total_units = $row['total'] ?? 0;
echo "<p><strong>TOTAL UNITS:</strong> <span style='font-size: 2rem; color: green;'>$total_units</span></p>";
echo "<hr>";

// Get individual stock details
echo "<h3>Individual Stock Locations:</h3>";
$result = $conn->query("
    SELECT 
        bs.stock_id,
        bs.units_available,
        bs.expiry_date,
        COALESCE(b.name, 'Main Hospital') as location
    FROM blood_stock bs
    LEFT JOIN branch b ON bs.branch_id = b.branch_id
    WHERE bs.blood_group_id = $blood_type_id AND bs.units_available > 0
    ORDER BY bs.expiry_date ASC
");
$count = 0;
while ($row = $result->fetch_assoc()) {
    $count++;
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0; background: #f9f9f9;'>";
    echo "<p><strong>Location:</strong> " . htmlspecialchars($row['location']) . "</p>";
    echo "<p><strong>Units:</strong> " . $row['units_available'] . "</p>";
    echo "<p><strong>Expiry:</strong> " . $row['expiry_date'] . "</p>";
    echo "</div>";
}
echo "<p><strong>Total Stock Locations:</strong> $count</p>";
echo "<hr>";

// Get donors
echo "<h3>Donors for this Blood Type:</h3>";
$result = $conn->query("
    SELECT DISTINCT
        d.donor_id,
        d.name,
        d.phone,
        d.last_donation_date,
        COUNT(dn.donation_id) as total_donations
    FROM donor d
    LEFT JOIN donation dn ON d.donor_id = dn.donor_id
    WHERE d.blood_group_id = $blood_type_id
    GROUP BY d.donor_id, d.name, d.phone, d.last_donation_date
    ORDER BY d.name
");
$donor_count = 0;
while ($row = $result->fetch_assoc()) {
    $donor_count++;
    echo "<p>" . htmlspecialchars($row['name']) . " - " . htmlspecialchars($row['phone']) . " (" . $row['total_donations'] . " donations)</p>";
}
echo "<p><strong>Total Donors:</strong> $donor_count</p>";

mysqli_close($conn);
?>
<hr>
<p><a href="/blood-bank/public/search_blood.php?blood_type_id=<?php echo $blood_type_id; ?>">Go to Real Search Page</a></p>