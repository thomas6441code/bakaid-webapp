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

// Fetch data from the database
function fetchData($table) {
    global $mysqli; // Assuming you have a mysqli connection in db.php
    $result = $mysqli->query("SELECT * FROM $table");
    return $result->fetch_all(MYSQLI_ASSOC);
}

$missionVision = fetchData('missionvision'); 
$clients = fetchData('client'); 
$objectives = fetchData('objective');

// Handle AJAX requests for adding, updating, and deleting data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {

        case 'editMissionVision':
            $id = $_POST['id'];
            $vision = $mysqli->real_escape_string($_POST['vision']);
            $mission = $mysqli->real_escape_string($_POST['mission']);
            
            if (!empty($vision) && !empty($mission)) {
                $query = "UPDATE missionvision SET vision = '$vision', mission = '$mission' WHERE id = $id";
                if ($mysqli->query($query)) {
                    echo "Mission and Vision updated successfully!";
                } else {
                    echo "Error: " . $mysqli->error;
                }
            } else {
                echo "Both Vision and Mission must be provided!";
            }
            break;

        case 'editClient':
            $id = $_POST['id'];
            $totalCTC = $_POST['totalCTC'];
            $totalClients = $_POST['totalClients'];
            $totalFaithLeaders = $_POST['totalFaithLeaders'];
            $totalDistricts = $_POST['totalDistricts'];
            $mysqli->query("UPDATE client SET totalCTC = '$totalCTC', totalClients = '$totalClients', totalFaithLeaders = '$totalFaithLeaders', totalDistricts = '$totalDistricts' WHERE id = $id");
            break;

        case 'editObjective':
            $id = $_POST['id'];
            $objectivesText = $_POST['objectives'];
            $mysqli->query("UPDATE objective SET objectives = '$objectivesText' WHERE id = $id");
            break;
            
        case 'addObjective':
            $objectivesText = trim($_POST['objectives']);
            if (!empty($objectivesText)) {
                $stmt = $mysqli->prepare("INSERT INTO objective (objectives) VALUES (?)");
                $stmt->bind_param("s", $objectivesText);
                $stmt->execute();
                $stmt->close();
            if ($mysqli->query($query)) {
                echo "Objective added successfully!";
            } else {
                echo "Error: " . $mysqli->error;
            }}
            break;
    }
    // Redirect to avoid form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
   }
