<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include("../config/db.php"); 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Flights</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">

    <div class="top-bar">
        <h2>Search Flights</h2>
        <div class="top-actions">
            <a href="dashboard.php" class="btn">My Bookings</a>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </div>


    <div class="card">
        <form method="GET" class="search-form">

            <div style="flex:1;">
                <label>Source</label>
                <input type="text" name="source" placeholder="Enter city" required>
            </div>

            <div style="flex:1;">
                <label>Destination</label>
                <input type="text" name="destination" placeholder="Enter city" required>
            </div>

            <div style="align-self:end;">
                <button type="submit">Search</button>
            </div>

        </form>
    </div>

    <br>


    <div class="grid">

    <?php
    if (isset($_GET['source']) && isset($_GET['destination'])) {

        $source = "%" . $_GET['source'] . "%";
        $destination = "%" . $_GET['destination'] . "%";

        $stmt = $conn->prepare("SELECT * FROM flights 
                                WHERE source LIKE ? AND destination LIKE ?");
        $stmt->bind_param("ss", $source, $destination);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {

                echo "
                <div class='card'>
                    <h3>{$row['flight_no']}</h3>

                    <p><strong>{$row['source']} → {$row['destination']}</strong></p>

                    <p>Departure: {$row['departure_time']}</p>
                    <p>Arrival: {$row['arrival_time']}</p>

                    <hr>

                    <p>Seats: {$row['seats']}</p>
                    <p><strong>₹{$row['price']}</strong></p>

                    <a class='btn' href='book.php?id={$row['id']}'>Book Now</a>
                </div>";
            }

        } else {
            echo "<p class='error'>No flights found</p>";
        }

        $stmt->close();
    }
    ?>

    </div>

</div>

</body>
</html>