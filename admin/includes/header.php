<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN | HOME</title>
    <link href="../styles/output.css" />
     <link rel="icon" href="./favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/react-chartjs-2/dist/index.umd.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react-css-spinners/0.10.0/ellipsis.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            transition: margin-left 0.5s;
        }

        .sidebar {
            transition: transform 0.3s ease;
        }

        .loading {
            display: none;
        }

        .loading.show {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        @media (max-width: 768px) {
            .main-content {
                width: 100vw;
                margin-left: 0;
            }
        }
        
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 10;
        }

        .main-content {
            width: 100vw;
            padding-top: 4rem;
            /* Adjust based on sidebar height */
            margin-left: 0;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Sidebar -->
    <div id="sidebar"
        class="sidebar bg-gray-800 text-white w-40 md:w-44 text-sm pt-20 h-screen p-5 transform -translate-x-full md:translate-x-0 md:relative">
        <ul class="space-y-3.5">
            <li><a href="index.php" class="flex pt-1 items-center"><i class="fas fa-tachometer-alt mr-2"></i>
                    Dashboard</a>
            </li>
            <li><a href="project.php" class="flex items-center"><i class="fas fa-folder-open mr-2"></i>
                    Projects</a></li>
            <li><a href="service.php" class="flex items-center"><i class="fas fa-cogs mr-2"></i>
                    Services</a></li>
            <li><a href="event.php" class="flex items-center"><i class="fas fa-calendar-alt mr-2"></i>
                    Events</a></li>
            <li><a href="teammembers.php" class="flex items-center"><i class="fas fa-users mr-2"></i>
                    Members </a></li>
            <li><a href="workregions.php" class="flex items-center"><i class="fas fa-users mr-2"></i>
                    Regions</a></li>
            <li><a href="donation.php" class="flex items-center"><i
                        class="fas fa-hand-holding-heart mr-2"></i> Donation</a>
            </li>
            <li><a href="photogallery.php" class="flex items-center"><i
                        class="fas fa-user-friends mr-2"></i> Photo Gallery</a></li>
            <li><a href="messages.php" class="flex items-center"><i class="fas fa-comment-dots mr-2"></i>
                    Messages</a></li>
            <li><a href="slides.php" class="flex items-center"><i class="fas fa-images mr-2"></i>
                    Slides</a></li>
            <li><a href="donor.php" class="flex items-center"><i class="fas fa-handshake mr-2"></i>
                    Donors</a></li>
            <li><a href="others.php" class="flex items-center"><i class="fas fa-ellipsis-h mr-2"></i>
                    Others</a></li>
            <li><a href="signup.php" class="flex items-center"><i class="fas fa-sign-in-alt mr-2"></i> Signup</a></li>
            <li><button onclick="logout()" class="flex items-center"><i class="fas fa-sign-out-alt mr-2"></i>
                    Logout</button>
            </li>
        </ul>
    </div>

    <!-- Fixed Navbar -->
    <nav class="bg-white shadow-md fixed top-0 left-0 right-0 flex justify-between items-center pl-8 pr-10 py-1 z-10">
        <div class="flex items-center">
            <img src="../public/images/logo.png" alt="Logo" class="h-14 w-16 rounded-sm mr-2">
            <span class="ml-2 text-xl hidden md:flex font-bold">Admin Dashboard</span>
        </div>
        <div class="flex items-center">
            <button id="toggleSidebar" class="text-gray-600 text-xl md:hidden">
                <i id="sidebarIcon" class="fas fa-bars fa-lg"></i>
            </button>
            <img src="../public/images/logo3.jpg" alt="Avatar" class="h-16 w-16 hidden md:flex rounded-sm">
        </div>
    </nav>

    <script>
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const sidebarIcon = document.getElementById('sidebarIcon');

        toggleSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');

            // Change icon based on sidebar state
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebarIcon.classList.remove('fa-times');
                sidebarIcon.classList.add('fa-bars');
            } else {
                sidebarIcon.classList.remove('fa-bars');
                sidebarIcon.classList.add('fa-times');
            }
        });

        function logout() {
         window.location.href = 'logout.php'; // Redirect to logout script
        }

    </script>