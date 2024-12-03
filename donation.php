<?php
include 'db.php'; 
session_save_path($_SERVER['DOCUMENT_ROOT'] . '/sessions');
session_start();

// Initialize the step in session if not set
if (!isset($_SESSION['step'])) {
    $_SESSION['step'] = 1;
}

$step = $_SESSION['step'];
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle back button
    if (isset($_POST['back'])) {
        $_SESSION['step'] = max(1, $step - 1);
        header("Location: " . $_SERVER['PHP_SELF']); // Refresh page to update step
        exit;
    }

    // Step 1: Amount selection
    if ($step == 1) {
        if (isset($_POST['preset_amount']) && is_numeric($_POST['preset_amount'])) {
            $_SESSION['amount'] = $_POST['preset_amount'];
            $_SESSION['step'] = 2;
        } elseif (!empty($_POST['custom_amount']) && is_numeric($_POST['custom_amount'])) {
            $_SESSION['amount'] = $_POST['custom_amount'];
            $_SESSION['step'] = 2;
        } else {
            $error = 'Please select or enter a valid amount.';
        }
    }

    // Step 2: Donor information
    elseif ($step == 2) {
        $_SESSION['full_name'] = trim($_POST['full_name'] ?? '');
        $_SESSION['last_name'] = trim($_POST['last_name'] ?? '');
        $_SESSION['email'] = trim($_POST['email'] ?? '');

        if (empty($_SESSION['full_name']) || empty($_SESSION['last_name']) || empty($_SESSION['email'])) {
            $error = 'Please fill in all the required fields.';
        } else {
            $_SESSION['step'] = 3;
        }
    }

    // Step 3: Terms acceptance
    elseif ($step == 3) {
        if (isset($_POST['terms_accepted'])) {
            $_SESSION['terms_accepted'] = true;
            $_SESSION['step'] = 4;
        } else {
            $error = 'Please accept the terms and conditions to proceed.';
        }
    }

    // Step 4: Final submission
    elseif ($step == 4) {
        $amount = $_SESSION['amount'];
        $full_name = $_SESSION['full_name'];
        $last_name = $_SESSION['last_name'];
        $email = $_SESSION['email'];

        // Insert data into database
        $stmt = $conn->prepare("INSERT INTO donation (amount, fullName, lastName, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $amount, $full_name, $last_name, $email);

        if ($stmt->execute()) {
            $success = "Donation submitted successfully!";
            session_destroy(); // Clear session data after success
        } else {
            $error = "Failed to submit donation. Please try again.";
        }
        $stmt->close();
        $conn->close();
    }

    // Redirect to refresh page and avoid form resubmission
    if (!$error) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAKAID | DONATION</title>
    <link rel="stylesheet" href="./styles/output.css">
    <link rel="icon" href="/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body class="bg-gray-100 text-cyan-950 min-h-screen">
    <!-- Navbar -->
    <header class="fixed top-0 right-0 left-0 z-50 bg-gray-200 shadow-md">
        <div class="bg-customBlue shadow-sm py-2 h-20">
            <div class="flex items-center justify-between mx-auto px-12 md:px-6 lg:px-8 h-16">
                <!-- Logo Section -->
                <div class="lg:pl-7 flex items-center justify-center">
                    <div class="flex-shrink-0 border-green-700 border-3 p-2 animate-spinn rounded-full">
                        <a href="index.php" onclick="handleLinkClick()">
                            <img src="./public/images/logo.png" alt="Logo" class="h-12 w-12">
                        </a>
                    </div>
                </div>

                <!-- Navigation Menu for Larger Screens -->
                <div class="hidden text tracking-wider font-semibold text-justify md:flex font-quick text-gray-950 space-x-3 justify-center items-center">
                    <div class="gap-x-5 ml-8">
                        <a href="index.php" class="relative mx-2 py-1 transition duration-300 hover:text-cyan-700"
                            onclick="handleLinkClick()">HOME</a>
                        <a href="index.php" class="relative mx-2 py-1 transition duration-300 hover:text-cyan-700"
                            onclick="scrollToSection('aboutus')">ABOUT US</a>
                        <a  href="projects.php" class="relative mx-2 py-1 transition duration-300 hover:text-cyan-700"
                            onclick="handleLinkClick()">PROJECTS</a>
                        <a href="services.php" class="relative mx-2 py-1 transition duration-300 hover:text-cyan-700"
                            onclick="handleLinkClick()">SERVICES</a>
                        <a href="events.php" class="relative mx-2 py-1 transition duration-300 hover:text-cyan-700"
                            onclick="handleLinkClick()">NEWS & EVENTS</a>
                        <a href="contactus.php" class="relative mx-2 py-1 transition duration-300 hover:text-cyan-700"
                            onclick="handleLinkClick()">CONTACT US</a>
                    </div>
                </div>

                <!-- Donate Button -->
                <div class="hidden mr-7 md:flex text-sm lg:flex items-center justify-center">
                   <!--  <a href="donation.php">
                        <button class="bg-green-400 text-black py-3 px-4 rounded-3xl shadow-lg hover:bg-sky-400 transition duration-300">DONATE</button>
                    </a> -->
                </div>

                <!-- Hamburger Menu for Small Screens -->
                <div class="font-quick md:hidden flex pt-3 items-center">
                    <button id="menuButton" onclick="toggleMenu()" class="focus:outline-none">
                        <svg id="menuIcon" class="w-7 h-7 text-cyan-900" fill="none" stroke="currentColor"
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
                onclick="handleLinkClick()">HOME</a>
            <a href="index.php" class="block mx-2 py-1 transition duration-300 hover:text-cyan-700">ABOUT US</a>
            <a href="projects.php" class="block mx-2 py-1 transition duration-300 hover:text-cyan-700"
                onclick="handleLinkClick()">PROJECTS</a>
            <a href="services.php" class="block mx-2 py-1 transition duration-300 hover:text-cyan-700"
                onclick="handleLinkClick()">SERVICES</a>
            <a href="events.php" class="block mx-2 py-1 transition duration-300 hover:text-cyan-700"
                onclick="handleLinkClick()">NEWS & EVENTS</a>
            <a href="contactus.php" class="block mx-2 py-1 transition duration-300 hover:text-cyan-700"
                onclick="handleLinkClick()">CONTACT US</a>
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

        function handleLinkClick() {
            isOpen = false;
            document.getElementById("dropdownMenu").classList.add("hidden");
        }

        function scrollToSection(id) {
            const element = document.getElementById(id);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    </script>
    <div class="p-6 my-24 max-w-lg mx-auto text-cyan-950">

    <h1 class="text-3xl font-bold mb-6 text-center">DONATE NOW</h1>


    <?php if ($success): ?>
        <p class="text-green-500 mb-2"><?php echo $success; ?></p>
    <?php else: ?>

        <!-- Step 1: Select Donation Amount -->
        <?php if ($step == 1): ?>
            <form method="POST">
                <label class="block mb-2 font-semibold">Select Amount</label>
                <div class="grid grid-cols-2 gap-4">
                    <?php foreach ([5000, 10000, 100000, 500000] as $preset): ?>
                        <button type="submit" name="preset_amount" value="<?php echo $preset; ?>"
                                class="px-4 py-2 border rounded bg-gray-100 hover:bg-green-500 text-gray-700">
                            <?php echo number_format($preset); ?> TZS
                        </button>
                    <?php endforeach; ?>
                </div>
                <label class="block mb-2 font-semibold mt-4">Or Enter Custom Amount</label>
                <input type="number" name="custom_amount" placeholder="Enter custom amount"
                       class="w-full px-4 py-2 border rounded">
                            
                <?php if ($error): ?>
                    <p class="text-red-500 mb-2"><?php echo $error; ?></p>
                <?php endif; ?>       
                <button type="submit" class="w-full bg-green-500 text-white px-4 py-2 rounded mt-4">Next</button>
            </form>
        <?php endif; ?>

        <!-- Step 2: Donor Information -->
        <?php if ($step == 2): ?>
            <form method="POST">
                <label class="block mb-2 font-semibold">Full Name</label>
                <input type="text" name="full_name" placeholder="Ally Joeelly"
                       class="w-full px-4 py-2 border rounded">
                <label class="block mb-2 font-semibold">Last Name</label>
                <input type="text" name="last_name" placeholder="Juany"
                       class="w-full px-4 py-2 border rounded">
                <label class="block mb-2 font-semibold">Email</label>
                <input type="email" name="email" placeholder="Allyjuany@gmail.com"
                       class="w-full px-4 py-2 border rounded">    
                <?php if ($error): ?>
                    <p class="text-red-500 mb-2"><?php echo $error; ?></p>
                <?php endif; ?>
                <div class="flex justify-between mt-4">
                    <button name="back" class="w-1/2 mr-2 bg-gray-500 text-white px-4 py-2 rounded">Back</button>
                    <button type="submit" class="w-1/2 bg-green-500 text-white px-4 py-2 rounded">Next</button>
                </div>
            </form>
        <?php endif; ?>

        <!-- Step 3: Terms and Conditions -->
        <?php if ($step == 3): ?>
            <form method="POST">
            <label class="block mb-1 font-semibold text-lg">Terms and Conditions</label>
                <div class="bg-gray-100 p-2 border rounded overflow-y-scroll h-56">
                    <p class="text-sm">
                        Acceptance of any contribution, gift, or grant is at the discretion of the BAKAID. The BAKAID
                        will
                        not accept any gift unless it can be used or expended consistently with the purpose and mission
                        of
                        the BAKAID.<br /><br />
                        No irrevocable gift, whether outright or life -income in character, will be accepted if under
                        any
                        reasonable set of circumstances the gift would jeopardize the donor’s financial
                        security.<br /><br />
                        The BAKAID will refrain from providing advice about the tax or other treatment of gifts and will
                        encourage donors to seek guidance from their own professional advisers to assist them in the
                        process
                        of making their donation.<br /><br />
                        The BAKAID will accept donations of cash or publicly traded securities. Gifts of in-kind
                        services
                        will be accepted at the discretion of the BAKAID.<br /><br />
                        Certain other gifts, real property, personal property, in-kind gifts, non-liquid securities, and
                        contributions whose sources are not transparent or whose use is restricted in some manner, must
                        be
                        reviewed prior to acceptance due to the special obligations raised or liabilities they may pose
                        for
                        BAKAID.<br /><br />
                        The BAKAID will provide acknowledgments to donors meeting tax requirements for property received
                        by
                        the charity as a gift. However, except for gifts of cash and publicly traded securities, no
                        value
                        shall be ascribed to any receipt or other form of substantiation of a gift received by
                        BAKAID.<br /><br />
                        The BAKAID will respect the intent of the donor relating to gifts for restricted purposes and
                        those
                        relating to the desire to remain anonymous. With respect to anonymous gifts, the BAKAID will
                        restrict information about the donor to only those staff members with a need to
                        know.<br /><br />
                        The BAKAID will not compensate, whether through commissions, finders fees, or other means, any
                        third
                        party for directing a gift or a donor to the BAKAID.
                    </p>
                </div>

                <div class="flex items-center mt-4">
                    <input type="checkbox" name="terms_accepted" id="terms" class="mr-2">
                    <label for="terms">I accept the terms and conditions</label>
                </div>
                <?php if ($error): ?>
                    <p class="text-red-500 mb-2"><?php echo $error; ?></p>
                <?php endif; ?>
                <div class="flex justify-between mt-4">
                    <button name="back" class="w-1/2 mr-2 bg-gray-500 text-white px-4 py-2 rounded">Back</button>
                    <button type="submit" class="w-1/2 bg-green-500 text-white px-4 py-2 rounded">Next</button>
                </div>
            </form>
        <?php endif; ?>

        <!-- Step 4: Review and Submit -->
        <?php if ($step == 4): ?>
            <form method="POST">
            <div class="bg-gray-100 p-4 border rounded">
                    <p class="text-sm mb-2">To make a donation toward this cause, follow these steps:</p>
                    <p class="text-sm mb-2">
                        Write a cheque payable to <span class='font-semibold'>BAKAID</span>. On the memo line of the
                        cheque,
                        indicate that the donation is for <span class='font-semibold'>BAKAID</span>. Mail your cheque
                        to:
                        <span class="font-semibold">info@bakaid.or.tz</span>.
                    </p>
                    <p class="text-sm mb-2">
                        Or deposit into our bank account at <span class="font-semibold">International Commercial Bank
                            (ICB)</span>:<br />
                        <span class='font-semibold'>A/C#: 20160139407</span> (Account Number)<br />
                        Account Name: <span class='font-semibold'>BAKWATA BAKAIDS</span>.<br />
                        Mail your bank receipt image to: <span class="font-semibold">info@bakaid.or.tz</span>.
                    </p>
                    <p class="text-sm mb-2">
                        Or donate using Tigo Pesa:<br />
                        Mobile Number: <span class="font-semibold">0713608068</span>.<br />
                        Mail your payment screenshot to: <span class="font-semibold">info@bakaid.or.tz</span>.
                    </p>
                    <p class="text-sm">Note: Once we receive your cheque, bank receipt, or SMS screenshot, we will mark
                        it
                        as complete in our system, and an email receipt will be generated for your records. Please
                        contact
                        us with any questions! Your tax-deductible donation is greatly appreciated!</p>
                </div>

                <h2 class="text-xl font-bold my-2">Review Your Donation</h2>

                <div class="bg-gray-100 p-4 border rounded">
                    <p><strong>Amount:</strong> <?php echo number_format($_SESSION['amount']); ?> TZS</p>
                    <p><strong>Full Name:</strong> <?php echo $_SESSION['full_name'] . ' ' . $_SESSION['last_name']; ?></p>
                    <p><strong>Email:</strong> <?php echo $_SESSION['email']; ?></p>
                </div>
                <?php if ($error): ?>
                    <p class="text-red-500 mb-2"><?php echo $error; ?></p>
                <?php endif; ?>
                <div class="flex justify-between mt-4">
                    <button name="back" class="w-1/2 mr-2 bg-gray-500 text-white px-4 py-2 rounded">Back</button>
                    <button type="submit" class="w-1/2 bg-green-500 text-white px-4 py-2 rounded">Submit Donation</button>
                </div>
            </form>
        <?php endif; ?>

    <?php endif; ?>

</div>
</body>
</html>
