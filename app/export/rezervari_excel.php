<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

require_once __DIR__ . '/../db/connection.php';

$rol     = $_SESSION['rol'];
$user_id = (int)($_SESSION['user_id'] ?? 0);

try {
    if ($rol === 'client') {
        $stmt = $conn->prepare("
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
            JOIN user a ON a.id_user = r.id_user_angajat
            JOIN camere ca ON ca.id_camera = r.id_camera
            WHERE r.id_user_client = ?
            ORDER BY r.id_rezervare DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $rezultat = $stmt->get_result();
    } else {
        $rezultat = $conn->query("
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
            JOIN user a ON a.id_user = r.id_user_angajat
            JOIN camere ca ON ca.id_camera = r.id_camera
            ORDER BY r.id_rezervare DESC
        ");
    }
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit("Eroare la export.");
}

$filename = "rezervari_" . date("Y-m-d_H-i") . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

function e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

echo "\xEF\xBB\xBF";

?>
<table border="1">
    <tr>
        <th colspan="7" style="font-weight:bold;">
            Export Rezervări - HotelHub | Generat: <?= e(date("Y-m-d H:i")) ?> | Rol: <?= e($rol) ?>
        </th>
    </tr>
    <tr style="font-weight:bold;">
        <th>ID</th>
        <th>Client</th>
        <th>Angajat</th>
        <th>Cameră</th>
        <th>Check-in</th>
        <th>Check-out</th>
        <th>Status</th>
    </tr>

    <?php while ($row = $rezultat->fetch_assoc()): ?>
    <tr>
        <td><?= (int)$row['id_rezervare'] ?></td>
        <td><?= e($row['client_nume']) ?></td>
        <td><?= e($row['angajat_nume']) ?></td>
        <td><?= e($row['numar_camera']) ?></td>
        <td><?= e($row['data_checkin']) ?></td>
        <td><?= e($row['data_checkout']) ?></td>
        <td><?= e($row['status_rezervare']) ?></td>
    </tr>
    <?php endwhile; ?>
</table>
