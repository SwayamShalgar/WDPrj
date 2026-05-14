<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

include("../config/db.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">

    <div class="top-bar">
        <h2>Admin Dashboard</h2>
        <div class="top-actions">
            <a href="add_flight.php" class="btn">Add Flight</a>
            <a href="../public/logout.php" class="btn">Logout</a>
        </div>
    </div>

    <h3>All Flights</h3>

    <div class="grid">
    <?php
    $result = $conn->query("SELECT * FROM flights ORDER BY id DESC");

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "
            <div class='card'>
                <h3>{$row['flight_no']}</h3>
                <p><strong>{$row['source']} → {$row['destination']}</strong></p>
                <p>Seats Available: {$row['seats']}</p>
                <p>Price: ₹{$row['price']}</p>

                <a class='btn' href='delete_flight.php?id={$row['id']}'>Delete</a>
            </div>";
        }
    } else {
        echo "<p class='error'>No flights found</p>";
    }
    ?>
    </div>

    <hr>

    <h3>All Bookings</h3>

    <div class="grid">
    <?php
    $stmt = $conn->prepare("
        SELECT b.id, u.name, f.flight_no, f.source, f.destination, b.seats_booked
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN flights f ON b.flight_id = f.id
        ORDER BY b.id DESC
    ");

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "
            <div class='card'>
                <p><strong>User:</strong> {$row['name']}</p>
                <p><strong>Flight:</strong> {$row['flight_no']}</p>
                <p>{$row['source']} → {$row['destination']}</p>
                <p>Seats Booked: {$row['seats_booked']}</p>
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