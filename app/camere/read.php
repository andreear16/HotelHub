<?php
session_start();
require_once "../db/connection.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$sql = "SELECT * FROM camere";
$rezultat = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Lista Camerelor</title>
    <link rel="stylesheet" href="../../css/admin.css">
</head>

<body>

<div class="dashboard-container">
    <h1>Camere</h1>
    <a href="create.php" class="dash-btn" style="background:#008c3a;">+ Adaugă cameră</a>

    <table border="1" cellpadding="8" cellspacing="0" style="margin-top:30px; width:100%; background:white;">
        <tr style="background:#003366; color:white;">
            <th>ID</th>
            <th>Număr Cameră</th>
            <th>Tip</th>
            <th>Preț/Noapte</th>
            <th>Disponibilitate</th>
            <th>Acțiuni</th>
        </tr>

        <?php while ($row = $rezultat->fetch_assoc()): ?>
            <tr style="text-align:center;">
                <td><?= $row["id_camera"] ?></td>
                <td><?= $row["numar_camera"] ?></td>
                <td><?= $row["tip_camera"] ?></td>
                <td><?= $row["pret_noapte"] ?> lei</td>
                <td><?= $row["disponibilitate"] ?></td>

                <td>
                    <a class="dash-btn" href="update.php?id=<?= $row['id_camera'] ?>" 
                       style="padding:8px 15px; background:#0055aa;">
                       Editează
                    </a>

                    <a class="dash-btn" href="delete.php?id=<?= $row['id_camera'] ?>" 
                       style="padding:8px 15px; background:#b30000;"
                       onclick="return confirm('Sigur vrei să ștergi această cameră?');">
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
