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
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header('Location: login.php'); // Redirect to login page
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

include 'db.php';  

// update_mission_vision.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $vision = $_POST['vision'];
    $mission = $_POST['mission'];

    // Validate input
    if (!empty($id) && !empty($vision) && !empty($mission)) {
        // Assuming you have a database connection established
        $stmt = $mysqli->prepare("UPDATE missionvision SET vision = ?, mission = ? WHERE id = ?");
        $stmt->bind_param("ssi", $vision, $mission, $id);

        if ($stmt->execute()) {
            // Redirect back to the page or return a success message
            header("Location: others.php");
            exit();
        } else {
            // Handle error
            echo "Error updating record: " . $mysqli->error;
        }
    } else {
        // Handle validation error
        echo "All fields are required.";
    }
}
?>