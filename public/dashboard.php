<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include("../config/db.php");

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">

    <div class="top-bar">
        <h2>My Bookings</h2>
        <div class="top-actions">
            <a href="search.php" class="btn">Search Flights</a>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </div>

    <?php
    if (isset($_GET['msg']) && $_GET['msg'] === 'booked') {
        echo "<p class='notice'>Booking confirmed. Your ticket is now in this list.</p>";
    }
    ?>

    <hr>


    <div class="grid">

    <?php
    $stmt = $conn->prepare("
        SELECT b.id, f.flight_no, f.source, f.destination, 
               f.departure_time, f.arrival_time, f.price, b.seats_booked
        FROM bookings b
        JOIN flights f ON b.flight_id = f.id
        WHERE b.user_id = ?
        ORDER BY b.id DESC
    ");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {

            $total = $row['price'] * $row['seats_booked'];

            echo "
            <div class='card'>
                <h3>{$row['flight_no']}</h3>
                <p><strong>{$row['source']} → {$row['destination']}</strong></p>

                <p>Departure: {$row['departure_time']}</p>
                <p>Arrival: {$row['arrival_time']}</p>

                <hr>

                <p>Seats Booked: {$row['seats_booked']}</p>
                <p><strong>Total Paid: ₹{$total}</strong></p>
            </div>";
        }

    } else {
        echo "<p class='error'>No bookings found</p>";
    }

    $stmt->close();
    ?>

    </div>

</div>

</body>
</html>