<?php
session_start();
require_once "../db/connection.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$mereu = "";

$clienti = $conn->query("SELECT * FROM user WHERE rol='client'");

$angajati = $conn->query("SELECT * FROM user WHERE rol='angajat'");

$camere = $conn->query("SELECT * FROM camere");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $client = $_POST["id_user_client"];
    $angajat = $_POST["id_user_angajat"];
    $camera = $_POST["id_camera"];
    $checkin = $_POST["data_checkin"];
    $checkout = $_POST["data_checkout"];
    $status = $_POST["status_rezervare"];

    $sql = "INSERT INTO rezervari (id_user_client, id_user_angajat, id_camera, data_checkin, data_checkout, status_rezervare)
            VALUES ('$client', '$angajat', '$camera', '$checkin', '$checkout', '$status')";

    if ($conn->query($sql)) {
        header("Location: read.php");
        exit();
    } else {
        $mereu = "Eroare la inserare!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Adaugă Rezervare</title>
    <link rel="stylesheet" href="../../css/admin.css">
</head>

<body>

<div class="login-container" style="max-width:500px;">
    <h2>Adaugă rezervare</h2>

    <?php if ($mereu): ?>
        <div class="error-box"><?= $mereu ?></div>
    <?php endif; ?>

    <form method="POST" class="login-box">

        <label>Client:</label>
        <select name="id_user_client" required>
            <?php while ($row = $clienti->fetch_assoc()): ?>
                <option value="<?= $row['id_user'] ?>"><?= $row['nume'] ?></option>
            <?php endwhile; ?>
        </select>

        <label>Angajat responsabil:</label>
        <select name="id_user_angajat" required>
            <?php while ($row = $angajati->fetch_assoc()): ?>
                <option value="<?= $row['id_user'] ?>"><?= $row['nume'] ?></option>
            <?php endwhile; ?>
        </select>

        <label>Cameră:</label>
        <select name="id_camera" required>
            <?php while ($row = $camere->fetch_assoc()): ?>
                <option value="<?= $row['id_camera'] ?>">Camera <?= $row['numar_camera'] ?></option>
            <?php endwhile; ?>
        </select>

        <input type="date" name="data_checkin" required>
        <input type="date" name="data_checkout" required>
        <input type="text" name="status_rezervare" placeholder="confirmată / anulată / în curs" required>

        <button type="submit">Salvează</button>
    </form>

    <a class="logout-btn" href="read.php">Înapoi</a>
</div>

</body>
</html>
