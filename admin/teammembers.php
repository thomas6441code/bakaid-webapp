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

// Pagination settings
$projectsPerPage = 15;
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($currentPage - 1) * $projectsPerPage;

// Fetch team members with pagination
$sql = "SELECT id, name, sex, nationality, position FROM teammember LIMIT ?, ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $offset, $projectsPerPage);
$stmt->execute();
$result = $stmt->get_result();

$teamMembers = [];
while ($row = $result->fetch_assoc()) {
    $teamMembers[] = $row;
}

// Fetch total number of team members for pagination calculation
$sqlTotal = "SELECT COUNT(id) AS total FROM teammember";
$resultTotal = $mysqli->query($sqlTotal);
$totalMembers = $resultTotal->fetch_assoc()['total'];
$totalPages = ceil($totalMembers / $projectsPerPage);

$stmt->close();
$mysqli->close();
?>

      <!-- Main Content Area -->
   <div class="mt-16 md:ml-44 w-fit py-4 pb-10">
    <div class="min-w-full px-3 text-lg md:px-5">
        <div class="text-cyan-950 mb-20">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-3xl font-bold">MEMBERS</h1>
                    <a href="teammemberscreate.php" class="bg-blue-500 text-white px-4 py-2 rounded">Create Member</a>
                </div>

                <!-- Table -->
                <div id="projectsTable" class="overflow-x-auto scrollbar-hide scrollbar-hidden">
                    <table class="min-w-full rounded-lg shadow-md">
                        <thead>
                            <tr class="bg-gray-300 text-left">
                                <th class="px-4 py-2">ID</th>
                                <th class="py-2 px-4 pr-20">Name</th>
                                <th class="py-2 px-4">Sex</th>
                                <th class="py-2 px-4">Nationality</th>
                                <th class="py-2 px-4">Position</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teamMembers as $index => $member): ?>
                                <tr class="border-t">
                                    <td class="px-4 py-2"><?= $index + 1 + $offset ?></td>
                                    <td class="py-2 px-4"><?= $member['name'] ?></td>
                                    <td class="py-2 px-4"><?= $member['sex'] ?></td>
                                    <td class="py-2 px-4"><?= $member['nationality'] ?></td>
                                    <td class="py-2 px-4"><?= $member['position'] ?></td>
                                    <td class="px-2 py-2 gap-2">
                                        <a href="teammembersedit.php?id=<?= $member['id'] ?>" class="bg-yellow-500 text-white px-4 py-1 my-2 mr-1 rounded">Edit</a>
                                        <button onclick="deleteMessage(<?php echo $member['id'] ?>)" class="bg-red-500 text-white px-4 py-1 mt-2 rounded text-sm">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
     <script>
         const deleteMessage = async (id) => {
            try {
                const model = 'teammember'
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
