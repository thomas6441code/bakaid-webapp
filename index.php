<?php
ini_set('display_errors', 1);
include './includes/header.php'; 
include 'db.php'; 

// Fetch mission and vision
$missionVisionQuery = "SELECT vision, mission FROM missionvision LIMIT 1";
$missionVisionResult = $conn->query($missionVisionQuery);
$missionVision = $missionVisionResult->fetch_assoc();

// Fetch objectives
$objectivesQuery = "SELECT objectives FROM objective";
$objectivesResult = $conn->query($objectivesQuery);
$objectives = [];
while ($row = $objectivesResult->fetch_assoc()) {
    $objectives[] = $row['objectives'];
}

// Fetch statistics
$statisticsQuery = "SELECT SUM(totalCTC) AS total_ctc, SUM(totalClients) AS total_clients, SUM(totalFaithLeaders) AS total_faith_leaders, SUM(totalDistricts) AS total_districts FROM client";
$statisticsResult = $conn->query($statisticsQuery);
$statistics = $statisticsResult->fetch_assoc();

// Fetch team members
$teamMembersQuery = "SELECT * FROM teammember";
$teamMembersResult = $conn->query($teamMembersQuery);
$teamMembers = [];
while ($row = $teamMembersResult->fetch_assoc()) {
    $teamMembers[] = $row;
}

// Fetch projects
$projectsQuery = "SELECT * FROM workregion";
$projectsResult = $conn->query($projectsQuery);
$projects = [];
while ($row = $projectsResult->fetch_assoc()) {
    $projects[] = $row;
}

// Fetch slides
$slidesQuery = "SELECT * FROM slide";
$slidesResult = $conn->query($slidesQuery);
$slides = [];
while ($row = $slidesResult->fetch_assoc()) {
    $slides[] = $row;
}

// Fetch donors
$donorsQuery = "SELECT * FROM donor";
$donorsResult = $conn->query($donorsQuery);
$donors = [];
while ($row = $donorsResult->fetch_assoc()) {
    $donors[] = $row;
}

// Fetch images for the gallery
$imagesQuery = "SELECT * FROM photogallery"; // Assuming you have a gallery table
$imagesResult = $conn->query($imagesQuery);
$images = [];
while ($row = $imagesResult->fetch_assoc()) {
    $images[] = $row;
}

