<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$rol = $_SESSION['rol'] ?? '';
$user_id = (int)($_SESSION['user_id'] ?? 0);

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

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=rezervari_" . date("Y-m-d_H-i") . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "\xEF\xBB\xBF";

echo "<table border='1'>";
echo "<tr>";
echo "<th>ID</th>";
echo "<th>Client</th>";
echo "<th>Angajat</th>";
echo "<th>Cameră</th>";
echo "<th>Check-in</th>";
echo "<th>Check-out</th>";
echo "<th>Status</th>";
echo "</tr>";

while ($row = $rezultat->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['id_rezervare']) . "</td>";
    echo "<td>" . htmlspecialchars($row['client_nume']) . "</td>";
    echo "<td>" . htmlspecialchars($row['angajat_nume']) . "</td>";
    echo "<td>" . htmlspecialchars($row['numar_camera']) . "</td>";
    echo "<td>" . htmlspecialchars($row['data_checkin']) . "</td>";
    echo "<td>" . htmlspecialchars($row['data_checkout']) . "</td>";
    echo "<td>" . htmlspecialchars($row['status_rezervare']) . "</td>";
    echo "</tr>";
}

echo "</table>";
exit;
