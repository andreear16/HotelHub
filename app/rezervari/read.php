<?php
session_start();
require_once "../db/connection.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$sql = "SELECT r.*, 
       c.numar_camera,
       u1.nume AS client_nume,
       u2.nume AS angajat_nume
FROM rezervari r
LEFT JOIN camere c ON r.id_camera = c.id_camera
LEFT JOIN user u1 ON r.id_user_client = u1.id_user
LEFT JOIN user u2 ON r.id_user_angajat = u2.id_user";

$rez = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Rezervări</title>
    <link rel="stylesheet" href="../../css/admin.css">
</head>

<body>

<div class="dashboard-container">
    <h1>Rezervări</h1>
    <a href="create.php" class="dash-btn" style="background:#008c3a;">+ Adaugă rezervare</a>

    <table border="1" cellpadding="8" cellspacing="0" style="margin-top:30px; width:100%; background:white;">
        <tr style="background:#003366; color:white;">
            <th>ID</th>
            <th>Client</th>
            <th>Angajat</th>
            <th>Cameră</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Status</th>
            <th>Acțiuni</th>
        </tr>

        <?php while ($row = $rez->fetch_assoc()): ?>
            <tr style="text-align:center;">
                <td><?= $row["id_rezervare"] ?></td>
                <td><?= $row["client_nume"] ?></td>
                <td><?= $row["angajat_nume"] ?></td>
                <td><?= $row["numar_camera"] ?></td>
                <td><?= $row["data_checkin"] ?></td>
                <td><?= $row["data_checkout"] ?></td>
                <td><?= $row["status_rezervare"] ?></td>

                <td>
                    <a class="dash-btn" style="background:#0055aa; padding:8px;" 
                        href="update.php?id=<?= $row['id_rezervare'] ?>">
                        Editează
                    </a>

                    <a class="dash-btn" style="background:#b30000; padding:8px;"
                        href="delete.php?id=<?= $row['id_rezervare'] ?>"
                        onclick="return confirm('Sigur vrei să ștergi această rezervare?');">
                        Șterge
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <br><br>
    <a class="logout-btn" href="../dashboard.php">Înapoi</a>
</div>

</body>
</html>
