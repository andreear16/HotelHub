<?php
session_start();
require_once "../db/connection.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET["id"];

$sql = "SELECT * FROM camere WHERE id_camera = $id";
$rez = $conn->query($sql);
$camera = $rez->fetch_assoc();

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $numar = $_POST["numar_camera"];
    $tip = $_POST["tip_camera"];
    $pret = $_POST["pret_noapte"];
    $disp = $_POST["disponibilitate"];

    $sqlUpdate = "UPDATE camere SET 
                    numar_camera = '$numar',
                    tip_camera = '$tip',
                    pret_noapte = '$pret',
                    disponibilitate = '$disp'
                  WHERE id_camera = $id";

    if ($conn->query($sqlUpdate)) {
        header("Location: read.php");
        exit();
    } else {
        $mesaj = "Eroare la actualizare!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Editare Cameră</title>
    <link rel="stylesheet" href="../../css/admin.css">
</head>

<body>

<div class="login-container" style="max-width:500px;">
    <h2>Editare cameră</h2>

    <?php if ($mesaj): ?>
        <div class="error-box"><?= $mesaj ?></div>
    <?php endif; ?>

    <form method="POST" class="login-box">

        <input type="number" name="numar_camera" value="<?= $camera['numar_camera'] ?>" required>
        <input type="text" name="tip_camera" value="<?= $camera['tip_camera'] ?>" required>
        <input type="number" name="pret_noapte" value="<?= $camera['pret_noapte'] ?>" required>
        <input type="text" name="disponibilitate" value="<?= $camera['disponibilitate'] ?>" required>

        <button type="submit">Salvează modificările</button>
    </form>

    <a class="logout-btn" href="read.php">Înapoi</a>
</div>

</body>
</html>
