<?php
session_start();
require_once "../db/connection.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $numar = $_POST["numar_camera"];
    $tip = $_POST["tip_camera"];
    $pret = $_POST["pret_noapte"];
    $disp = $_POST["disponibilitate"];

    $sql = "INSERT INTO camere (numar_camera, tip_camera, pret_noapte, disponibilitate)
            VALUES ('$numar', '$tip', '$pret', '$disp')";

    if ($conn->query($sql)) {
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
    <title>Adaugă Cameră</title>
    <link rel="stylesheet" href="../../css/admin.css">
</head>

<body>

<div class="login-container" style="max-width:500px;">
    <h2>Adaugă cameră</h2>

    <?php if ($mesaj): ?>
        <div class="error-box"><?= $mesaj ?></div>
    <?php endif; ?>

    <form method="POST" class="login-box">

        <input type="number" name="numar_camera" placeholder="Număr cameră" required>
        <input type="text" name="tip_camera" placeholder="Tip (Single, Double, Deluxe)" required>
        <input type="number" name="pret_noapte" placeholder="Preț/noapte" required>

        <input type="text" name="disponibilitate" placeholder="liber / ocupat" required>

        <button type="submit">Salvează</button>
    </form>

    <a class="logout-btn" href="read.php">Înapoi</a>
</div>

</body>
</html>
