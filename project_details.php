<?php
include './includes/header.php';
include 'db.php'; // Include the database connection

// Fetch project details based on the project ID from the URL
$projectId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$project = null;

if ($projectId > 0) {
    $stmt = $conn->prepare("SELECT * FROM project WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $project = $result->fetch_assoc();
        } else {
            echo "<p class='text-center text-red-500'>No project found with that ID.</p>";
        }

        $stmt->close();
    } else {
        echo "<p class='text-center text-red-500'>Failed to prepare SQL statement.</p>";
    }
} else {
    echo "<p class='text-center text-red-500'>Invalid project ID.</p>";
}

$conn->close();
?>

    <div id="project-container" class="max-w-6xl mt-20 mx-auto p-6 w-fit">
        <?php if ($project): ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h1 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($project['title']); ?></h1>
                    <p class="text-lg text-gray-700 mb-4"><strong>Location:</strong> <?php echo htmlspecialchars($project['location'] ?: 'Not specified'); ?></p>
                    <p class="text-lg text-gray-700 mb-4"><strong>Date:</strong> <?php echo $project['date'] ? date('F j, Y', strtotime($project['date'])) : 'N/A'; ?></p>
                </div>
                <div>
                    <?php if ($project['mainImage']): ?>
                        <img src="./public/images/<?php echo htmlspecialchars($project['mainImage']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" onclick="openModal('./public/images/<?php echo htmlspecialchars($project['mainImage']); ?>')" class="w-full h-64 object-cover mb-4 rounded-sm cursor-pointer">
                    <?php endif; ?>
                </div>
            </div>
    
            <div class="bg-white p-3 rounded-lg shadow-md mt-8">
                <h2 class="text-2xl font-semibold mb-4">Description</h2>
                <p class="text-gray-700 mb-4"><?php echo htmlspecialchars($project['description'] ?: 'No description available.'); ?></p>
                <div><?php echo htmlspecialchars($project['moreDescription'] ?: 'No additional description available.'); ?></div>
            </div>
    
            <div class="mt-8">
                <h3 class="text-xl font-semibold mb-4">More Images</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <?php if (!empty($project['image' . $i])): ?>
                            <img src="./public/images/<?php echo htmlspecialchars($project['image' . $i]); ?>" 
                            alt="Project Image <?php echo $i; ?>" 
                            onclick="openModal('./public/images/<?php echo htmlspecialchars($project['image' . $i]); ?>')" 
                            class="object-cover w-full h-48 rounded-md shadow-md cursor-pointer">
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>
    
            <div class="mt-8">
                <button onclick="goBack()"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-300">
                    Back to Projects
                </button>
            </div>
        <?php else: ?>
            <p class="text-center min-h-60 py-40 text-red-500">Project not found.</p>
        <?php endif; ?>
    </div>

<!-- Modal for full-screen image view -->
<div id="image-modal" class="fixed hidden inset-0 bg-black bg-opacity-20 flex-1 items-center justify-center z-50">
    <div class="relative">
        <img id="modal-image" src="" alt="Full Screen" class="max-w-full max-h-[98vh] object-contain">
        <button onclick="closeModal()"
            class="absolute top-4 right-4 px-2 bg-white shadow-lg rounded-full text-gray-900 text-3xl">&times;</button>
    </div>
</div>

<script>
    const imageModal = document.getElementById('image-modal');
    const modalImage = document.getElementById('modal-image');

    function openModal(imageSrc) {
        modalImage.src = imageSrc;
        imageModal.classList.remove('hidden');
    }

    function closeModal() {
        imageModal.classList.add('hidden');
        modalImage.src = '';
    }

    // Go back to the projects page
    function goBack() {
        window.location.href = 'projects.php';
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