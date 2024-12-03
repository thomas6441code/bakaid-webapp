<?php
include('db.php'); // Include your database connection script

// Functionality handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => 'Invalid request'];

    if (isset($_POST['action'])) {
        $action = $_POST['action'];

      if ($action === 'verifyOtp') {
            $otp = $mysqli->real_escape_string(trim($_POST['otp']));

            $query = "SELECT * FROM passwordreset WHERE otp = '$otp' AND expiresAt > NOW()";
            $result = $mysqli->query($query);

            if ($result->num_rows === 0) {
                $response['message'] = 'Invalid or expired OTP';
            } else {
                $response = ['success' => true, 'message' => 'OTP verified'];
            }
        } elseif ($action === 'resetPassword') {
            $newPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $otp = $mysqli->real_escape_string(trim($_POST['otp']));

            // Verify OTP
            $query = "SELECT * FROM passwordreset WHERE otp = '$otp' AND expiresAt > NOW()";
            $result = $mysqli->query($query);

            if ($result->num_rows === 0) {
                $response['message'] = 'Invalid or expired OTP';
            } else {
                $otpData = $result->fetch_assoc();
                $adminId = $otpData['adminId'];

                // Update password
                $updateQuery = "UPDATE admin SET password = '$newPassword' WHERE ad_id = '$adminId'";
                if ($mysqli->query($updateQuery)) {
                    $response = ['success' => true, 'message' => 'Password reset successfully'];
                } else {
                    $response['message'] = 'Error resetting password';
                }
            }
        }
    }
    echo json_encode($response);
    exit;
}
?>