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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize it
    $region = trim($_POST['region']);
    $districts = trim($_POST['districts']);
    $projects = trim($_POST['projects']);

    // Validate input
    if (!empty($region) && !empty($districts) && !empty($projects)) {
        // Prepare an SQL query to insert the data
        $stmt = $mysqli->prepare("INSERT INTO workregion (region, districts, projects) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $region, $districts, $projects);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to the workregions page after successful insertion
            echo "<script type='text/javascript'>
                            window.location.href = 'workregions.php';
                          </script>";
                    exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "All fields are required.";
    }
}

// Close the mysqli connection
$mysqli->close();
?>

<div class="mt-16 md:ml-44 w-fit py-4 pb-10">
    <div class="min-w-full px-3 text-lg md:px-5">
        <h1 class="text-3xl font-bold mb-6">Create Region</h1>
        <!-- Form starts here -->
        <form id="projectForm" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <input type="text" name="region" id="region" placeholder="Region Name" required
                    class="block w-full border p-3 rounded mb-2" />
            </div>

            <div class="md:col-span-2">
                <textarea name="districts" id="districts" placeholder="Work District" required
                    class="block w-full border p-4 rounded mb-2 h-28"></textarea>
            </div>

            <div class="md:col-span-2">
                <textarea name="projects" id="projects" placeholder="Work Project" required
                    class="block w-full border p-4 rounded mb-2 h-36"></textarea>
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