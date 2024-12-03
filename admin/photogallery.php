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

// Pagination settings
$limit = 6; // Number of images per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch data from the database with pagination
function fetchData($table, $limit, $offset) {
    global $mysqli; 
    $stmt = $mysqli->prepare("SELECT * FROM $table LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Count total photos for pagination
function countPhotos($table) {
    global $mysqli;
    $result = $mysqli->query("SELECT COUNT(*) as count FROM $table");
    return $result->fetch_assoc()['count'];
}

$photos = fetchData('photogallery', $limit, $offset);
$totalPhotos = countPhotos('photogallery');
$totalPages = ceil($totalPhotos / $limit);

define('UPLOAD_TMP_DIR', $_SERVER['DOCUMENT_ROOT'] . '/tmp/uploads/');

if (!is_dir(UPLOAD_TMP_DIR)) {
    if (!mkdir(UPLOAD_TMP_DIR, 0777, true)) {
        error_log("Failed to create temporary upload directory: " . UPLOAD_TMP_DIR);
        die('Temporary upload directory is not writable. Please check permissions.');
    }
}

// Initialize variables for error/success messages
$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $image = $_FILES['image'];

    // Check if the image was uploaded
    if ($image['error'] === UPLOAD_ERR_OK) {
        $uniqueFileName = uniqid() . '-' . basename($image['name']);
        $tempFilePath = UPLOAD_TMP_DIR . $uniqueFileName;
        $uploadDir = '/home/bakaid/public_html/public/images/';
        $finalFilePath = $uploadDir . $uniqueFileName;

        // Create the uploads directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($image['tmp_name'], $finalFilePath)) {
            // Insert data into the database
            $stmt = $mysqli->prepare("INSERT INTO photogallery (title, description, imageUrl, createdAt) VALUES (?, ?, ?,  NOW())");
            $stmt->bind_param("sss", $title, $description, $uniqueFileName);

            if ($stmt->execute()) {
                $successMessage = "Slide created successfully!";
                echo "<script type='text/javascript'>
                            window.location.href = 'photogallery.php';
                          </script>";
                    exit();
            } else {
                $errorMessage = "Error saving to database: " . $mysqli->error;
            }

            $stmt->close();
        } else {
            $errorMessage = "Failed to upload the image.";
        }
    } else {
        switch ($image['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errorMessage = "The uploaded file exceeds the allowed size.";
                break;
            case UPLOAD_ERR_PARTIAL:
                $errorMessage = "The uploaded file was only partially uploaded.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $errorMessage = "No file was uploaded.";
                break;
            default:
                $errorMessage = "An unknown error occurred.";
                break;
        }
    }
}

// Close the database connection
$mysqli->close();
?>
    <div id="head" class="mt-16 md:ml-44 w-fit py-4">
        <div class="min-w-full px-3 text-lg md:px-5">
            <div class="text-cyan-950 mb-20">
                <div class="p-4 max-w-full bg-white py-6">
                    <h1  class="text-2xl font-bold mb-4">PHOTO GALLERY</h1>
                      <?php if (!empty($successMessage)): ?>
            <p style="color: green;"><?php echo $successMessage; ?></p>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <p style="color: red;"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
                    <div class="overflow-x-auto scrollbar-hide min-w-full">
                        <div id="photoError" class="text-red-500 hidden"></div>
                        <form id="photoForm" class="mb-4 min-w-full" method="POST" enctype="multipart/form-data">
                            <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-2">
                                <input type="text" id="title" name="title" placeholder="Photo title" class="border px-2 py-2 mr-2" required />
                                <input type="text" id="description" name="description" placeholder="Photo description" class="border px-2 mr-2 py-2" required />
                                <input type="file" id="image" name="image" accept="image/*" class="border py-2 px-2" />
                                <button id="add" type="submit" name="action" class="bg-blue-500 hover:bg-blue-700 text-white rounded-md px-4 py-2 ml-2">Add Photo</button>
                            </div>
                        </form>
                       <div id="photoGallery" class="min-w-fit overflow-auto grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($photos as $photo): ?>
                                <div class="w-fit">
                                    <img src="../public/images/<?= htmlspecialchars($photo['imageUrl']) ?>" alt="<?= htmlspecialchars($photo['title']) ?>" class="min-w-full md:h-56 h-52" />
                                    <p class="text-left mt-2"><?= htmlspecialchars($photo['title']) ?></p>
                                    <p class="text-left text-sm py-2 mb-2"><?= htmlspecialchars($photo['description']) ?></p>
                                    <button onclick="deletePhoto(<?= $photo['id'] ?>)" class="bg-red-500 rounded-sm text-white px-4 py-1 ml-1">Delete</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="pagination my-10">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?= $i ?>" class="text-gray-50 bg-blue-200 p-2 rounded-md px-4 <?= ($i === $page) ? 'font-bold bg-blue-600 hover:bg-blue-500 text-white' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>

    const deletePhoto = async (id) => {
        try {
            const model = 'photogallery'
            const response = await fetch(`delete.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, model }),
            });
    
            if (!response.ok) {
                throw new Error(`Error: ${response.statusText}`);
            }
    
            const result = await response.json(); 
            if (result.success) {
                location.reload(); 
            } else {
                alert(`Error: ${result.error}`); 
            }
        } catch (error) {
            console.error('Failed to delete record:', error);
        }
    };
</script>

</body>
</html>