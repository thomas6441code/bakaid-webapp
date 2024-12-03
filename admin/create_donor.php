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
include './includes/header.php';

define('UPLOAD_TMP_DIR', $_SERVER['DOCUMENT_ROOT'] . '/tmp/uploads/');

if (!is_dir(UPLOAD_TMP_DIR)) {
    if (!mkdir(UPLOAD_TMP_DIR, 0777, true)) {
        error_log("Failed to create temporary upload directory: " . UPLOAD_TMP_DIR);
        die('Temporary upload directory is not writable. Please check permissions.');
    }
}

$error = ''; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $image = $_FILES['image'];

    // Validate image upload
    $allowedFileTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if ($image['error'] === UPLOAD_ERR_OK && in_array($image['type'], $allowedFileTypes)) {
        $uniqueFileName = uniqid() . '-' . basename($image['name']);
        $tempFilePath = UPLOAD_TMP_DIR . $uniqueFileName;
        $uploadDir = '/home/bakaid/public_html/public/images/';
        $finalFilePath = $uploadDir . $uniqueFileName;

        // Move the uploaded file to the temporary directory
        if (move_uploaded_file($image['tmp_name'], $tempFilePath)) {
            // Move the file from temp directory to the final upload directory
            if (rename($tempFilePath, $finalFilePath)) {
                // Insert donor information into the database
                $stmt = $mysqli->prepare("INSERT INTO donor (title, image, updatedAt) VALUES (?, ?, NOW())");
                $stmt->bind_param("ss", $title, $uniqueFileName);
                if ($stmt->execute()) {
                    // Redirect using JavaScript
                    echo "<script type='text/javascript'>
                            window.location.href = 'donor.php';
                          </script>";
                    exit();
                } else {
                    // Capture the error message
                    $error = "Failed to insert donor information into the database: " . $stmt->error;
                }
            } else {
                $error = "Failed to move file to temporary directory.";
            }
        } else {
            $error = "File upload error: " . ($image['error'] === UPLOAD_ERR_OK ? "Invalid file type." : $image['error']);
        }
    }
}
?>

<title>ADMIN | DONORS</title>

<!-- Main Content Area -->
<div class="mt-16 md:ml-44 max-w-3xl py-4 pb-10">
    <div class="min-w-full px-3 text-lg md:px-5">
        <h1 class="text-3xl font-bold mb-6">Create Donor</h1>
        <?php if (!empty($error)): ?>
            <div class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form id="projectForm" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <input type="text" name="title" id="title" placeholder="Donor Title" required
                    class="block w-full border p-3 rounded mb-2" />
            </div>

            <div class="md:col-span-2">
                <label class=" block text-sm font-medium mb-1">Image</label>
                <input type="file" name="image" id="image" required
                    class="block w-full border p-3 rounded mb-4" />
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="w-fit bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>
</body>

</html>