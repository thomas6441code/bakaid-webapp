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

// Fetch the work region data by id
$regionId = $_GET['id']; // Assuming `id` is passed in the query string

// Initialize the work region data
$region = $districts = $projects = "";

// Fetch data from database if `id` exists
if ($regionId) {
    $sql = "SELECT region, districts, projects FROM workregion WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $regionId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $region = $row['region'];
        $districts = $row['districts'];
        $projects = $row['projects'];
    } else {
        echo "Region not found.";
    }

    $stmt->close();
}

// Handle form submission for updating the region
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $region = $_POST['region'];
    $districts = $_POST['districts'];
    $projects = $_POST['projects'];

    // Prepare the update query
    $sql = "UPDATE workregion SET region = ?, districts = ?, projects = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sssi", $region, $districts, $projects, $regionId);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect after successful update
        echo "<script>window.location.href = 'workregions.php';</script>";
    } else {
        echo "Error updating region: " . $stmt->error;
    }

    $stmt->close();
}

// Close the connection
$mysqli->close();
?>

      <!-- Main Content Area -->
    <div class="mt-16 md:ml-44 w-fit py-4 pb-10">
        <div class="min-w-full px-3 text-lg md:px-5">
            <div class="text-cyan-950 mb-20">
                <h1 class="text-3xl font-bold mb-6">Edit Work Regions</h1>
        
                <!-- Edit Form -->
                <form id="projectForm" method="POST" class="space-y-6">
                    <!-- Region Field -->
                    <div class="mb-6">
                        <label class="block text-lg font-semibold mb-2">Work Regions</label>
                        <input type="text" name="region" id="region" placeholder="Work Region" value="<?php echo htmlspecialchars($region); ?>" required
                            class="w-full p-3 border rounded-md" />
                    </div>
        
                    <!-- Districts Field -->
                    <div class="mb-6">
                        <label class="block text-lg font-semibold mb-2">Districts</label>
                        <textarea name="districts" id="districts" placeholder="districts" required
                            class="w-full p-3 border rounded-md resize-none"><?php echo htmlspecialchars($districts); ?></textarea>
                    </div>
        
                    <!-- Projects Field -->
                    <div class="mb-6">
                        <label class="block text-lg font-semibold mb-2">Projects</label>
                        <textarea name="projects" id="projects" placeholder="Projects" required
                            class="w-full p-3 border rounded-md resize-none"><?php echo htmlspecialchars($projects); ?></textarea>
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
