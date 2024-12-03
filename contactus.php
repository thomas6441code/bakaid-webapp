<?php
include './includes/header.php';
include 'db.php'; // Include the database connection

// Initialize variables
$fullName = '';
$email = '';
$message = '';
$successMessage = '';
$errorMessage = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Validate input
    if (empty($fullName) || empty($email) || empty($message)) {
        $errorMessage = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    } else {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO message (fullName, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fullName, $email, $message);

        // Execute the statement
        if ($stmt->execute()) {
            $successMessage = "Message sent successfully!";
            // Clear the form fields
            $fullName = $email = $message = '';
        } else {
            $errorMessage = "Failed to send message. Please try again.";
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the connection
$conn->close();
?>

    <div class="container px-6 p-6 pt-0 mt-20 mx-auto">
        <h1 class="text-2xl font-bold text-center py-5 mb-2">OUR LOCATION</h1>
        <div class="w-full flex justify-center items-center px-6 lg:px-6 pb-0 lg:pb-10">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.9414528117954!2d39.22817691076706!3d-6.776980693191655!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x185c4ff47527c2bd%3A0x3b8a863dbd988848!2sTalee%20Street!5e0!3m2!1sen!2stz!4v1729334985765!5m2!1sen!2stz"
                class="border-0 w-full h-60 md:h-80 lg:h-96 mb-5 shadow-md" loading="lazy">
            </iframe>
        </div>

        <div class="container text-[0.9rem] md:text-xl mx-auto px-3 md:px-8">
            <h1 class="text-2xl font-bold text-center">GET IN TOUCH WITH US</h1>
            <div class="flex flex-col lg:flex-row justify-between items -center text-sm py-10">
                <div class="flex-col justify-between gap-10 w-full lg:w-1/3 lg:mb-0">
                    <!-- Contact Information -->
                    <div class="bg-white p-6 rounded-lg shadow-lg text-center flex-1">
                        <i class="fas fa-phone text-blue-500 mb-4 fa-2x"></i>
                        <h3 class="font-semibold mb-2">Phone</h3>
                        <p class="text-gray-600">+255 713 608 068</p>
                    </div>
                    <div class="bg-white p-6 my-4 rounded-lg shadow-lg text-center flex-1">
                        <i class="fas fa-envelope text-blue-500 mb-4 fa-2x"></i>
                        <h3 class="font-semibold mb-2">Email</h3>
                        <p class="text-gray-600">info@bakaid.or.tz</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-lg text-center flex-1">
                        <i class="fas fa-map-marker-alt text-blue-500 mb-4 fa-2x"></i>
                        <h3 class="font-semibold mb-2">Address</h3>
                        <p class="text-gray-600">Kijitonyama Mpakani B, Block 47, Plot 209 Tale Street</p>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="bg-white mt-5 md:mt-0 p-8 px-8 rounded-lg lg:w-2/4 w-full shadow-lg">
                    <h1 class="text-xl text-center font-bold mb-6">CONTACT US</h1>

                    <form id="contactForm" method="POST" class="space-y-4">
                        <div>
                            <label for="fullName" class="block text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="fullName" id="fullName" placeholder="John Doe" required
                                class="w-full p-1 px-2 lg:p-3 md:p-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300" value="<?php echo htmlspecialchars($fullName); ?>" />
                            <p class="text-red-500 text-sm"><?php echo $errorMessage; ?></p>
                        </div>

                        <div>
                            <label for="email" class="block text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="email" placeholder="Johndoe@example.com" required
                                class="w-full p-1 px-2 lg:p-3 md:p-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300" value="<?php echo htmlspecialchars($email); ?>" />
                            <p class="text-red-500 text-sm"><?php echo $errorMessage; ?></p>
                        </div>

                        <div>
                            <label for="message" class="block text-gray-700 mb-1">Message</label>
                            <textarea name="message" id="message" placeholder="Your message goes here!" required
                                rows="4"
                                class="w-full p-1 px-2 lg:p-3 md:p-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"><?php echo htmlspecialchars($message); ?></textarea>
                            <p class="text-red-500 text-sm"><?php echo $errorMessage; ?></p>
                        </div>

                        <?php if ($successMessage): ?>
                            <p class="mt-2 text-green-500"><?php echo $successMessage; ?></p>
                        <?php endif; ?>

                        <div class="text-right">
                            <button type="submit" id="submit-button"
                                class="px-4 py-2 font-normal text-white bg-blue-500 rounded-lg focus:outline-none focus:ring hover:bg-blue-600">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Social Media Icons -->
            <div class="text-center my-6">
                <h2 class="text-xl font-bold mb-6">CONNECT WITH US</h2>
                <div class="flex justify-center space-x-6 text-blue-600">
                    <a href="#" aria-label="Facebook" class="hover:text-blue-700 text-3xl"><i class="fab fa-facebook"></i></a>
                    <a href="#" aria-label="Twitter" class="hover:fill-blue-700 text-3xl">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="h-8 w-8 text-blue-600 fill-current">
                            <path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="LinkedIn" class="hover:text-blue-700 text-3xl"><i class="fab fa-linkedin"></i></a>
                    <a href="#" aria-label="Instagram" class="hover:text-blue-700 text-3xl"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube" class="hover:text-blue-700 text-3xl"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.title = "BAKAID | CONTACT US"; // Change this title for each page
        });
    </script>
<?php
include './includes/footer.php';
?>