<?php
session_start();
require_once "../db/connection.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nume = $_POST["nume"];
    $email = $_POST["email"];
    $telefon = $_POST["telefon"];
    $rol = $_POST["rol"];
    $parola = $_POST["parola"];

    $sql = "INSERT INTO user (nume, email, telefon, parola, rol)
            VALUES ('$nume', '$email', '$telefon', '$parola', '$rol')";

    if ($conn->query($sql) === TRUE) {
        header("Location: read.php");
        exit();
    } else {
        $mesaj = "Eroare la inserare!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Adaugă Utilizator</title>
    <link rel="stylesheet" href="../../css/admin.css">
</head>

<body>

<div class="login-container" style="max-width:500px;">
    <h2>Adaugă utilizator</h2>

    <?php if ($mesaj): ?>
        <div class="error-box"><?= $mesaj ?></div>
    <?php endif; ?>

    <form method="POST" class="login-box">

        <input type="text" name="nume" placeholder="Nume" required>

        <input type="email" name="email" placeholder="Email" required>

        <input type="text" name="telefon" placeholder="Telefon">

        <input type="text" name="rol" placeholder="Rol (admin/angajat/client)" required>

        <input type="password" name="parola" placeholder="Parola" required>

        <button type="submit">Salvează</button>
    </form>

    <a class="logout-btn" href="read.php">Înapoi</a>
</div>

</body>
</html>
