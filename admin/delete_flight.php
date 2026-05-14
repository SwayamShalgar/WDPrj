<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

include("../config/db.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT id FROM flights WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Flight not found");
}
$stmt->close();

$stmt = $conn->prepare("DELETE FROM flights WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: dashboard.php?msg=deleted");
} else {
    header("Location: dashboard.php?msg=error");
}

$stmt->close();
exit;