// Close the database connection
$conn->close();
?>

   
    <!-- Loading Spinner -->
    <div id="loading" class="h-28 items-center justify-center hidden">
        <div class="flex-col justify-center items-center">
            <div class="h-16 w-16 border-t-4 border-b-4 border-gray-900">
                Loading...
            </div>
        </div>
    </div>

    <div id="slides" class="relative w-full mt-20 slide hidden">
        <div class="overflow-hidde shadow-lg h-100"> 
            <img id="slide-image" src="" alt="" class="object-cover w-full h-full">
            <div class="absolute inset-0 flex justify-between items-center bg-gradient-to-t from-gray-900 via-transparent to-transparent text-white px-0 md:px-6">
                <div class="md:mt-24 mt-20 md:mx-12 mx-5">
                    <h2 id="slide-title" class="md:text-4xl text-xl font-bold md:mt-16 mt-5 md:mb-2"></h2>
                    <p id="slide-description" class="md:text-xl text-sm"></p>
                </div>
            </div>
        </div>

        <!-- Numbered Navigation -->
        <div id="navigation" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-4 z-20">
        </div>
    </div>
    
    


    <!-- Welcome section -->
    <section style="background-image:url('./public/images/logo.png');" class="relative flex items-center justify-center h-80 lg:min-h-80 mt-10 bg-cover bg-center">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative z-10 text-center text-white p-6 rounded-lg bg-opacity-80">
            <h1 class="lg:text-5xl text-3xl font-extrabold mb-4"> Welcome to Our Organization </h1>
            <p class="text-lg mb-6">Your generous donations fuel our efforts to create a positive impact. Join us in
                making a difference!</p>
            <a href="projects.php">
                <button
                    class="inline-block bg-yellow-400 text-black py-3 px-6 rounded-3xl shadow-lg hover:bg-yellow-300 transition duration-300">Explore
                    Projects</button>
            </a>
        </div>
    </section>

    <!-- Mission and Vision Section -->
    <section class="py-5 lg:px-8">
        <div class="p-8">
            <div class="mb-8 text-cyan-950">
                <h2 class="text-2xl font-bold py-4 text-center">OUR MISSION AND VISION</h2>
                <div class="flex flex-col space-y-4">
                    <div class="bg-gray-200 p-4 px-6 rounded shadow">
                        <h3 class="text-xl font-semibold">Vision</h3>
                        <p id="vision" class="text-gray-700"><?php echo htmlspecialchars($missionVision['vision']); ?></p>
                    </div>
                    <div class="bg-gray-200 p-4 px-6 rounded shadow">
                        <h3 class="text-xl font-semibold">Mission</h3>
                        <p id="mission" class="text-gray-700"><?php echo htmlspecialchars($missionVision['mission']); ?></p>
                    </div>
                </div>
            </div>

           <!-- Objectives Section -->
           <div class="mb-8 text-cyan-950">
                <h2 class="text-2xl font-bold py-4 text-center">OUR OBJECTIVES</h2>
                <div class="flex flex-col space-y-4">
                    <div class="bg-gray-200 p-4 px-6 rounded shadow">
                        <h2 class="text-xl font-semibold py-4 text-left">Objectives</h2>
                        <ul class="list-disc list-inside">
                            <?php foreach ($objectives as $objective): ?>
                                <li class="text-gray-700"><?php echo htmlspecialchars($objective); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <div class="lg:h-60 flex items-center justify-center bg-gradient-to-r from-blue-500 via-teal-500 to-green-500">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 p-10 lg:mx-5 w-full text-white">
            <div class="bg-white bg-opacity-20 flex-col items-center justify-center p-6 shadow-lg text-center rounded">
                <h3 class="text-[1.1rem] font-semibold mb-2">TOTAL CTC COVERED</h3>
                <p class="text-4xl font-bold"><?php echo htmlspecialchars($statistics['total_ctc']); ?></p>
            </div>
            <div class="bg-white bg-opacity-20 flex-col items-center justify-center p-6 shadow-lg text-center rounded">
                <h3 class="text-[1.1rem] font-semibold mb-2">TOTAL CLIENTS</h3>
                <p class="text-4xl font-bold"><?php echo htmlspecialchars($statistics['total_clients']); ?></p>
            </div>
            <div class="bg-white bg-opacity-20 flex-col items-center justify-center p-6 shadow-lg text-center rounded">
                <h3 class="text-[1.1rem] font-semibold mb-2">TOTAL FAITH LEADERS</h3>
                <p class="text-4xl font-bold"><?php echo htmlspecialchars($statistics['total_faith_leaders']); ?></p>
            </div>
            <div class="bg-white bg-opacity-20 flex-col items-center justify-center p-6 shadow-lg text-center rounded">
                <h3 class="text-[1.1rem] font-semibold mb-2">TOTAL DISTRICTS</h3>
                <p class="text-4xl font-bold"><?php echo htmlspecialchars($statistics['total_districts']); ?></p>
            </div>
        </div>
    </div>


    <!-- About Us Section -->
    <div id="aboutus" class="py-8 mt-5">
        <span class="py-8">.</span>
        <section class="bg-gradient-to-r from-gray-400 via-gray-800 to-gray-400 text-gray-100 text-start py-12">
            <h1 class="text-2xl text-center text-green-400 font-bold mb-4"> ABOUT US </h1>
            <div class="container mx-auto px-4">
                <h1 class="text-4xl text-center font-bold mb-4">Make a Difference</h1>
                <p class="text-lg text-center max-w-2xl mx-auto mb-6">Your generous donations empower us to
                    create
                    impactful solutions and support those in need. Join us in making a positive change in our
                    communities.</p>
                <div class="flex items-center justify-center mt-8">
                    <a href="donation.php">
                        <button
                            class="inline-block bg-green-400 text-black py-3 px-6 rounded-3xl shadow-lg hover:bg-sky-400 transition duration-300">DONATE
                            NOW</button>
                    </a>
                </div>
            </div>
        </section>
    </div>


    <!-- Gallery Section -->
    <div class="bg-slate-50 text-cyan-950 md:px-5">

        <h1 id="display-section" class="py-6 text-center text-3xl font-semibold">GALLERY</h1>

        <div class="flex flex-col md:flex-row gap-4 p-6 pb-14">
            <!-- Left Section: Large Image Display with Details -->
            <div id="largeImageContainer" class="flex-1 w-full h-auto">
                <!-- Large image and details will be inserted here dynamically -->
            </div>

            <!-- Right Section: Thumbnails -->
            <div id="thumbnailsContainer" class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-0 h-auto">
                <!-- Thumbnails will be inserted here dynamically -->
            </div>
        </div>

        <!-- Pagination Controls -->
        <div id="paginationContainer" class="flex justify-center my-2">
            <!-- Pagination buttons will be added here -->
        </div>
    </div>


       <!-- Team Members -->
    <div class="lg:px-28 p-3 py-10 bg-gray-100 max-w-full mx-auto">
        <h1 class="text-2xl text-center font-bold py-6 text-cyan-900">TEAM MEMBERS</h1>
        <div class="overflow-x-auto scrollbar-hide">
            <table class="w-full bg-white rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">#</th>
                        <th class="py-3 px-6 text-left pr-14">Name</th>
                        <th class="py-3 px-6 text-left">Sex</th>
                        <th class="py-3 px-6 text-left pr-5">Nationality</th>
                        <th class="py-3 px-6 text-left pr-10">Position</th>
                    </tr>
                </thead>
                <tbody id="leaders-tbody" class="text-gray-600 text-sm font-light">
                    <?php foreach ($teamMembers as $index => $member): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6"><?php echo $index + 1; ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($member['name']); ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($member['sex']); ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($member['nationality']); ?></td>
                            <td class ```php
                            <td class="py-3 px-6"><?php echo htmlspecialchars($member['position']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

         <!-- Were we Work -->
    <div class="lg:px-28  mb-14 p-3 pb-10 bg-gray-100 max-w-full mx-auto">
        <h1 class="text-2xl text-center font-bold py-6 text-cyan-900">WHERE WE WORK</h1>
        <div class="overflow-x-auto scrollbar-hide">
            <table class="w-full bg-white rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">#</th>
                        <th class="py-3 px-6 text-left pr-8">Regions</th>
                        <th class="py-3 px-6 text-left pr-16">Districts</th>
                        <th class="py-3 px-6 text-left pr-20">Projects</th>
                    </tr>
                </thead>
                <tbody id="projects-table-body" class="text-gray-600 text-sm font-light">
                    <?php foreach ($projects as $index => $project): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6"><?php echo $index + 1; ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($project['region']); ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($project['districts']); ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($project['projects']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
            </table>
        </div>
    </div>


    <!-- Donors/Company Logos Section  -->
    <section class=" bg-white w-full">
        <section class="py-16 text-gray-900 bg-white max-w-6xl mx-auto px-10 flex-col items-center justify-center">
            <h2 class="text-3xl font-semibold text-center pb-10">OUR DONORS</h2>
            <!--  {/* Grid Layout for Logos */} -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-8 items-center justify-center">
                <?php foreach ($donors as $donor): ?>
                    <img src="./public/images/<?php echo htmlspecialchars($donor['image']); ?>" alt="<?php echo htmlspecialchars($donor['title']); ?>" class="w-full h-auto">
                <?php endforeach; ?>
                </div>
        </section>
    </section>
          
    <!-- Call to Action Section -->
    <section class="bg-blue-500 bg-opacity-30 text-cyan-950 py-16">
        <div class="container mx-auto text-center">
            <h2 class="text-4xl font-bold mb-6">Join Us in Making a Difference</h2>
            <p class="text-lg mb-8">
                We believe in the power of community and collective action. Together, we can create lasting change.
            </p>
            <a href="contactus.php"
                class="bg-white text-blue-500 hover:bg-gray-200 py-3 px-8 rounded-lg transition duration-300">
                CONTACT US
            </a>
        </div>
    </section>

   <!-- Slides Scripts -->
<script>
    const slidesData = <?php echo json_encode($slides); ?>;
    let currentSlide = 0;
    let loading = true;

    const loadingslides = document.getElementById('slides');
    const slideImage = document.getElementById('slide-image');
    const slideTitle = document.getElementById('slide-title');
    const slideDescription = document.getElementById('slide-description');
    const navigation = document.getElementById('navigation');

    // Function to hide loading slides
    function hideLoadingSlides() {
        loadingslides.classList.remove('hidden'); // Add the hidden class
    }

    // Function to display slides after a delay
    function displaySlidesAfterDelay() {
        setTimeout(() => {
            loading = false; 
            hideLoadingSlides();
            displaySlide();
        }, 1500); 
    }

    // Display the current slide
    function displaySlide() {
        if (slidesData.length === 0) {
            slideTitle.textContent = 'No slides available.';
            return;
        }
        slideImage.src = `./public/images/${slidesData[currentSlide]?.image}`;
        slideImage.alt = slidesData[currentSlide]?.id;
        slideTitle.textContent = slidesData[currentSlide]?.title;
        slideDescription.textContent = slidesData[currentSlide]?.description;

        updateNavigation();
    }

    // Update navigation dots
    function updateNavigation() {
        navigation.innerHTML = '';
        slidesData.forEach((_, index) => {
            const navDot = document.createElement('div');
            navDot.className = `cursor-pointer ${currentSlide === index ? 'bg-blue-600 text-white' : 'bg-gray-300 text-black'} md:w-10 w-6 md:h-10 h-6 flex items-center justify-center skew-x-12`;
            navDot.onclick = () => goToSlide(index);
            navDot.innerHTML = `<span class="skew-x-[-12deg]">${`0${index + 1}`}</span>`;
            navigation.appendChild(navDot);
        });
    }

    // Go to a specific slide
    function goToSlide(index) {
        currentSlide = index;
        displaySlide();
    }

    // Automatic slide change
    setInterval(() => {
        if (!loading) { // Only change slide if not loading
            currentSlide = (currentSlide + 1) % slidesData.length;
            displaySlide();
        }
    }, 5000); // Change slide every 5 seconds

    // Show loading text and start the delay for displaying slides
    displaySlidesAfterDelay();
</script>
    <!-- Gallery Section -->
    <script>
        const ITEMS_PER_PAGE = 6;
        let images = <?php echo json_encode($images); ?>;
        let selectedImage = null;
        let currentPage = 1;

        // Render the large selected image with details
        function renderLargeImage(image) {
            const container = document.getElementById('largeImageContainer');
            container.innerHTML = `
            <img src='./public/images/${image.imageUrl}' alt="${image.title}" class="w-full h-56 md:h-auto max-h-80 object-cover shadow-lg">
            <div class="mt-2">
                <h2 class="text-2xl font-semibold">${image.title}</h2>
                <p class="text-gray-600 mt-1">${new Date(image.createdAt).toLocaleDateString()}</p>
                <p class="text-gray-700">${image.description}</p>
            </div>
        `;
        }

        
        // Render thumbnails for the current page
        function renderThumbnails() {
            const container = document.getElementById('thumbnailsContainer');
            container.innerHTML = ''; // Clear previous thumbnails

            const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
            const currentImages = images.slice(startIndex, startIndex + ITEMS_PER_PAGE);

            currentImages.forEach(image => {
                const imgElement = document.createElement('img');
                imgElement.src = `./public/images/${image.imageUrl}`;
                imgElement.alt = image.title;
                imgElement.className = `w-full h-44 md:h-36 object-cover cursor-pointer border-2 ${selectedImage?.id === image.id ? 'border-indigo-500' : 'border-transparent'}`;
                imgElement.addEventListener('click', () => handleImageClick(image));
                container.appendChild(imgElement);
            });
        }


         // Handle clicking on a thumbnail image
         function handleImageClick(image) {
            selectedImage = image;
            renderLargeImage(image);

            // Scroll to the large image section on small devices
            if (window.innerWidth < 768) {
                document.getElementById('display-section').scrollIntoView({ behavior: 'smooth' });
            }

            renderThumbnails(); // Re-render thumbnails to update border styles
        }

        // Render pagination buttons
        function renderPagination() {
            const totalPages = Math.ceil(images.length / ITEMS_PER_PAGE);
            const container = document.getElementById('paginationContainer');
            container.innerHTML = ''; // Clear previous buttons

            for (let i = 1; i <= totalPages; i++) {
                const button = document.createElement('button');
                button.textContent = i;
                button.className = `mx-2 px-4 py-2 rounded ${currentPage === i ? 'bg-blue-500 text-white' : 'bg-gray-300 text-black'}`;
                button.addEventListener('click', () => handlePageChange(i));
                container.appendChild(button);
            }

            totalPages < 2 && container.classList.add('hidden');
        }

        function handlePageChange(pageNumber) {
            currentPage = pageNumber;
            renderThumbnails();
            renderPagination();
        }

        window.addEventListener('DOMContentLoaded', () => {
            if (images.length > 0) {
                selectedImage = images[0];
                renderLargeImage(selectedImage);
                renderThumbnails();
                renderPagination();
            }
        });
    </script>

<?php
include './includes/footer.php';
?>