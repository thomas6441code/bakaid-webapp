<?php
include('db.php');

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the email from the request
    $email = $mysqli->real_escape_string(trim($_POST['email']));

    // Check if the admin exists
    $adminQuery = "SELECT * FROM admin WHERE ad_email = '$email'";
    $adminResult = $mysqli->query($adminQuery);

    if ($adminResult->num_rows === 0) {
        $errorMessage = 'Email not found';
    } else {
        // Generate a 6-digit OTP
        $otp = random_int(100000, 999999);

        // Set expiration for OTP (valid for 10 minutes)
        $expiresAt = date("Y-m-d H:i:s", strtotime('+10 minutes'));
        $admin = $adminResult->fetch_assoc();
        $adminId = $admin['ad_id'];

        // Save the OTP and expiration to the database
        $insertQuery = "INSERT INTO passwordreset (email, otp, expiresAt, adminId) 
                        VALUES ('$email', '$otp', '$expiresAt', '$adminId')
                        ON DUPLICATE KEY UPDATE 
                        otp='$otp', expiresAt='$expiresAt'";
        if ($mysqli->query($insertQuery) === TRUE) {
            // Send email with OTP
            $subject = 'Password Reset - OTP Verification';
            $message = "Your OTP for password reset is: $otp\nThis OTP is valid for 10 minutes.";
            $headers = 'From: hopeshayo1@gmail.com' . "\r\n" .
                       'Reply-To: info@bakaid.or.tz' . "\r\n" .
                       'X-Mailer: PHP/' . phpversion();

            if (mail($email, $subject, $message, $headers)) {
            // OTP sent successfully
            $successMessage = 'OTP sent to your email!';
            // Trigger JavaScript for redirection
            echo "<script>
                alert('$successMessage');
                setTimeout(function() {
                    window.location.href = 'reset_password.php';
                }, 2000);
            </script>";
            exit; // Ensure no further processing
            } else {
            // OTP sending failed
            $errorMessage = 'Error sending OTP email';
                
            }
        } else {
            $errorMessage = 'Error saving OTP to database';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FORGOT PASSWORD</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
        }
        .animate-spin {
            animation: spin 3s linear infinite;
        }
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <script>
        function changeButtonText() {
            const button = document.getElementById('submitButton');
            button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v8l4 4"/></svg> Sending...';
            button.disabled = true; // Disable the button to prevent multiple submissions
        }
    </script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen pb-14 pt-20 px-10">
    <div class="bg-white relative py-10 px-10 rounded-lg shadow-lg md:w-1/3 w-full mx-auto text-center">
        <div class="absolute -top-1/4 left-1/2 transform -translate-x-1/2 translate-y-1/2 bg-white p-3 h-36 w-36 rounded-full flex justify-center items-center">
            <div class="border-green-700 rounded-full border-4 h-full w-full">
                <img src="../public/images/logo.png" alt="Logo" class="animate-spin p-3 h-28 w-32">
            </div>
        </div>
        
        <h1 class='mt-20 md:mt-24 text-green-700 font-semibold mb-5 text-xl'>FORGOT PASSWORD</h1>

        <form method="POST" id="forgotPasswordForm" onsubmit="changeButtonText()">
            <div class="relative mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-green-700 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                    class="w-full pl-10 p-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-green-500"
                />
            </div>
            
            <?php if (isset($errorMessage)): ?>
                <div class="text-red-500 mb-4"><?php echo $errorMessage; ?></div>
            <?php endif; ?>
    
            <?php if (isset($successMessage)): ?>
                <div class="text-green-500 mb-4"><?php echo $successMessage; ?></div>
            <?php endif; ?>
            <button
                type="submit"
                id="submitButton"
                class="w-full bg-green-700 text-white p-2 rounded-lg hover:bg-green-800 transition duration-200 flex items-center justify-center gap-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-6-6l6 6-6 6"/>
                </svg>
                Send OTP
            </button>
        
        </form>
        <p class="mt-4 text-gray-600">Remembered your password? <a href="login.php" class="text-green-700">Login here</a></p>
    </div>
</body>
</html>

<?php
// Close the database connection
$mysqli->close();
?>
