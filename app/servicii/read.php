<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);

require_once __DIR__ . '/../db/connection.php';

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error   = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$servicii = $conn->query(
    "SELECT id_serviciu, denumire, pret FROM serviciu ORDER BY denumire"
);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Servicii</title>
    <link rel="stylesheet" href="../../css/admin.css">
</head>
<body>

<div class="dashboard-container">
    <h1>Servicii</h1>

    <a href="create.php" class="dash-btn" style="background:#008c3a;">
        + Adaugă serviciu
    </a>

    <?php if ($flash_success): ?>
        <div class="error-box" style="background:#e7ffe7; color:#0b5d1e;">
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
            <th>Denumire</th>
            <th>Preț</th>
            <th>Acțiuni</th>
        </tr>

        <?php while ($s = $servicii->fetch_assoc()): ?>
            <tr style="text-align:center;">
                <td><?= htmlspecialchars($s['denumire']) ?></td>
                <td><?= number_format((float)$s['pret'], 2) ?> lei</td>
                <td>
                    <a class="dash-btn"
                       href="update.php?id=<?= (int)$s['id_serviciu'] ?>"
                       style="padding:8px 15px; background:#0055aa;">
                        Editează
                    </a>

                    <form method="POST"
                          action="delete.php"
                          style="display:inline;">
                        <input type="hidden" name="id" value="<?= (int)$s['id_serviciu'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button class="dash-btn"
                                style="padding:8px 15px; background:#b30000;"
                                onclick="return confirm('Ștergi serviciul?');">
                            Șterge
                        </button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <br><br>
    <a class="logout-btn" href="../dashboard.php">Înapoi</a>
</div>

</body>
</html>
