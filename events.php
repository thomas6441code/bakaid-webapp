<?php
include './includes/header.php';
include 'db.php'; // Include the database connection

// Fetch events data from the database
$events = [];
$loading = true;
$error = '';

try {
    $sql = "SELECT * FROM event"; // Adjust this query to match your table structure
    $result = $conn->query($sql);
    
    if ($result) {
        if ($result->num_rows > 0) {
            // Fetch all events
            while ($row = $result->fetch_assoc()) {
                $events[] = $row;
            }
        } else {
            $error = "No news or events found.";
        }
    } else {
        $error = "Database query failed: " . $conn->error;
    }
    $loading = false;
} catch (Exception $e) {
    $error = $e->getMessage();
    $loading = false;
}
?>

<header id="header" class="text-center mt-20 bg-gradient-to-r from-blue-500 to-green-500 py-16 text-white">
    <h1 class="text-2xl font-bold mb-4">OUR NEWS & EVENTS</h1>
    <p id="scroll" class="text-lg mx-auto px-5">Join us at our upcoming events and news or explore our recent and past activities to see how we’re making a difference.</p>
</header>

<div class="mx-4">
    <?php if ($loading): ?>
        <div id="loading" class="h-96 flex-1 items-center justify-center">
            <div class="loader">Loading...</div>
        </div>
    <?php elseif ($error): ?>
        <div id="error" class="h-60 flex-1 items-center justify-center">
            <p class="text-gray-400 p-6 text-2xl font-bold"><?php echo $error; ?></p>
        </div>
    <?php else: ?>

        <!-- Upcoming Events Section -->
        <section id="upcoming-events" class="container mx-auto py-12">
            <h2 class="text-2xl font-bold text-center mb-8">UPCOMING NEWS & EVENTS</h2>
            <div id="upcoming-events-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 lg:mx-0 gap-8">
                <?php foreach ($events as $event): ?>
                    <?php if (new DateTime($event['date']) >= new DateTime()): ?>
                        <div class="bg-gray-50 p-4 rounded-lg shadow-lg ">
                            <h3 class="text-2xl font-bold mb-1"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p class="text-gray-600 mb-1"><strong>Date: </strong><?php echo date('F j, Y', strtotime($event['date'])); ?></p>
                            <p class="text-gray-600 mb-1"><strong>Location: </strong><?php echo htmlspecialchars($event['location']); ?></p>
                            <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($event['description']); ?></p>
                            <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($event['moreDescription']); ?></p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Recent Past Events Section -->
        <section id="recent-events" class="container mx-auto py-12">
            <h2 class="text-2xl font-bold text-center mb-8">RECENT NEWS & EVENTS</h2>
            <div id="recent-events-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 lg:mx-0 gap-8">
                <?php foreach ($events as $event): ?>
                    <?php if (new DateTime($event['date']) < new DateTime() && (new DateTime($event['date'])) >= (new DateTime())->modify('-4 months')): ?>
                        <div class="bg-white p-4 rounded-lg shadow-lg">
                            <?php if (!empty($event['mainImage'])): ?>
                                <img src="./public/images/<?php echo htmlspecialchars($event['mainImage']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="w-full md:h-60 h-56 object-cover mb-4 rounded-sm cursor-pointer" />
                            <?php endif; ?>
                            <h3 class="text-2xl font-bold mb-1"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p class="text-gray-600 mb-1"><strong>Date: </strong><?php echo date('F j, Y', strtotime($event['date'])); ?></p>
                            <p class="text-gray-600 mb-1"><strong>Location: </strong><?php echo htmlspecialchars($event['location']); ?></p>
                            <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($event['description']); ?></p>
                            <a href="event_details.php?id=<?php echo $event['id']; ?>" class="bg-blue-100 hover:bg-blue-200 text-gray-900 px-4 py-2 rounded">Learn More</a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Older Past Events Section -->
        <section id="past-events" class="container mx-auto py-12">
            <h2 class="text-2xl font-bold text-center mb-8">PAST NEWS & EVENTS</h2>
            <div id="past-events-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 lg:mx-0 gap-8">
                <?php foreach ($events as $event): ?>
                    <?php if (new DateTime($event['date']) < new DateTime() && (new DateTime($event['date'])) < (new DateTime())->modify('-4 months')): ?>
                        <div class="bg-white p-4 rounded-lg shadow-lg">
                            <?php if (!empty($event['mainImage'])): ?>
                                <img src="./public/images/<?php echo htmlspecialchars($event['mainImage']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="w-full md:h-60 h-56 object-cover mb-4 rounded-sm cursor-pointer" />
                            <?php endif; ?>
                            <h3 class="text-2xl font-bold mb-1"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p class="text-gray-600 mb-1"><strong>Date: </strong><?php echo date('F j, Y', strtotime($event['date'])); ?></p>
                            <p class="text-gray-600 mb-1"><strong>Location: </strong><?php echo htmlspecialchars($event['location']); ?></p>
                            <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($event['description']); ?></p>
                            <a href="event_details.php?id=<?php echo $event['id']; ?>" class="bg-blue-100 hover:bg-blue-200 text-gray-900 px-4 py-2 rounded" onclick="past(<?php echo $event['id']; ?>)">Learn More</a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<script>
    const upcoming = (id) => {
        let idd = parseInt(id);
        const events = <?php echo json_encode($events); ?>;
        console.log(<?php echo json_encode($events); ?>);

        let eventCard = null;
        const upcomingselected = document.getElementById('selectedevent');
        upcomingselected.innerHTML = ''; 
        const selectedEvent = events.find(event => event.id == idd); 
        if (selectedEvent) {
            eventCard = document.createElement('div');
            eventCard.className = "bg-white p-3 container bg-gray-200 mx-auto my-7 rounded-lg shadow-lg";
            eventCard.innerHTML = `
                <p class="mt-6"><strong>Title: </strong>${selectedEvent.title}</p>
                <p class="mt-6"><strong>Location: </strong>${selectedEvent.location}</p>
                <p class="mt-6"><strong>Date: </strong>${new Date(selectedEvent.date).toDateString()}</p>
                <p class="mt-6 text-gray-700">${selectedEvent.description}</p>
                <p class="mt-3 text-gray-700">${selectedEvent.moreDescription || ''}</p>
            `;
            upcomingselected.appendChild(eventCard);

            // Scroll to the upcoming section
            const upcomingSection = document.getElementById('scroll');
            if (upcomingSection) {
                upcomingSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            if (upcomingselected) {
                upcomingselected.classList.remove('hidden');
            } else {
                console.error('Selected event container not found!');
            }

            // Show the selected event details
        } else {
            console.error('Event not found with ID:', id);
        }
    }
    
    
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.title = "BAKAID | EVENTS";
    });
</script>

<?php
include './includes/footer.php';
$conn->close(); 
?>