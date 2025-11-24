<?php
session_start();
require_once "../db/connection.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: read.php");
    exit();
}

$id = $_GET["id"];

$sql = "SELECT * FROM rezervari WHERE id_rezervare = $id";
$rez = $conn->query($sql);
$rezervare = $rez->fetch_assoc();

$clienti = $conn->query("SELECT * FROM user WHERE rol='client'");

$angajati = $conn->query("SELECT * FROM user WHERE rol='angajat'");

$camere = $conn->query("SELECT * FROM camere");

$mesaj = "";

// Când se trimite formularul
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $client = $_POST["id_user_client"];
    $angajat = $_POST["id_user_angajat"];
    $camera = $_POST["id_camera"];
    $checkin = $_POST["data_checkin"];
    $checkout = $_POST["data_checkout"];
    $status = $_POST["status_rezervare"];

    $sqlUpdate = "UPDATE rezervari SET 
                    id_user_client = '$client',
                    id_user_angajat = '$angajat',
                    id_camera = '$camera',
                    data_checkin = '$checkin',
                    data_checkout = '$checkout',
                    status_rezervare = '$status'
                  WHERE id_rezervare = $id";

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
    <title>Editare Rezervare</title>
    <link rel="stylesheet" href="../../css/admin.css">
</head>

<body>

<div class="login-container" style="max-width:500px;">
    <h2>Editare rezervare</h2>

    <?php if ($mesaj): ?>
        <div class="error-box"><?= $mesaj ?></div>
    <?php endif; ?>

    <form method="POST" class="login-box">

        <label>Client:</label>
        <select name="id_user_client" required>
            <?php while ($row = $clienti->fetch_assoc()): ?>
                <option 
                    value="<?= $row['id_user'] ?>"
                    <?= ($row['id_user'] == $rezervare['id_user_client']) ? "selected" : "" ?>
                >
                    <?= $row['nume'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Angajat responsabil:</label>
        <select name="id_user_angajat" required>
            <?php while ($row = $angajati->fetch_assoc()): ?>
                <option 
                    value="<?= $row['id_user'] ?>"
                    <?= ($row['id_user'] == $rezervare['id_user_angajat']) ? "selected" : "" ?>
                >
                    <?= $row['nume'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Cameră:</label>
        <select name="id_camera" required>
            <?php while ($row = $camere->fetch_assoc()): ?>
                <option 
                    value="<?= $row['id_camera'] ?>"
                    <?= ($row['id_camera'] == $rezervare['id_camera']) ? "selected" : "" ?>
                >
                    Camera <?= $row['numar_camera'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Data check-in:</label>
        <input type="date" name="data_checkin" value="<?= $rezervare['data_checkin'] ?>" required>

        <label>Data check-out:</label>
        <input type="date" name="data_checkout" value="<?= $rezervare['data_checkout'] ?>" required>

        <label>Status:</label>
        <input type="text" name="status_rezervare" value="<?= $rezervare['status_rezervare'] ?>" required>

        <button type="submit">Salvează modificările</button>
    </form>

    <a class="logout-btn" href="read.php">Înapoi</a>
</div>

</body>
</html>
