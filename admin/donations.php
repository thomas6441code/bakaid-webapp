<?php
session_save_path($_SERVER['DOCUMENT_ROOT'] . '/sessions');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

// Check for session timeout (1 hour)
$timeout_duration = 3600; // 1 hour in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Last request was more than 1 hour ago
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header('Location: login.php'); // Redirect to login page
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

include 'db.php'; 

// Get the JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && isset($data['isRead'])) {
    $id = $data['id'];
    $isRead = $data['isRead'] ? 1 : 0;

    // Update the donation status
    $sql = "UPDATE donation SET isRead = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $isRead, $id);

    if ($stmt->execute()) {
        // Fetch the updated donation
        $sql = "SELECT * FROM donation WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $updatedDonation = $result->fetch_assoc();

        // Return the updated donation as JSON
        echo json_encode($updatedDonation);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update donation status."]);
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input."]);
}

$conn->close();
?>