?>

    <div class="mt-14 md:ml-44 md:w-fit md:p-4 py-6 px-2 pb-10">
        <div class="max-w-full px-2">
            <div class="mt-10 mb-14 p-5 bg-white shadow-md text-cyan-950">
                <h2 class="text-2xl my-5 font-semibold">MISSION & VISION</h2>
                <div class="overflow-x-auto scrollbar-hide">
                    <table class="min-w-full rounded-lg shadow-md">
                        <thead>
                            <tr class="bg-gray-300 border-gray-200">
                                <th class="border px-4 py-2">#</th>
                                <th class="border pr-44 pl-4 py-2">Vision</th>
                                <th class="border pr-44 pl-4 py-2">Mission</th>
                                <th class="border px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($missionVision as $index => $item): ?>
                                <tr class="border-b border-gray-300">
                                    <td class="border px-4 py-2"><?= $index + 1 ?></td>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($item['vision']) ?></td>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($item['mission']) ?></td>
                                    <td class="border px-4 py-2">
                                        <button onclick="editMissionvision(1, 'Enter the vision', 'Enter the mission')" class="bg-yellow-500 text-white p-1 px-4 m-1 rounded">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div id="editModal" class="flex space-x-2 mt-2 hidden w-full">
                    <form id="editForm" method="POST" action="mission.php">
                        <input type="hidden" name="id" id="missionVisionId">
                        <div class="mb-4">
                            <label for="vision" class="block text-sm font-medium text-gray-700">Vision</label>
                            <input type="text" placeholder="Enter the vision" class="mt-1 px-3 py-2 block w- border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" id="vision" name="vision" required>
                        </div>
                        <div class="mb-4">
                            <label for="mission" class="block text-sm font-medium text-gray-700">Mission</label>
                            <input type="text" placeholder="Enter the mission" class="mt-1 px-3 py-2 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" id="mission" name="mission" required>
                        </div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
                        <button type="button" onclick="closeModal()" class="bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="max-w-full px-2">
            <div class="mt-10 mb-14 p-5 bg-white shadow-md text-cyan-950">
                <h2 class="text-2xl my-4 font-semibold">CLIENTS</h2>
                <div class="overflow-x-auto scrollbar-hide">
                    <table class="min-w-full rounded-lg shadow-md">
                        <thead>
                            <tr class="bg-gray-300 border-gray-200">
                                <th class="border px-4 py-2">#</th>
                                <th class="border px-4 py-2">Total-CTC</th>
                                <th class="border px-4 py-2">Total Clients</th>
                                <th class="border px-4 py-2">Total Faith-Leaders</th>
                                <th class="border px-4 py-2">Total Districts</th>
                                <th class="border px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $index => $client): ?>
                                <tr class="border-b border-gray-300">
                                    <td class="border px-4 py-2"><?= $index + 1 ?></td>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($client['totalCTC']) ?></td>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($client['totalClients']) ?></td>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($client['totalFaithLeaders']) ?></td>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($client['totalDistricts']) ?></td>
                                    <td class="border px-4 py-2">
                                        <button onclick="editClient(<?= $client['id'] ?>, '<?= htmlspecialchars($client['totalCTC']) ?>', '<?= htmlspecialchars($client['totalClients']) ?>', '<?= htmlspecialchars($client['totalFaithLeaders']) ?>', '<?= htmlspecialchars($client['totalDistricts']) ?>')" class="bg-yellow-500 text-white p-1 px-4 m-1 rounded">Edit</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="flex space-x-2 mt-2">
                    <input type="number" id="newTotalCTC" placeholder="Total CTC" class="border p-2 rounded w-full" />
                    <input type="number" id="newTotalClients" placeholder="Total Clients" class="border p-2 rounded w-full" />
                    <input type="number" id="newTotalFaithLeaders" placeholder="Total Faith Leaders" class="border p-2 rounded w-full" />
                    <input type="number" id="newTotalDistricts" placeholder="Total Districts" class="border p-2 rounded w-full" />
                    <button id="addClient" class="bg-blue-500 hover:bg-blue-700 text-white text-sm p-2 px-5 rounded">Edit Client</button>
                </div>
            </div>
        </div>

        <div class="max-w-full px-2">
            <div class="mt-5 p-5 bg-white shadow-md text-cyan-950">
                <h2 class="text-2xl my-5 font-semibold">OBJECTIVES</h2>
                <div class="overflow-x-auto scrollbar-hide">
                    <table class="min-w-full rounded-lg shadow-md">
                        <thead>
                            <tr class="bg-gray-300 border-gray-200">
                                <th class="border px-4 py-2">#</th>
                                <th class="border pl-4 pr-44 py-2">Objectives</th>
                                <th class="border px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($objectives as $index => $objective): ?>
                                <tr class="border-b border-gray-300">
                                    <td class="border px-4 py-2"><?= $index + 1 ?></td>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($objective['objectives']) ?></td>
                                    <td class="border px-4 py-2">
                                        <button onclick="editObjective(<?= $objective['id'] ?>, '<?= htmlspecialchars($objective['objectives']) ?>')" class="bg-yellow-500 text-white p-1 px-4 m-1 rounded">Edit</button>
                                        <button onclick="deletePhoto(<?= $objective['id'] ?>)" class="bg-red-500 rounded-sm text-white px-4 py-1 ml-1">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div id="edit" class="flex space-x-2 mt-2 hidden">
                    <input type="text" id="newObjective" placeholder="Objectives" class="border p-2 rounded w-full" />
                    <button id="addObjective" class="bg-blue-500 hover:bg-blue-700 text-white text-sm p-2 px-4 rounded">Edit Objective</button>
                </div>
                <div class="flex space-x-2 mt-2">
                    <input type="text" id="newObjectivee" placeholder="Enter a new objective" class="border p-2 rounded w-full" />
                    <button id="addObjectivee" class="bg-blue-500 hover:bg-blue-700 text-white text-sm p-2 px-4 rounded">Add Objective</button>
                </div>
            </div>
        </div>
    </div>

<script>

    document.getElementById('addObjectivee').onclick = async () => {
    const objectivesText = document.getElementById('newObjectivee').value.trim();
    if (!objectivesText) {
        alert('Please enter a valid objective.');
        return;
    }

    try {
        
        console.log(objectivesText)
        const response = await fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'addObjective',
                objectives: objectivesText
            })
        });

        if (response.ok) {
            location.reload();
        } else {
            alert('Failed to add objective. Please try again.');
        }
    } catch (error) {
        console.error('Error adding objective:', error);
        alert('An unexpected error occurred. Please try again.');
    }
    };
    
    function editMissionvision(id, vision, mission) {
    // Set the values in the modal
    document.getElementById('missionVisionId').value = id;
    document.getElementById('vision').value = vision;
    document.getElementById('mission').value = mission;

    // Show the modal
    document.getElementById('editModal').classList.remove('hidden');
}

    function closeModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function editClient(id, totalCTC, totalClients, totalFaithLeaders, totalDistricts) {
        document.getElementById('newTotalCTC').value = totalCTC;
        document.getElementById('newTotalClients').value = totalClients;
        document.getElementById('newTotalFaithLeaders').value = totalFaithLeaders;
        document.getElementById('newTotalDistricts').value = totalDistricts;
        document.getElementById('addClient').onclick = async () => {
            await fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'editClient',
                    id: id,
                    totalCTC: document.getElementById('newTotalCTC').value,
                    totalClients: document.getElementById('newTotalClients').value,
                    totalFaithLeaders: document.getElementById('newTotalFaithLeaders').value,
                    totalDistricts: document.getElementById('newTotalDistricts').value
                })
            });
            location.reload(); // Reload the page to see the updated entry
        };
    }

    function editObjective(id, objectives) {
        const objectivesedit = document.getElementById('edit')
        objectivesedit.classList.remove('hidden');
        
        document.getElementById('newObjective').value = objectives;
        document.getElementById('addObjective').onclick = async () => {
            await fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'editObjective',
                    id: id,
                    objectives: document.getElementById('newObjective').value
                })
            });
            location.reload();
        };
    }
    
    const deletePhoto = async (id) => {
        try {
            const model = 'objective'
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