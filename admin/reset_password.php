<?php
session_save_path($_SERVER['DOCUMENT_ROOT'] . '/sessions');
session_start();
include('db.php'); // Include your database connection file

// Initialize error and success messages
$error = "";
$success = "";

// Check if the form was submitted and if required fields are set
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['otp']) && isset($_POST['adminId'])) {
    $otp = $_POST['otp'];
    $adminId = $_POST['adminId'];

    // Prepare SQL query to fetch OTP data for the given adminId
    $stmt = $mysqli->prepare("SELECT id, expiresAt, otp FROM passwordreset WHERE adminId = ? ORDER BY expiresAt DESC LIMIT 1");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $expiresAt, $dbOtp);
        $stmt->fetch();

        // Check if the OTP matches and is not expired
        if ($otp === $dbOtp) {
            $current_time = new DateTime();
            $expiry_time = new DateTime($expiresAt);

            if ($current_time < $expiry_time) {
                $success = "OTP verified successfully!";
                // Redirect or perform further actions as needed
                // Example: header('Location: reset_password.php');
                // exit;
            } else {
                $error = "The OTP has expired.";
            }
        } else {
            $error = "Invalid OTP.";
        }
    } else {
        $error = "No OTP found for the given admin ID.";
    }

    $stmt->close();
}

// Close the database connection
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen px-6">
    <div class="bg-white py-10 px-10 rounded-lg shadow-lg md:w-1/3 w-full">
        <h1 class="text-green-700 font-semibold text-2xl text-center mb-5">Verify OTP</h1>

        <!-- Display error or success messages -->
        <?php if ($error): ?>
            <p class="mb-4 text-red-500 text-center"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="mb-4 text-green-500 text-center"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="relative mb-4">
                <input
                    type="text"
                    name="otp"
                    placeholder="Enter OTP"
                    required
                    class="w-full pl-4 p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-green-500"
                />
            </div>

            <div class="relative mb-6">
                <input
                    type="number"
                    name="adminId"
                    placeholder="Enter Admin ID"
                    required
                    class="w-full pl-4 p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-green-500"
                />
            </div>

            <button
                type="submit"
                class="w-full bg-green-700 text-white p-2 rounded-lg hover:bg-green-800 transition duration-200 flex items-center justify-center gap-2"
            >
                Verify OTP
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="/" class="text-green-700 hover:underline">Back Home</a>
        </div>
    </div>
</body>

</html>
