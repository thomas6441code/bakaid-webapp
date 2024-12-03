<?php
include './includes/header.php'; 
include 'db.php'; 
// Fetch services from the database
$services = [];
$loading = true;
$error = '';

try {
    $sql = "SELECT id, title, image, createdAt, category, locations FROM service"; 
    $result = $conn->query($sql);
    
    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $services[] = $row;
            }
        } else {
             $error = "No services found.";
        }
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
    <header class="text-center mt-20 bg-gradient-to-r px-5 from-green-400 to-blue-600 py-16 text-white">
        <h1 class="text-2xl font-bold">OUR SERVICES</h1>
        <p class="text-lg mt-4">Join us at our services or explore services to see how we’re making a difference.</p>
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

    <!-- Services Container -->
    <div id="services-container" class="container min-h-screen mx-auto py-16 text-cyan-950">
        <div id="services-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 px-6">
            <?php foreach ($services as $service): ?>
                <div class="border p-3 pb-4 lg:mx-0 rounded-lg shadow-lg">
                    <?php if ($service['image']): ?>
                        <img src="./public/images/<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" class="w-full h-52 object-cover overflow-hidden mb-4 rounded-sm cursor-pointer" />
                    <?php endif; ?>
                    <h2 class="text-2xl font-semibold mb-1 px-2"><?php echo htmlspecialchars($service['title']); ?></h2>
                    <p class="text-lg text-gray-800 mb-1 px-2"><strong>Date:</strong> <?php echo date('F j, Y', strtotime($service['createdAt'])); ?></p>
                    <p class="text-lg text-gray-800 mb-1 px-2"><strong>Category:</strong> <?php echo htmlspecialchars($service['category'] ?: 'Not specified'); ?></p>
                    <p class="text-lg text-gray-800 mb-1 px-2"><strong>Location:</strong> <?php echo htmlspecialchars($service['locations'] ?: 'Not specified'); ?></p>
                    <div class="mt-4 p-2">
                        <a href="viewService(<?php echo $service['id']; ?>)" class="bg-blue-100 text-gray-900 px-4 py-2 rounded hover:bg-blue-200 transition-colors duration-300">
                            Learn More
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div id="pagination-controls" class="flex justify-center mt-8">
            <button id="prev-button" class="px-4 py -2 mr-2 bg-gray-200 text-gray-900 rounded hover:bg-gray-300" onclick="goToPreviousPage()">Previous</button>
            <span id="page-info" class="px-4 py-2 text-gray-900"></span>
            <button id="next-button" class="px-4 py-2 ml-2 bg-gray-200 text-gray-900 rounded hover:bg-gray-300" onclick="goToNextPage()">Next</button>
        </div>
    </div>
    <?php endif; ?>
    <script>
        let services = <?php echo json_encode($services); ?>;
        let currentPage = 1;
        const servicesPerPage = 12;

        document.addEventListener("DOMContentLoaded", () => {
            displayServices();
            updatePaginationControls();
        });

        function displayServices() {
            const servicesGrid = document.getElementById("services-grid");
            servicesGrid.innerHTML = '';

            const indexOfLastService = currentPage * servicesPerPage;
            const indexOfFirstService = indexOfLastService - servicesPerPage;
            const currentServices = services.slice(indexOfFirstService, indexOfLastService);

            currentServices.forEach(service => {
                const serviceDate = service.createdAt ? new Date(service.createdAt).toDateString() : 'N/A';
                const serviceCard = `
                    <div class="border p-2 pb-4 rounded-lg shadow-lg bg-white hover:shadow-xl transition-shadow duration-300">
                        ${service.image ? `<img src="./public/images/${service.image}" alt="${service.title}" class="w-full md:h-60 h-56 object-cover mb-4 rounded-sm cursor-pointer" />` : ''}
                        <h2 class="text-2xl font-semibold mb-1 px-2">${service.title}</h2>
                        <p class="text-lg text-gray-800 mb-1 px-2"><strong>Date:</strong> ${serviceDate}</p>
                        <p class="text-lg text-gray-800 mb-1 px-2"><strong>Category:</strong> ${service.category || 'Not specified'}</p>
                        <p class="text-lg text-gray-800 mb-1 px-2"><strong>Location:</strong> ${service.locations || 'Not specified'}</p>
                        <div class="mt-4 p-2">
                            <button onclick="viewService(${service.id})" class="bg-blue-100 text-gray-900 px-4 py-2 rounded hover:bg-blue-200 transition-colors duration-300">
                            Learn More
                            </button>
                        </div>
                    </div>
                `;
                servicesGrid.insertAdjacentHTML('beforeend', serviceCard);
            });
        }

        function updatePaginationControls() {
            const totalPages = Math.ceil(services.length / servicesPerPage);
            document.getElementById("page-info").textContent = `Page ${currentPage} of ${totalPages}`;
            const pagination = document.getElementById("pagination-controls");
            totalPages < 2 ? pagination.classList.add('hidden') : pagination.classList.remove('hidden');
            document.getElementById("prev-button").disabled = currentPage === 1;
            document.getElementById("next-button").disabled = currentPage === totalPages;
        }

        function goToNextPage() {
            const totalPages = Math.ceil(services.length / servicesPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                displayServices();
                updatePaginationControls();
            }
        }

        function goToPreviousPage() {
            if (currentPage > 1) {
                currentPage--;
                displayServices();
                updatePaginationControls();
            }
        }

        // Navigate to the project details page
        function viewService(serviceid) {
            window.location.href = `service_details.php?id=${serviceid}`;
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