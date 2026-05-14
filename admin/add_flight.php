<?php
session_start();

// Auth check
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
    <title>Add Flight</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">

    <div class="top-bar">
        <h2>Add Flight</h2>
        <div class="top-actions">
            <a href="dashboard.php" class="btn">Dashboard</a>
            <a href="../public/logout.php" class="btn">Logout</a>
        </div>
    </div>

    <form method="POST">

        <label>Flight No:</label>
        <input type="text" name="flight_no" required>

        <label>Source:</label>
        <input type="text" name="source" required>

        <label>Destination:</label>
        <input type="text" name="destination" required>

        <label>Departure Time:</label>
        <input type="datetime-local" name="departure" required>

        <label>Arrival Time:</label>
        <input type="datetime-local" name="arrival" required>

        <label>Price (₹):</label>
        <input type="number" name="price" required>

        <label>Seats:</label>
        <input type="number" name="seats" required>

        <button type="submit">Add Flight</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $flight_no = $_POST['flight_no'];
        $source = $_POST['source'];
        $destination = $_POST['destination'];
        $departure = $_POST['departure'];
        $arrival = $_POST['arrival'];
        $price = $_POST['price'];
        $seats = $_POST['seats'];

        if ($seats <= 0) {
            echo "<p class='error'>Seats must be greater than 0</p>";
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO flights 
        (flight_no, source, destination, departure_time, arrival_time, price, seats)
        VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssssssi", $flight_no, $source, $destination, $departure, $arrival, $price, $seats);

        if ($stmt->execute()) {
            echo "<p class='success'>Flight added successfully</p>";
        } else {
            echo "<p class='error'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
    ?>

</div>

</body>
</html>