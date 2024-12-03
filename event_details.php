<?php
include './includes/header.php';
include 'db.php'; 

// Get the event ID from the URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch event details from the database
$sql = "SELECT * FROM event WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    echo "<div class='text-red-500'>Event not found.</div>";
    exit;
}

// Close the database connection
$stmt->close();
$conn->close();
?>

    <div class="max-w-6xl p-6 mt-20 mx-auto">
        <div id="event-details" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div>
                <h1 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($event['title']); ?></h1>
                <p class="text-lg text-gray-700 mb-4"><strong>Location: </strong><?php echo htmlspecialchars($event['location']); ?></p>
                <p class="text-lg text-gray-700 mb-4"><strong>Date: </strong><?php echo date('F j, Y', strtotime($event['date'])); ?></p>
                <p class="text-lg text-gray-700 mb-8"><strong>Event Type: </strong><?php echo htmlspecialchars($event['eventType']); ?></p>
            </div>
            <div>
                <img src="<?php echo './public/images/' . htmlspecialchars($event['mainImage']); ?>" alt="Main Event"
                    class="object-cover w-full h-64 rounded-md shadow-lg" />
            </div>
        </div>

        <div id="description" class="bg-white p-3 rounded-lg shadow-md mt-8">
            <h2 class="text-2xl font-semibold mb-4">Description</h2>
            <p class="text-gray-700 mb-4"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
            <div><?php echo nl2br(htmlspecialchars($event['moreDescription'])); ?></div>
        </div>

        <div id="more-images" class="mt-8">
            <h3 class="text-xl font-semibold mb-4">More Images</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <?php if (!empty($event["image$i"])): ?>
                        <img src="<?php echo './public/images/' . htmlspecialchars($event["image$i"]); ?>" alt="Event Image <?php echo $i; ?>"
                             class="object-cover w-full h-48 rounded-md shadow-md" />
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </div>

        <div class="mt-8">
            <a href="events.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-300">Back to Events</a>
        </div>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.title = "BAKAID | EVENTS";
    });
</script>

 
<?php
include './includes/footer.php';
?>