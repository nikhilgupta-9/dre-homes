<?php
// Set JSON header
header('Content-Type: application/json');

// Include database configuration
include('../db/config.php');
include('../includes/Logger.php');

$logger = new Logger($conn);

// Start session for authentication
session_start();

// Check if user is logged in
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Authentication required. Please login again.'
    ]);
    exit;
}

// Check database connection
if (!$conn || $conn->connect_error) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed'
    ]);
    exit;
}

function checkExist($team_id, $order_no, $team_desg)
{
    global $conn;

    $stmt = $conn->prepare("
        SELECT team_id
        FROM tbl_team
        WHERE team_desg_id = ?
        AND order_no = ?
        AND team_id != ?
    ");

    $stmt->bind_param("iii", $team_desg, $order_no, $team_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt->close();

        echo json_encode([
            'status' => 'error',
            'message' => 'Order number already exists for this designation'
        ]);
        exit;
    }

    $stmt->close();

    return true;
}

// Initialize response
$response = [
    'status' => 'error',
    'message' => 'Something went wrong'
];

// Check if required parameters are present
if (isset($_POST['team_id']) && isset($_POST['order_no'])) {

    // Sanitize and validate input
    $team_id_raw = trim($_POST['team_id']);
    $order_no_raw = trim($_POST['order_no']);
    $team_desg_id = trim($_POST['team_desg_id']);

    // Check for empty values - FIX THIS PART
    if ($order_no_raw === '') {
        echo json_encode([
            'status' => 'invalid',
            'message' => 'Order number cannot be empty'
        ]);
        exit;
    }

    // Handle NULL or empty string for order_no
    if ($order_no_raw === '' || $order_no_raw === null) {
        $order_no = null;
    } else {
        // Validate numeric
        if (!is_numeric($order_no_raw)) {
            echo json_encode([
                'status' => 'invalid',
                'message' => 'Order number must be a number'
            ]);
            exit;
        }
        $order_no = intval($order_no_raw);
    }

    // Validate team_id
    if (!is_numeric($team_id_raw)) {
        echo json_encode([
            'status' => 'invalid',
            'message' => 'Invalid team ID format'
        ]);
        exit;
    }

    $team_id = intval($team_id_raw);

    // Check if team exists
    $check_stmt = $conn->prepare("SELECT team_id, order_no FROM tbl_team WHERE team_id = ?");
    $check_stmt->bind_param("i", $team_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Team record not found'
        ]);
        $check_stmt->close();
        exit;
    }

    $current_data = $result->fetch_assoc();
    $check_stmt->close();

    $old_data = $logger->getOldRecordData('tbl_team', $team_id);

    // check if order already exist     
    checkExist($team_id, $order_no, $team_desg_id);

    // Update order number using prepared statement
    $update_stmt = $conn->prepare("UPDATE tbl_team SET order_no = ? WHERE team_id = ?");
    $update_stmt->bind_param("ii", $order_no, $team_id);

    if ($update_stmt->execute()) {
        if ($update_stmt->affected_rows >= 0) {

            $new_data = $logger->getOldRecordData('tbl_team', $team_id);

            $logger->log(
                'UPDATE',
                'tbl_team',
                $team_id,
                $old_data,
                $new_data,
                'Team Order no Update'
            );
            $response = [
                'status' => 'success',
                'message' => 'Order number updated successfully',
                'data' => [
                    'team_id' => $team_id,
                    'old_order_no' => $current_data['order_no'],
                    'new_order_no' => $order_no
                ]
            ];

            // Optional: Get new position after update
            $pos_stmt = $conn->prepare("SELECT COUNT(*) as position FROM tbl_team WHERE order_no <= ?");
            $pos_stmt->bind_param("i", $order_no);
            $pos_stmt->execute();
            $pos_result = $pos_stmt->get_result();
            $pos_data = $pos_result->fetch_assoc();
            $response['new_position'] = $pos_data['position'];
            $pos_stmt->close();

        } else {
            $response = [
                'status' => 'info',
                'message' => 'No changes made (same order number)'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Database update failed: ' . $conn->error
        ];
    }

    $update_stmt->close();

} else {
    $response = [
        'status' => 'invalid',
        'message' => 'Missing parameters: team_id and order_no are required'
    ];
}

// Close connection
$conn->close();

// Return JSON response
echo json_encode($response);
?>