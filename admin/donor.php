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

// Fetch donors from the database
function fetchDonors($mysqli, $limit, $offset) {
    $stmt = $mysqli->prepare("SELECT id, title, image FROM donor ORDER BY createdAt DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get total donor count for pagination
function getTotalDonorsCount($mysqli) {
    $result = $mysqli->query("SELECT COUNT(*) as total FROM donor");
    return $result->fetch_assoc()['total'];
}

// Handle donor deletion
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $mysqli->prepare("DELETE FROM donor WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    exit(); // Exit after deletion
}

// Pagination settings
$donorsPerPage = 100;
$totalDonors = getTotalDonorsCount($mysqli);
$totalPages = ceil($totalDonors / $donorsPerPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, min($currentPage, $totalPages)); 
$offset = ($currentPage - 1) * $donorsPerPage;

// Fetch donors for the current page
$donors = fetchDonors($mysqli, $donorsPerPage, $offset);
?>

    <title>ADMIN | DONORS</title>
  
  
      <!-- Main Content Area -->
    <div class="mt-16 md:ml-44 w-fit py-4 pb-10">
        <div class="min-w-full px-3 text-lg md:px-5">
            <div class="text-cyan-950 mb-20">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-3xl font-bold">DONORS</h1>
                    <a href="create_donor.php" class="bg-blue-500 text-white px-4 py-2 rounded">Create Donor</a>
                </div>
                <div id="loading" class="h-screen flex-1 items-center justify-center hidden">
                    <div class="flex-col justify-center items-center">
                        Loading...
                    </div>
                </div>
                <div id="error" class="text-red-500 text-center hidden"></div>
                <div id="donorsTable" class="overflow-x-auto scrollbar-hide">
                    <table class="min-w-full rounded-lg shadow-md">
                        <thead>
                            <tr class="bg-gray-300 text-left">
                                <th class="px-4 py-2">ID</th>
                                <th class="px-4 py-2">Title</th>
                                <th class="px-4 pr-44 py-2">Image</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donors as $index => $donor): ?>
                                <tr class="border-t">
                                    <td class="px-4 py-2"><?php echo $index + 1 + $offset; ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($donor['title']) ?></td>
                                    <td class="px-4 py-2">
                                        <img src="../public/images/<?= htmlspecialchars($donor['image']) ?>" alt="Main" class="h-40 min-w-60 object-fit">
                                    </td>
                                    <td class="px-2 py-2">
                                        <form action="" method="POST">
                                            <input type="hidden" name="id" value="<?= $donor['id'] ?>">
                                            <button type="submit" name="delete" class="bg-red-500 text-white px-4 py-1 my-1 rounded mx-1">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
              
            </div>
        </div>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $stmt = $mysqli->prepare("DELETE FROM donor WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF'] . "?page=" . $currentPage); 
        exit();
    }
    ?> 
</body>

</html>