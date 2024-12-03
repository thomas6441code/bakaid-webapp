<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="pageTitle">BAKAID | HOME</title>
    <link rel="stylesheet" href="./styles/output.css">
    <link rel="icon" href="./favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
        }
       .h-100 {
         height: 34rem; /* Default height for larger screens */
        }
        
        @media (max-width: 900px) {
            .h-100 {
                height: 20rem;
            }
        }
        
        @media (max-width: 768px)
            .h-100 {
                height: 15rem;
            }
        }
</style>
</head>

<body class="bg-gray-100 text-cyan-950 min-h-screen">
    <!-- Navbar -->
    <header class="fixed top-0 right-0 left-0 z-50 bg-gray-200 shadow-md">
        <div class="bg-customBlue shadow-sm py-2 h-20">
            <div class="flex items-center justify-between mx-au px-12 md:px-8 h-16">
                <!-- Logo Section -->
                <div class="lg:pl-10 md:ml-5 flex items-center justify-center">
                    <div class="flex-shrink-0 border-green-700 border-3 p-2 animate-spinn rounded-full">
                        <a href="index.php" onclick="handleLinkClick('BAKAID | HOME')">
                            <img src="./public/images/logo.png" alt="Logo" class="h-12 w-12">
                        </a>
                    </div>
                </div>

                <!-- Navigation Menu for Larger Screens -->
                <div class="hidden text tracking-wider font-semibold text-justify md:flex font-quick text-gray-950 space-x-3 justify-center items-center">
                    <div class="gap-x-5 ml-8">
                        <a href="index.php" class="relative mx-2 py-1 transition duration-300 hover:text-cyan-700"
                            onclick="handleLinkClick('BAKAID | HOME')">HOME</a>
                        <a href="#" class="relative mx-2 py-1 transition duration-300 hover:text-cyan-700"
                            onclick="scrollToSection('aboutus')">ABOUT US</a>
                        <a href="projects.php" class="relative mx-2 py-1 transition duration-300 hover:text-cyan-700"
                            onclick="handleLinkClick('BAKAID | PROJECTS')">PROJECTS</a>
                        <a href="services.php" class="relative mx-2 py-1 transition duration-300 hover:text-cyan-700"
                            onclick="handleLinkClick('BAKAID | SERVICES')">SERVICES</a>
                        <a href="events.php" class="relative mx-2 py-1 transition duration-300 hover:text-cyan-700"
                            onclick="handleLinkClick('BAKAID | NEWS & EVENTS')">NEWS & EVENTS</a>
                        <a href="contactus.php" class="relative mx-2 py-1 transition duration-300 hover:text-cyan-700"
                            onclick="handleLinkClick('BAKAID | CONTACT US')">CONTACT US</a>
                    </div>
                </div>

                <!-- Donate Button -->
                <div class="hidden mr-14 md:flex text-sm lg:flex items-center justify-center">
                    <a href="donation.php">
                        <button class="bg-green-400 text-black py-3 px-4 rounded-3xl shadow-lg hover:bg-sky-400 transition duration-300">DONATE</button>
                    </a>
                </div>

                <!-- Hamburger Menu for Small Screens -->
                <div class="font-quick md:hidden flex  items-center">
                    <button id="menuButton" onclick="toggleMenu()" class="focus:outline-none">
                        <svg id="menuIcon" class="w-8 h-8 text-cyan-900" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path id="menuOpenIcon" 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Dropdown Menu for Small Screens -->
        <div id="dropdownMenu"
            class="hidden absolute text bg-gray-200 text-gray-900 font-semibold left-0 right-0 top-[4.5rem] shadow-lg rounded-b-lg p-4 md:hidden">
            <a href="index.php" class="block mx-2 py-1 transition duration-300 hover:text-cyan-700"
                onclick="handleLinkClick('BAKAID | HOME')">HOME</a>
            <a href="#" class="block mx-2 py-1 transition duration-300 hover:text-cyan-700">ABOUT US</a>
            <a href="projects.php" class="block mx-2 py-1 transition duration-300 hover:text-cyan-700"
                onclick="handleLinkClick('BAKAID | PROJECTS')">PROJECTS</a>
            <a href="services.php" class="block mx-2 py-1 transition duration-300 hover:text-cyan-700"
                onclick="handleLinkClick('BAKAID | SERVICES')">SERVICES</a>
            <a href="events.php" class="block mx-2 py-1 transition duration-300 hover:text-cyan-700"
                onclick="handleLinkClick('BAKAID | NEWS & EVENTS')">NEWS & EVENTS</a>
            <a href="contactus.php" class="block mx-2 py-1 transition duration-300 hover:text-cyan-700"
                onclick="handleLinkClick('BAKAID | CONTACT US')">CONTACT US</a>
            
            <!-- Donate Button -->
            <div class="ml-2 my-3 md:flex text-sm lg:flex items-center justify-center">
                <a href="donation.php">
                    <button class="bg-green-400 text-black py-3 px-5 rounded-3xl shadow-lg hover:bg-sky-400 transition duration-300">DONATE</button>
                </a>
            </div>    
        </div>
    </header>
    
    <!-- Navbar Scripts -->
    <script>
        let isOpen = false;

        function toggleMenu() {
            const menuIcon = document.getElementById("menuIcon");
            const dropdownMenu = document.getElementById("dropdownMenu");
            isOpen = !isOpen;
            dropdownMenu.classList.toggle("hidden", !isOpen);
            menuIcon.innerHTML = isOpen
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
        }

        function handleLinkClick(title) {
            document.getElementById("pageTitle").innerText = title;
            if (isOpen) {
                toggleMenu(); // Close the menu if it's open
            }
        }

        function scrollToSection(id) {
            const element = document.getElementById(id);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    </script>
</body>
</html>