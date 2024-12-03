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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['id']) && isset($data['model'])) {
        $id = (int)$data['id']; // Ensure id is an integer
        $model = $data['model'];

        // Define the valid models
        $validModels = [
            'slide',
            'photogallery',
            'missionvision',
            'donor',
            'message',
            'project',
            'event',
            'service',
            'client',
            'objective',
            'workregion',
            'teammember'
        ];

        // Check if the model is valid
        if (in_array($model, $validModels)) {
            // Prepare the SQL statement based on the model
            $sql = "DELETE FROM $model WHERE id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to delete record."]);
            }

            $stmt->close();
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Invalid model specified."]);
        }
        exit;
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Invalid input."]);
    }
}

// Close the database connection
$mysqli->close();
?>