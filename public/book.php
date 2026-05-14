<?php
include("../config/db.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$flight_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($flight_id <= 0) {
    die("Invalid flight ID");
}

$stmt = $conn->prepare("SELECT * FROM flights WHERE id=?");
$stmt->bind_param("i", $flight_id);
$stmt->execute();
$result = $stmt->get_result();
$flight = $result->fetch_assoc();
$stmt->close();

if (!$flight) {
    die("Flight not found");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $seats_requested = intval($_POST['seats']);

    if ($seats_requested <= 0) {
        $booking_error = "Invalid seat count";
    } else {
        $user_id = $_SESSION['user_id'];

        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("SELECT seats FROM flights WHERE id=? FOR UPDATE");
            $stmt->bind_param("i", $flight_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if (!$row) {
                throw new Exception("Flight not found");
            }

            if ($row['seats'] < $seats_requested) {
                throw new Exception("Not enough seats available");
            }

            $stmt = $conn->prepare("UPDATE flights SET seats = seats - ? WHERE id=?");
            $stmt->bind_param("ii", $seats_requested, $flight_id);
            $stmt->execute();

            $stmt = $conn->prepare("INSERT INTO bookings (user_id, flight_id, seats_booked)
                                    VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $flight_id, $seats_requested);
            $stmt->execute();

            $conn->commit();
            header("Location: dashboard.php?msg=booked");
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            $booking_error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Flight</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">

    <div class="top-bar">
        <h2>Book Flight</h2>
        <div class="top-actions">
            <a href="search.php" class="btn">Back</a>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </div>

    <div class="grid">

        <div class="card">
            <h3><?php echo $flight['flight_no']; ?></h3>
            <p><strong><?php echo $flight['source']; ?> → <?php echo $flight['destination']; ?></strong></p>

            <p>Departure: <?php echo $flight['departure_time']; ?></p>
            <p>Arrival: <?php echo $flight['arrival_time']; ?></p>

            <hr>

            <p>Available Seats: <?php echo $flight['seats']; ?></p>
            <p>Price per seat: ₹<?php echo $flight['price']; ?></p>
        </div>


        <div class="card">
            <h3>Booking Details</h3>

            <form method="POST">
                <label>Number of Seats:</label>
                <input type="number" id="seats" name="seats" min="1" required>

                <p><strong>Total Price: ₹<span id="total">0</span></strong></p>

                <button type="submit">Confirm Booking</button>
            </form>

            <?php
            if (isset($booking_error)) {
                echo "<p class='error'>" . $booking_error . "</p>";
            }
            ?>
        </div>

    </div>

</div>


<script>
const price = <?php echo $flight['price']; ?>;
const seatsInput = document.getElementById('seats');
const totalDisplay = document.getElementById('total');

seatsInput.addEventListener('input', function() {
    const seats = parseInt(this.value) || 0;
    totalDisplay.textContent = seats * price;
});
</script>

</body>
</html>