<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

require_once __DIR__ . '/../db/connection.php';

$flash_success = $_SESSION["flash_success"] ?? "";
$flash_error   = $_SESSION["flash_error"] ?? "";
unset($_SESSION["flash_success"], $_SESSION["flash_error"]);

$rol     = $_SESSION['rol'];
$user_id = (int)$_SESSION['user_id'];

try {
    $baseSql = "
        SELECT
            r.id_rezervare,
            c.nume AS client_nume,
            a.nume AS angajat_nume,
            ca.numar_camera,
            r.data_checkin,
            r.data_checkout,
            r.status_rezervare
        FROM rezervari r
        JOIN user c ON c.id_user = r.id_user_client
        LEFT JOIN user a ON a.id_user = r.id_user_angajat
        JOIN camere ca ON ca.id_camera = r.id_camera
    ";

    if ($rol === 'client') {
        $stmt = $conn->prepare($baseSql . "
            WHERE r.id_user_client = ?
            ORDER BY r.id_rezervare DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $rezultat = $stmt->get_result();
    } else {
        $rezultat = $conn->query($baseSql . "
            ORDER BY r.id_rezervare DESC
        ");
    }
} catch (mysqli_sql_exception $e) {
    $rezultat = false;
    $flash_error = "Eroare la încărcarea rezervărilor.";
}
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

<div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;">
    <?php if ($rol !== 'client'): ?>
        <a href="create.php" class="dash-btn" style="background:#008c3a;">+ Adaugă rezervare</a>
    <?php endif; ?>

    <a href="/proiect/app/export/rezervari_excel.php" class="dash-btn" style="background:#6a1b9a;">
        Export Excel
    </a>
</div>

<?php if ($flash_success): ?>
<div class="error-box" style="background:#e7ffe7; color:#0b5d1e;">
<?= htmlspecialchars($flash_success) ?>
</div>
<?php endif; ?>

<?php if ($flash_error): ?>
<div class="error-box"><?= htmlspecialchars($flash_error) ?></div>
<?php endif; ?>

<table border="1" cellpadding="8" cellspacing="0" style="margin-top:30px; width:100%; background:white;">
<tr style="background:#003366; color:white;">
<th>ID</th>
<th>Client</th>
<th>Angajat</th>
<th>Cameră</th>
<th>Check-in</th>
<th>Check-out</th>
<th>Status</th>
<?php if ($rol !== 'client'): ?><th>Acțiuni</th><?php endif; ?>
</tr>

<?php if ($rezultat): ?>
<?php while ($row = $rezultat->fetch_assoc()): ?>
<tr style="text-align:center;">
<td><?= (int)$row['id_rezervare'] ?></td>
<td><?= htmlspecialchars($row['client_nume']) ?></td>

<td>
    <?php
        $ang = $row['angajat_nume'];
        echo $ang ? htmlspecialchars($ang) : '—';
    ?>
</td>

<td><?= htmlspecialchars($row['numar_camera']) ?></td>
<td><?= htmlspecialchars($row['data_checkin']) ?></td>
<td><?= htmlspecialchars($row['data_checkout']) ?></td>
<td><?= htmlspecialchars($row['status_rezervare']) ?></td>

<?php if ($rol !== 'client'): ?>
<td>
<a class="dash-btn"
   href="update.php?id=<?= (int)$row['id_rezervare'] ?>"
   style="padding:8px 15px; background:#0055aa;">
Editează
</a>

<a class="dash-btn"
   href="delete.php?id=<?= (int)$row['id_rezervare'] ?>&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
   style="padding:8px 15px; background:#b30000;"
   onclick="return confirm('Sigur vrei să ștergi această rezervare?');">
Șterge
</a>
</td>
<?php endif; ?>
</tr>
<?php endwhile; ?>
<?php endif; ?>
</table>

<br><br>
<a class="logout-btn" href="../dashboard.php">Înapoi</a>
</div>

</body>
</html>
