<?php
session_start();
require_once "../db/connection.php";

$eroare = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $parola = trim($_POST["parola"]);

    // LOGIN simplu fără hash, ca la colega ta
    $sql = "SELECT * FROM user WHERE email='$email' AND parola='$parola' LIMIT 1";
    $rezultat = $conn->query($sql);

    if ($rezultat && $rezultat->num_rows === 1) {
        $user = $rezultat->fetch_assoc();

        $_SESSION["user_id"] = $user["id_user"];
        $_SESSION["rol"] = $user["rol"];
        $_SESSION["nume"] = $user["nume"];

        header("Location: ../dashboard.php");
        exit();
    } else {
        $eroare = "Email sau parolă greșite!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Autentificare - HotelHub Admin</title>
    <link rel="stylesheet" href="../../css/admin.css">
</head>

<body>

<div class="login-container">
    <h2>Autentificare Admin</h2>

    <?php if ($eroare): ?>
        <div class="error-box"><?= $eroare ?></div>
    <?php endif; ?>

    <form method="POST" class="login-box">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="parola" placeholder="Parola" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>

