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


// Fetch data from the database
function fetchData($mysqli, $query) {
    $result = $mysqli->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$clientsData = fetchData($mysqli, "SELECT  totalCTC,  totalFaithLeaders , totalDistricts FROM client");
$teamMembersData = fetchData($mysqli, "SELECT * FROM teammember");
$workRegionsData = fetchData($mysqli, "SELECT * FROM workregion");
$donorsData = fetchData($mysqli, "SELECT * FROM donor");
$projectsData = fetchData($mysqli, "SELECT * FROM project");
$eventsData = fetchData($mysqli, "SELECT * FROM event");
$messagesData = fetchData($mysqli, "SELECT * FROM message");
$subscribersData = fetchData($mysqli, "SELECT * FROM service");
$donationsData = fetchData($mysqli, "SELECT * FROM donation");

$totalClients = $clientsData[0]['totalCTC'] + $clientsData[0]['totalFaithLeaders'] + $clientsData[0]['totalDistricts'];

// Close the database connection
$mysqli->close();
?>

<!-- Main Content Area -->
  <!-- Main Content Area -->
    <div class="mt-16 md:ml-44 w-fit py-4">
        <div class="min-w-full px-3 text-lg md:px-5">
            <div class="text-cyan-950 mb-2">
                <h1 class="text-lg py-2 md:py-4 font-bold">Welcome to the Admin Dashboard</h1>
        
                <div id="dashboard" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 w-full">
                    <!-- Client Statistics -->
                    <div class="bg-blue-100 p-4 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold">CLIENTS STATISTICS</h2>
                        <p id="totalClients">Total Clients: <?php echo $totalClients; ?></p>
                        <p id="totalCTC">Total CTC: <?php echo $clientsData[0]['totalCTC']; ?></p>
                        <p id="totalFaithLeaders">Total Faith Leaders: <?php echo $clientsData[0]['totalFaithLeaders']; ?></p>
                        <p id="totalDistricts">Total Districts: <?php echo $clientsData[0]['totalDistricts']; ?></p>
                    </div>
        
                    <!-- Team Members -->
                    <div class="bg-green-100 p-4 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold">TEAM MEMBERS</h2>
                        <p id="totalTeamMembers">Total Team Members: <?php echo count($teamMembersData); ?></p>
                    </div>
        
                    <!-- Work Regions -->
                    <div class="bg-yellow-100 p-4 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold">WORK REGIONS</h2>
                        <p id="totalWorkRegions">Total Work Regions: <?php echo count($workRegionsData); ?></p>
                    </div>
        
                    <!-- Donors -->
                    <div class="bg-purple-100 p-4 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold">DONORS</h2>
                        <p id="totalDonors">Total Donors: <?php echo count($donorsData); ?></p>
                    </div>
        
                    <!-- Projects -->
                    <div class="bg-orange-100 p-4 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold">PROJECTS</h2>
                        <p id="totalProjects">Total Projects: <?php echo count($projectsData); ?></p>
                    </div>
        
                    <!-- Events -->
                    <div class="bg-teal-100 p-4 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold">EVENTS</h2>
                        <p id="totalEvents">Total Events: <?php echo count($eventsData); ?></p>
                    </div>
        
                    <!-- Graphs Section -->
                    <div class="bg-indigo-100 p-4 rounded-lg shadow-lg col-span-1 sm:col-span-2 lg:col-span-3">
                        <h2 class="text-xl font-semibold">GRAPHS</h2>
                        <div class="h-64">
                            <canvas id="myChart"></canvas>
                        </div>
                    </div>
        
                    <!-- Messages -->
                    <div class="bg-pink-100 p-4 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold">MESSAGES</h2>
                        <p id="totalMessages">Total Messages: <?php echo count($messagesData); ?></p>
                    </div>
        
                    <!-- Subscribers -->
                    <div class="bg-green-200 p-4 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold">Services</h2>
                        <p id="totalSubscribers">Total Services: <?php echo count($subscribersData); ?></p>
                    </div>
        
                    <!-- Donations -->
                    <div class="bg-red-100 p-4 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold">DONATIONS</h2>
                        <p id="totalDonations">Total Donations: <?php echo count($donationsData); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Prepare chart data
            const chartData = {
                labels: ['Messages', 'Services', 'Donations'],
                datasets: [{
                    label: 'Count',
                    data: [
                        <?php echo count($messagesData); ?>,
                        <?php echo count($subscribersData); ?>,
                        <?php echo count($donationsData); ?>
                    ],
                    backgroundColor: ['#3b82f6', '#34d399', '#fbbf24'],
                }],
            };

            // Create the chart
            const ctx = document.getElementById('myChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Data Overview',
                        },
                    },
                },
            });
        });
    </script>
</body>

</html>