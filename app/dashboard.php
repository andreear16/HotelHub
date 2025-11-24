<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - HotelHub</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>

<div class="dashboard-container">
    <h1>Bun venit, <?= $_SESSION["nume"] ?>!</h1>
    <p>Selectează ce vrei să administrezi:</p>

    <div class="dash-grid">
        <a href="users/read.php" class="dash-btn">👤 Utilizatori</a>
        <a href="camere/read.php" class="dash-btn">🏨 Camere</a>
        <a href="rezervari/read.php" class="dash-btn">📅 Rezervări</a>
    </div>

    <a class="logout-btn" href="auth/logout.php">Logout</a>
</div>

</body>
</html>

