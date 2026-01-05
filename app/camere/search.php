<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['client','admin','angajat']);

require_once __DIR__ . '/../db/connection.php';

$rezultat = null;
$checkin_value = '';
$checkout_value = '';
$mesaj = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $checkin_value  = $_POST['checkin'] ?? '';
    $checkout_value = $_POST['checkout'] ?? '';

    if ($checkin_value === '' || $checkout_value === '') {
        $mesaj = "Selectează ambele date.";
    } elseif (strtotime($checkout_value) <= strtotime($checkin_value)) {
        $mesaj = "Check-out trebuie să fie după check-in.";
    } else {
        $stmt = $conn->prepare("
            SELECT c.numar_camera, c.tip_camera, c.pret_noapte
            FROM camere c
            WHERE c.disponibilitate = 1
            AND NOT EXISTS (
                SELECT 1 FROM rezervari r
                WHERE r.id_camera = c.id_camera
                  AND r.status_rezervare <> 'anulata'
                  AND NOT (r.data_checkout <= ? OR r.data_checkin >= ?)
            )
            ORDER BY c.numar_camera
        ");
        $stmt->bind_param("ss", $checkin_value, $checkout_value);
        $stmt->execute();
        $rezultat = $stmt->get_result();
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Caută camere</title>
    <link rel="stylesheet" href="../../css/admin.css">
</head>
<body>

<div class="dashboard-container">
    <h1>Caută camere disponibile</h1>

    <?php if ($mesaj): ?>
        <div class="error-box"><?= htmlspecialchars($mesaj) ?></div>
    <?php endif; ?>

    <form method="POST" class="login-box" style="max-width:400px; margin:0 auto;">
        <label>Data check-in:</label>
        <input type="date" name="checkin" required value="<?= htmlspecialchars($checkin_value) ?>">

        <label>Data check-out:</label>
        <input type="date" name="checkout" required value="<?= htmlspecialchars($checkout_value) ?>">

        <button type="submit">Caută camere</button>
    </form>

    <?php if ($rezultat): ?>
        <h2 style="margin-top:40px;">Camere disponibile</h2>

        <?php if ($rezultat->num_rows === 0): ?>
            <p>Nu există camere disponibile în perioada selectată.</p>
        <?php else: ?>
            <table border="1" cellpadding="8" cellspacing="0"
                   style="margin-top:20px; width:100%; background:white;">
                <tr style="background:#003366; color:white;">
                    <th>Număr cameră</th>
                    <th>Tip</th>
                    <th>Preț / noapte</th>
                </tr>

                <?php while ($c = $rezultat->fetch_assoc()): ?>
                    <tr style="text-align:center;">
                        <td><?= (int)$c['numar_camera'] ?></td>
                        <td><?= htmlspecialchars($c['tip_camera']) ?></td>
                        <td><?= htmlspecialchars($c['pret_noapte']) ?> lei</td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>
    <?php endif; ?>

    <div style="display:flex; justify-content:center; margin-top:24px;">
        <a class="logout-btn" href="../dashboard.php" style="position:static; float:none; margin:0;">
            Înapoi
        </a>
    </div>
</div>

</body>
</html>
