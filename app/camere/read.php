<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin', 'angajat']);

require_once __DIR__ . '/../db/connection.php';

$flash_success = $_SESSION["flash_success"] ?? "";
$flash_error   = $_SESSION["flash_error"] ?? "";
unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

try {
    $rezultat = $conn->query("
        SELECT id_camera, numar_camera, tip_camera, pret_noapte, disponibilitate
        FROM camere
        ORDER BY numar_camera
    ");
} catch (mysqli_sql_exception $e) {
    error_log("Read camere error: " . $e->getMessage());
    $rezultat = false;
    $flash_error = "Eroare la încărcarea camerelor.";
}
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

    <a href="create.php" class="dash-btn" style="background:#008c3a;">
        + Adaugă cameră
    </a>

    <?php if ($flash_success): ?>
        <div class="error-box"
             style="background:#e7ffe7; color:#0b5d1e; border:1px solid #0b5d1e;">
            <?= htmlspecialchars($flash_success) ?>
        </div>
    <?php endif; ?>

    <?php if ($flash_error): ?>
        <div class="error-box">
            <?= htmlspecialchars($flash_error) ?>
        </div>
    <?php endif; ?>

    <table border="1" cellpadding="8" cellspacing="0"
           style="margin-top:30px; width:100%; background:white;">
        <tr style="background:#003366; color:white;">
            <th>ID</th>
            <th>Număr Cameră</th>
            <th>Tip</th>
            <th>Preț / Noapte</th>
            <th>Disponibilă</th>
            <th>Acțiuni</th>
        </tr>

        <?php if ($rezultat): ?>
            <?php while ($row = $rezultat->fetch_assoc()): ?>
                <tr style="text-align:center;">
                    <td><?= (int)$row["id_camera"] ?></td>
                    <td><?= (int)$row["numar_camera"] ?></td>
                    <td><?= htmlspecialchars($row["tip_camera"]) ?></td>
                    <td><?= htmlspecialchars($row["pret_noapte"]) ?> lei</td>
                    <td><?= ((int)$row["disponibilitate"] === 1) ? "Da" : "Nu" ?></td>
                    <td>

                        <a class="dash-btn"
                           href="update.php?id=<?= (int)$row["id_camera"] ?>"
                           style="padding:8px 15px; background:#0055aa;">
                           Editează
                        </a>

                        <form method="POST"
                              action="delete.php"
                              style="display:inline;"
                              onsubmit="return confirm('Sigur vrei să ștergi această cameră?');">

                            <input type="hidden" name="id"
                                   value="<?= (int)$row["id_camera"] ?>">

                            <input type="hidden" name="csrf_token"
                                   value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                            <button type="submit"
                                    class="dash-btn"
                                    style="padding:8px 15px; background:#b30000;">
                                Șterge
                            </button>
                        </form>

                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </table>

    <br><br>
    <a class="logout-btn" href="../dashboard.php">Înapoi</a>
</div>

</body>
</html>
