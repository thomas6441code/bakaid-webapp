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
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
    $moreDescription = htmlspecialchars(trim($_POST['moreDescription']), ENT_QUOTES, 'UTF-8');
    $date = htmlspecialchars(trim($_POST['date']), ENT_QUOTES, 'UTF-8'); // Assuming this is a datetime input
    $location = htmlspecialchars(trim($_POST['location']), ENT_QUOTES, 'UTF-8');

    // Upload images
    $mainImage = $_FILES['mainImage'];
    $image1 = $_FILES['image1'];
    $image2 = $_FILES['image2'];
    $image3 = $_FILES['image3'];
    $image4 = $_FILES['image4'];

    $uploadDir = '../public/images/'; // Directory to store uploaded images
    $allowedFileTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];

    // Function to handle file upload
    function handleFileUpload($file, $uploadDir) {
        // If no file was uploaded, return null
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // No file uploaded, return null
        }

        // Check for other upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null; // Return null if there was an error
        }

        // Validate file type
        if (!in_array($file['type'], $GLOBALS['allowedFileTypes'])) {
            return null; // Return null if the file type is not allowed
        }

        $uniqueFileName = uniqid() . '-' . basename($file['name']);
        $tempFilePath = UPLOAD_TMP_DIR . $uniqueFileName;
        $finalFilePath = $uploadDir . $uniqueFileName;

        // Move the uploaded file to the temporary directory
        if (move_uploaded_file($file['tmp_name'], $tempFilePath)) {
            // Move the file from temp directory to the final upload directory
            if (rename($tempFilePath, $finalFilePath)) {
                return $uniqueFileName; // Return the unique file name
            }
        }
        return null; // Return null if there was an error
    }

    // Handle file uploads
    $uploadedMainImage = handleFileUpload($mainImage, $uploadDir);
    $uploadedImage1 = handleFileUpload($image1, $uploadDir);
    $uploadedImage2 = handleFileUpload($image2, $uploadDir);
    $uploadedImage3 = handleFileUpload($image3, $uploadDir);
    $uploadedImage4 = handleFileUpload($image4, $uploadDir);

    // Insert data into the database
    $stmt = $mysqli->prepare("INSERT INTO project (title, description, moreDescription, date, location, mainImage, image1, image2, image3, image4, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssssssssss", $title, $description, $moreDescription, $date, $location, $uploadedMainImage, $uploadedImage1, $uploadedImage2, $uploadedImage3, $uploadedImage4);

    if ($stmt->execute()) {
        echo "New project created successfully";
        echo "<script>window.location.href = 'project.php';</script>";
        exit;
    } else {
        $error = "Failed to insert project information into the database: " . $stmt->error;
    }
}
?>

<title>ADMIN | CREATE SERVICE</title>

<!-- Main Content Area -->
<div class="mt-16 md:ml-44 max-w-3xl py-4 pb-10">
    <div class="min-w-full px-3 text-lg md:px-5">
        <h1 class="text-3xl font-bold mb-6">Create New Project</h1>
        <?php if (!empty($error)): ?>
            <div class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form id="projectForm" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <input type="text" name="title" id="title" placeholder="Service Title" required
                    class="block w-full border p-3 rounded mb-2" />
            </div>

            <div class="md:col-span-2">
                <textarea name="description" id="description" placeholder="Description" required
                    class="block w-full border p-4 rounded mb-2 h-28"></textarea>
            </div>

            <div class="md:col-span-2">
                <textarea name="moreDescription" id="moreDescription" placeholder="More Description" required
                    class="block w-full border p-4 rounded mb-2 h-28"></textarea>
            </div>

            <div class="md:col-span-2">
                <input type="datetime-local" name="date" id="date" required
                    class="block w-full border p-3 rounded mb-2" />
            </div>

            <div class="md:col-span-2">
                <input type="text" name="location" id="location" placeholder="Location" required
                    class="block w-full border p-3 rounded mb-2" />
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Main Image</label>
                <input type="file" name="mainImage" id="mainImage" required
                    class="block w-full border p-3 rounded mb-4" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Image 1</label>
                <input type="file" name="image1" id="image1" class="block w-full border p-3 rounded mb-4" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Image 2</label>
                <input type="file" name="image2" id="image2" class="block w-full border p-3 rounded mb-4" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Image 3</label>
                <input type="file" name="image3" id="image3" class="block w-full border p-3 rounded mb-4" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Image 4</label>
                <input type="file" name="image4" id="image4" class="block w-full border p-3 rounded mb-4" />
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