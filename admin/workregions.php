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

// Fetch work regions from database
$sql = "SELECT * FROM workregion"; // Assuming the table is called work_regions
$result = $mysqli->query($sql);

$projects = [];
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}

$mysqli->close();
?>

    <!-- Main Content Area -->
   <div class="mt-16 md:ml-44 w-fit py-4 pb-10">
    <div class="min-w-full px-3 text-lg md:px-5">
        <div class="text-cyan-950 mb-20">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-3xl font-bold">WORK REGION</h1>
                <button onclick="createProject()" class="bg-blue-500 text-white px-4 py-2 rounded">Create
                    Region
                </button>
            </div>
            <div id="loading" class="h-screen flex-1 items-center justify-center hidden">
                <div class="flex-col justify-center items-center">
                    Loading...
                </div>
            </div>
            <div id="error" class="text-red-500 text-center hidden"></div>
            
            <div id="projectsTable" class="overflow-x-auto scrollbar-hide scrollbar-hidden">
                <table class="min-w-full rounded-lg shadow-md">
                    <thead>
                        <tr class="bg-gray-300 text-left">
                            <th class="px-4 py-2">ID</th>
                            <th class="py-2 px-4">Regions</th>
                            <th class="py-2 px-4 pr-32">Districts</th>
                            <th class="py-2 px-4 pr-32">Projects</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                            <tr class="border-t">
                                <td class="px-4 py-2"><?= $project['id'] ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($project['region']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($project['districts']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($project['projects']) ?></td>
                                <td class="px-2 py-2">
                                    <a href="workregionsedit.php?id=<?= $project['id'] ?>" class="bg-yellow-500 text-white px-4 py-1 mr-1 rounded text-sm">Edit</a>
                                    <button onclick="deleteMessage(<?php echo $project['id'] ?>)" class="bg-red-500 text-white px-4 py-1 mt-2 rounded text-sm">
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
        function createProject() {
            window.location.href = 'workregionscreate.php';
        }
        
        const deleteMessage = async (id) => {
            try {
                const model = 'workregion'
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
