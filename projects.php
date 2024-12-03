<?php
include './includes/header.php';
include 'db.php';

$projects = [];
$loading = true;
$error = '';

try {
    
    $sql = "SELECT * FROM project";
    $result = $conn->query($sql);
    
    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $projects[] = $row;
            }
        } else {
            $error = "No projects found.";
        }
        $loading = false;
    } else {
        $error = "Database query failed: " . $conn->error;
    }
    $loading = false;
} catch (Exception $e) {
    $error = $e->getMessage();
    $loading = false;
}

$conn->close();
?>

    <!-- Header -->
    <header class="text-center mt-20 px-4 bg-gradient-to-r from-blue-400 to-green-700 py-16 text-white">
        <h1 class="text-2xl font-bold">OUR PROJECTS</h1>
        <p class="text-lg mt-4">Join us at our upcoming projects or explore our recent and past projects to see how we’re making a difference.</p>
    </header>
    
     <?php if ($loading): ?>
        <div id="loading" class="h-96 flex-1 items-center justify-center">
            <div class="loader">Loading...</div>
        </div>
    <?php elseif ($error): ?>
        <div id="error" class="h-60 flex-1 items-center justify-center">
            <p class="text-gray-400 p-8 text-2xl font-bold"><?php echo $error; ?></p>
        </div>
    <?php else: ?>

    <!-- Projects Container -->
    <div id="projectsContainer" class="container min-h-screen mx-auto py-16 text-cyan-950">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 px-4" id="projectsGrid">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                    <div class="border p-3 pb-4 lg:mx-0 rounded-lg shadow-lg">
                        <img src="./public/images/<?php echo htmlspecialchars($project['mainImage'] ?: 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="w-full h-52 object-cover mb-4 rounded-sm cursor-pointer">
                        <h3 class="text-2xl font-bold mb-1"><?php echo htmlspecialchars($project['title']); ?></h3>
                        <p class="text-gray-600 mb-1"><strong>Date: </strong><?php echo date('F j, Y', strtotime($project['date'])); ?></p>
                        <p class="text-gray-600 mb-1"><strong>Location: </strong><?php echo htmlspecialchars($project['location']); ?></p>
                        <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($project['description']); ?></p>
                        <div class="mt-4">
                            <button onclick="viewProduct(<?php echo $project['id']; ?>)" class="bg-blue-100 hover:bg-blue-200 text-cyan-950 px-4 py-2 rounded">
                                Learn More
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-600">No projects available at this time.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <?php endif; ?>

    <script>
        // Navigate to the project details page
        function viewProduct(productId) {
            window.location.href = `project_details.php?id=${productId}`;
        }
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.title = "BAKAID | PROJECTS"; 
    });
</script>

<?php
include './includes/footer.php';
?>