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


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form data
    $name = $_POST['name'];
    $sex = $_POST['sex'];
    $nationality = $_POST['nationality'];
    $position = $_POST['position'];

    // Insert data into the database
    $query = "INSERT INTO teammember (name, sex, nationality, position) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ssss', $name, $sex, $nationality, $position);

    if ($stmt->execute()) {
        echo "<script>window.location.href = 'teammembers.php';</script>";
        exit;
    } else {
        // Handle error
        echo "<p>Error: Could not create new member.</p>";
    }
}
?>

  <!-- Main Content Area -->
    <div class="mt-16 md:ml-44 w-fit py-4 pb-10">
        <div class="min-w-full px-3 text-lg md:px-5">
            <div class="text-cyan-950 mb-20">
        <h1 class="text-3xl font-bold mb-6">Create New Member</h1>
        <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <input type="text" name="name" id="name" placeholder="Member Full Name" required class="block w-full border p-3 rounded mb-2" />
            </div>

            <div class="md:col-span-2">
                <textarea name="sex" id="sex" placeholder="Member Sex" required class="block w-full border p-4 rounded mb-2"></textarea>
            </div>

            <div class="md:col-span-2">
                <textarea name="nationality" id="nationality" placeholder="Member Nationality" required class="block w-full border p-4 rounded mb-2"></textarea>
            </div>

            <div>
                <input type="text" name="position" id="position" placeholder="Member Position" required class="block w-full border p-3 rounded mb-2" />
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="w-fit bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>
</div>

</body>
</html>