<?php
include './includes/header.php';
include 'db.php'; 

// Fetch service details based on the service ID from the URL
$serviceId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$service = null;

if ($serviceId > 0) {
    $stmt = $conn->prepare("SELECT * FROM service WHERE id = ?");
    $stmt->bind_param("i", $serviceId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
    }

    $stmt->close();
}

$conn->close();
?>

    <!-- Service Details Container -->
    <div class="min-h-screen py-16 px-6 mt-10">
        <div class="max-w-6xl mx-auto" id="service-details">

            <!-- Loader -->
            <div class="flex items-center bg-gray-100 justify-center ">
                <div id="loader" class="hidden justify-center items-center">
                    <div class="min-h-screen w-fit">
                        Loading...
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <p id="error-message" class="text-center text-red-500 <?php echo $service ? 'hidden' : ''; ?>">Service not found.</p>

            <!-- Service Content -->
            <div id="service-content" class="<?php echo $service ? '' : 'hidden'; ?>">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h1 id="service-title" class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($service['title'] ?? ''); ?></h1>
                        <p id="service-category" class="text-lg text-gray-700 mb-4"><strong>Category:</strong> <?php echo htmlspecialchars($service['category'] ?? 'Not specified'); ?></p>
                        <p id="service-location" class="text-lg text-gray-700 mb-4"><strong>Location:</strong> <?php echo htmlspecialchars($service['locations'] ?? 'Not specified'); ?></p>
                        <p id="service-date" class="text-lg text-gray-700 mb-4"><strong>Date:</strong> <?php echo $service['createdAt'] ? date('F j, Y', strtotime($service['createdAt'])) : 'N/A'; ?></p>
                        <p id="service-group" class="text-lg text-gray-700 mb-8"><strong>Beneficial Group:</strong> <?php echo htmlspecialchars($service['beneficalGroup'] ?? 'Not specified'); ?></p>
                    </div>
                    <div>
                        <img id="main-image" src="./public/images/<?php echo htmlspecialchars($service['image'] ?? ''); ?>" alt="Main Service"
                            class="object-cover w-full h-64 rounded-md shadow-lg cursor-pointer"
                            onclick="openModal(this.src)">
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-white p-3 rounded-lg shadow-md mb-8">
                    <h2 class="text-2xl font-semibold mb-4">Description</h2>
                    <p id="service-description" class="text-gray-700 mb-4"><?php echo htmlspecialchars($service['description'] ?? 'No description available.'); ?></p>
                </div>

                <!-- Additional Images -->
                <h3 class="text-xl font-semibold mb-4">More Images</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="additional-images">
                    <?php 
                    for ($i = 1; $i <= 4; $i++) {
                        $additionalImageKey = 'image' . $i;
                        if (!empty($service[$additionalImageKey])) {
                            echo '<img src="./public/images/' . htmlspecialchars($service[$additionalImageKey]) . '" alt="Service Image ' . $i . '" class="object-cover w-full h-48 rounded-md shadow-md cursor-pointer" onclick="openModal(this.src)">';
                        }
                    }
                    ?>
                </div>

                <!-- Back Button -->
                <div class="mt-8">
                    <button onclick="goBack()"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-300">
                        Back to Services
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Full-Screen Image Modal -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-80 flex-1 items-center justify-center z-50 hidden">
        <div class="relative">
            <img id="modal-image" src="" alt="Full Screen" class="max-w-full max-h-[98vh] object-contain">
            <button onclick="closeModal()"
                class="absolute top-4 right-4 px-2 bg-gray-100 shadow-lg rounded-full text-gray-900 text-3xl">&times;</button>
        </div>
    </div>

    <script>
        function openModal(imageSrc) {
            document.getElementById('modal').classList.remove('hidden');
            document.getElementById('modal-image').src = imageSrc;
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
            document.getElementById('modal-image').src = '';
        }

        // Go back to the products page
        function goBack() {
            window.location.href = 'services.php';
        }
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.title = "BAKAID | SERVICES"; 
    });
</script>

<?php
include './includes/footer.php';
?>