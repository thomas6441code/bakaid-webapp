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

// Check if the 'id' parameter exists in the URL
if (isset($_GET['id'])) {
    $memberId = $_GET['id'];

    // Fetch team member data from the database
    $query = "SELECT * FROM teammember WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $memberId);
    $stmt->execute();
    $result = $stmt->get_result();
    $teamMember = $result->fetch_assoc();

    // Handle form submission for updating team member
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $sex = $_POST['sex'];
        $nationality = $_POST['nationality'];
        $position = $_POST['position'];

        // Update the team member data in the database
        $updateQuery = "UPDATE teammember SET name = ?, sex = ?, nationality = ?, position = ? WHERE id = ?";
        $updateStmt = $mysqli->prepare($updateQuery);
        $updateStmt->bind_param('ssssi', $name, $sex, $nationality, $position, $memberId);

        if ($updateStmt->execute()) {
            echo "<script>window.location.href = 'teammembers.php';</script>";
            exit;
        } else {
            echo "<p>Error: Could not update member.</p>";
        }
    }
} else {
    echo "<script>window.location.href = 'teammembers.php';</script>";
    exit;
}
?>

  <!-- Main Content Area -->
    <div class="mt-16 md:ml-44 w-fit py-4 pb-10">
        <div class="min-w-full px-3 text-lg md:px-5">
            <div class="text-cyan-950 mb-20">
                <h1 class="text-4xl font-bold mb-6">Edit Member</h1>
                <form method="POST" class="space-y-6">
                    <!-- Name Field -->
                    <div class="mb-6">
                        <label class="block text-lg font-semibold mb-2">Member Name</label>
                        <input type="text" name="name" id="name" placeholder="Member Name" required class="w-full p-3 border rounded-md" value="<?php echo htmlspecialchars($teamMember['name']); ?>" />
                    </div>
        
                    <!-- Gender Field -->
                    <div class="mb-6">
                        <label class="block text-lg font-semibold mb-2">Member Gender</label>
                        <textarea name="sex" id="sex" placeholder="Sex" required class="w-full p-3 border rounded-md resize-none"><?php echo htmlspecialchars($teamMember['sex']); ?></textarea>
                    </div>
        
                    <!-- Nationality Field -->
                    <div class="mb-6">
                        <label class="block text-lg font-semibold mb-2">Nationality</label>
                        <textarea name="nationality" id="nationality" placeholder="Nationality" required class="w-full p-3 border rounded-md resize-none"><?php echo htmlspecialchars($teamMember['nationality']); ?></textarea>
                    </div>
        
                    <!-- Position Field -->
                    <div class="mb-6">
                        <label class="block text-lg font-semibold mb-2">Position</label>
                        <input type="text" name="position" id="position" placeholder="Member Position" required class="w-full p-3 border rounded-md" value="<?php echo htmlspecialchars($teamMember['position']); ?>" />
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
