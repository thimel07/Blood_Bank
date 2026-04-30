<?php
/**
 * API: Search Blood
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/db_connect.php';

try {
    $blood_type = isset($_GET['blood_type']) ? sanitize($_GET['blood_type']) : '';

    if (empty($blood_type)) {
        sendJSON(['success' => false, 'message' => 'Blood type is required'], 400);
    }

    // Get blood type ID
    $stmt = $conn->prepare("SELECT blood_group_id FROM blood_group WHERE group_name = ? LIMIT 1");
    $stmt->bind_param("s", $blood_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendJSON(['success' => false, 'message' => 'Blood type not found'], 404);
    }

    $bloodTypeData = $result->fetch_assoc();
    $blood_type_id = $bloodTypeData['blood_group_id'];

    // Get TOTAL aggregated stock information
    $stmt = $conn->prepare("
        SELECT 
            bs.blood_group_id, 
            bg.group_name,
            SUM(bs.units_available) as total_units,
            COUNT(DISTINCT bs.stock_id) as locations
        FROM blood_stock bs
        JOIN blood_group bg ON bs.blood_group_id = bg.blood_group_id
        WHERE bs.blood_group_id = ?
        AND bs.units_available > 0
        GROUP BY bs.blood_group_id, bg.group_name
    ");
    $stmt->bind_param("i", $blood_type_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_units = intval($row['total_units']);
        
        if ($total_units > 0) {
            // Determine status
            $status = $total_units > 20 ? 'high' : ($total_units > 5 ? 'medium' : 'low');
            $status_text = $total_units > 20 ? 'In Stock' : ($total_units > 5 ? 'Low Stock' : 'Critical');
            
            sendJSON([
                'success' => true,
                'message' => 'Blood found',
                'data' => [
                    [
                        'blood_group_id' => $row['blood_group_id'],
                        'blood_type' => $row['group_name'],
                        'quantity_units' => $total_units,
                        'status' => $status,
                        'status_text' => $status_text,
                        'locations' => intval($row['locations'])
                    ]
                ]
            ]);
        } else {
            sendJSON(['success' => false, 'message' => 'No stock available for this blood type'], 404);
        }
    } else {
        sendJSON(['success' => false, 'message' => 'No stock available for this blood type'], 404);
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    sendJSON(['success' => false, 'message' => 'Server error'], 500);
}
?>
