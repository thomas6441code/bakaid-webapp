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

// Fetch the event data if an ID is provided in the query string
$eventId = $_GET['id'] ?? null;
$event = null;
if ($eventId) {
    $stmt = $mysqli->prepare("SELECT * FROM event WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $location = $_POST['location'];
    $date = $_POST['date']; // Assuming this is a datetime input
    $eventType = $_POST['eventType'];
    $description = $_POST['description'];
    $moreDescription = $_POST['moreDescription'];

    // Initialize image paths to existing values
    $mainImagePath = $event['mainImage'] ?? '';
    $image1Path = $event['image1'] ?? '';
    $image2Path = $event['image2'] ?? '';
    $image3Path = $event['image3'] ?? '';
    $image4Path = $event['image4'] ?? '';

    // Handle file uploads
    if (isset($_FILES['mainImage']) && $_FILES['mainImage']['error'] === UPLOAD_ERR_OK) {
        $mainImagePath = handleFileUpload($_FILES['mainImage']);
    }
    if (isset($_FILES['image1']) && $_FILES['image1']['error'] === UPLOAD_ERR_OK) {
        $image1Path = handleFileUpload($_FILES['image1']);
    }
    if (isset($_FILES['image2']) && $_FILES['image2']['error'] === UPLOAD_ERR_OK) {
        $image2Path = handleFileUpload($_FILES['image2']);
    }
    if (isset($_FILES['image3']) && $_FILES['image3']['error'] === UPLOAD_ERR_OK) {
        $image3Path = handleFileUpload($_FILES['image3']);
    }
    if (isset($_FILES['image4']) && $_FILES['image4']['error'] === UPLOAD_ERR_OK) {
        $image4Path = handleFileUpload($_FILES['image4']);
    }

    // Update event in the database
    $stmt = $mysqli->prepare("UPDATE event SET title = ?, location = ?, date = ?, eventType = ?, description = ?, moreDescription = ?, mainImage = ?, image1 = ?, image2 = ?, image3 = ?, image4 = ?, updatedAt = NOW() WHERE id = ?");
    $stmt->bind_param("sssssssssssi", $title, $location, $date, $eventType, $description, $moreDescription, $mainImagePath, $image1Path, $image2Path, $image3Path, $image4Path, $eventId);

    if ($stmt->execute()) {
        echo "<script>window.location.href = 'event.php';</script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$mysqli->close();

// Function to handle file upload
function handleFileUpload($file) {
    $targetDir = "../public/images/";
    $originalFileName = basename($file["name"]);
    $imageFileType = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

    // Check if file is a valid image
    if (getimagesize($file["tmp_name"]) !== false) {
        if ($file["size"] <= 5000000 && in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            // Create a unique name for the image
            $uniqueFileName = uniqid('img_', true) . '.' . $imageFileType;

            // Move the uploaded file to the target directory with the unique name
            if (move_uploaded_file($file["tmp_name"], $targetDir . $uniqueFileName)) {
                return $uniqueFileName; // Return the unique file name
            } else {
                echo "Error uploading the file.";
            }
        } else {
            echo "Invalid image size or type.";
        }
    } else {
        echo "File is not an image.";
    }
    return null; // Return null if there was an error
}
?>

<!-- Main Content Area -->
<div class="mt-16 md:ml-44 w-fit py-4 pb-10">
    <div class="min-w-full px-3 text-lg md:px-5">
        <div class="text-cyan-950 mb-20">
            <h1 class="text-4xl font-bold mb-6">Edit Event</h1>
            <form id="eventForm" class="space-y-6" method="POST" enctype="multipart/form-data">
                <!-- Title Field -->
                <div class="mb-6">
                    <label class="block text-lg font-semibold mb-2">Title</label>
                    <input type="text" name="title" id="title" placeholder="Event Title" value="<?php echo htmlspecialchars($event['title'] ?? ''); ?>" required
                        class="w-full p-3 border rounded-md" />
                </div>

                <!-- Location Field -->
                <div class="mb-6">
                    <label class="block text-lg font-semibold mb-2">Location</label>
                    <input type="text" name="location" id="location" placeholder="Location" value="<?php echo htmlspecialchars($event['location'] ?? ''); ?>" required
                        class="w-full p-3 border rounded-md" />
                </div>

                <!-- Date Field -->
                <div class="mb-6">
                    <label class="block text-lg font-semibold mb-2">Date</label>
                    <input type="datetime-local" name="date" id="date" value="<?php echo htmlspecialchars($event['date'] ?? ''); ?>" required
                        class="w-full p-3 border rounded-md" />
                </div>

                <!-- Event Type Field -->
                <div class="mb-6">
                    <label class="block text-lg font-semibold mb-2">Event Type</label>
                    <input type="text" name="eventType" id="eventType" placeholder="Event Type" value="<?php echo htmlspecialchars($event['eventType'] ?? ''); ?>" required
                        class="w-full p-3 border rounded-md" />
                </div>

                <!-- Description Field -->
                <div class="mb-6">
                    <label class="block text-lg font-semibold mb-2">Description</label>
                    <input type="text" name="description" id="description" placeholder="Brief event description" value="<?php echo htmlspecialchars($event['description'] ?? ''); ?>" required
                        class="w-full p-3 border rounded-md" />
                </div>

                <!-- More Description Field -->
                <div class="mb-6">
                    <label class="block text-lg font-semibold mb-2">More Description</label>
                    <input type="text" name="moreDescription" id="moreDescription" placeholder="Additional details" value="<?php echo htmlspecialchars($event['moreDescription'] ?? ''); ?>" required
                        class="w-full p-3 border rounded-md" />
                </div>

                <!-- Image Upload Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="mb-6">
                        <label class="block text-lg font-semibold mb-2">Main Image</label>
                        <input type ="file" name="mainImage" id="mainImage" class="block w-full p-3 border rounded-md" />
                    </div>
                    <div class="mb-6">
                        <label class="block text-lg font-semibold mb-2">Image 1</label>
                        <input type="file" name="image1" id="image1" class="block w-full p-3 border rounded-md" />
                    </div>
                    <div class="mb-6">
                        <label class="block text-lg font-semibold mb-2">Image 2</label>
                        <input type="file" name="image2" id="image2" class="block w-full p-3 border rounded-md" />
                    </div>
                    <div class="mb-6">
                        <label class="block text-lg font-semibold mb-2">Image 3</label>
                        <input type="file" name="image3" id="image3" class="block w-full p-3 border rounded-md" />
                    </div>
                    <div class="mb-6">
                        <label class="block text-lg font-semibold mb-2">Image 4</label>
                        <input type="file" name="image4" id="image4" class="block w-full p-3 border rounded-md" />
                    </div>
                </div>

                <!-- Current Image Display (Optional) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if ($event['mainImage']): ?>
                        <div class="mb-6">
                            <label class="block text-lg font-semibold mb-2">Current Main Image</label>
                            <img src="../public/images/<?php echo $event['mainImage']; ?>" alt="Current Main Image" class='object-cover h-56 w-full rounded-md cursor-pointer' />
                        </div>
                    <?php endif; ?>
                    <?php if ($event['image1']): ?>
                        <div class="mb-6">
                            <label class="block text-lg font-semibold mb-2">Current Image 1</label>
                            <img src="../public/images/<?php echo $event['image1']; ?>" alt="Current Image 1" class='object-cover h-56 w-full rounded-md cursor-pointer' />
                        </div>
                    <?php endif; ?>
                    <?php if ($event['image2']): ?>
                        <div class="mb-6">
                            <label class="block text-lg font-semibold mb-2">Current Image 2</label>
                            <img src="../public/images/<?php echo $event['image2']; ?>" alt="Current Image 2" class='object-cover h-56 w-full rounded-md cursor-pointer' />
                        </div>
                    <?php endif; ?>
                    <?php if ($event['image3']): ?>
                        <div class="mb-6">
                            <label class="block text-lg font-semibold mb-2">Current Image 3</label>
                            <img src="../public/images/<?php echo $event['image3']; ?>" alt="Current Image 3" class='object-cover h-56 w-full rounded-md cursor-pointer' />
                        </div>
                    <?php endif; ?>
                    <?php if ($event['image4']): ?>
                        <div class="mb-6">
                            <label class="block text-lg font-semibold mb-2">Current Image 4</label>
                            <img src="../public/images/<?php echo $event['image4']; ?>" alt="Current Image 4" class='object-cover h-56 w-full rounded-md cursor-pointer' />
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700">
                        Update 
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>