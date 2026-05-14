<?php
session_start();
include("../config/db.php");

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$register_error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['role'] = 'user';
        header("Location: dashboard.php");
        exit;
    } else {
        $register_error = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-shell">

    <div class="auth-card">

        <h2>Create Account</h2>

        <form method="POST">

            <label>Name:</label>
            <input type="text" name="name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Register</button>

        </form>

        <p class="auth-link">
            Already have an account? 
            <a href="login.php">Login</a>
        </p>

        <?php
        if ($register_error) {
            echo "<p class='error'>" . $register_error . "</p>";
        }
        ?>

    </div>

</div>

</body>
</html>