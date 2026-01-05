<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin','angajat']);

require_once __DIR__ . '/../db/connection.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $days = 30;
    $labels = [];
    $map = [];

    for ($i = $days - 1; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        $labels[] = $d;
        $map[$d] = 0;
    }

    $stmt = $conn->prepare("
        SELECT DATE(data_checkin) AS zi, COUNT(*) AS cnt
        FROM rezervari
        WHERE data_checkin >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
        GROUP BY DATE(data_checkin)
        ORDER BY zi
    ");
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $zi = $row['zi'];
        if (isset($map[$zi])) {
            $map[$zi] = (int)$row['cnt'];
        }
    }

    $rezervariValues = array_values($map);

    $monthsLabels = [];
    $monthsMap = [];

    for ($i = 5; $i >= 0; $i--) {
        $m = date('Y-m', strtotime("first day of -$i month"));
        $monthsLabels[] = $m;
        $monthsMap[$m] = 0.0;
    }

    $stmt2 = $conn->prepare("
        SELECT DATE_FORMAT(data_emitere, '%Y-%m') AS luna, SUM(total) AS suma
        FROM factura
        WHERE data_emitere >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
        GROUP BY DATE_FORMAT(data_emitere, '%Y-%m')
        ORDER BY luna
    ");
    $stmt2->execute();
    $res2 = $stmt2->get_result();

    while ($row = $res2->fetch_assoc()) {
        $luna = $row['luna'];
        if (isset($monthsMap[$luna])) {
            $monthsMap[$luna] = (float)$row['suma'];
        }
    }

    $incasariValues = array_values($monthsMap);

    echo json_encode([
        "rezervari" => [
            "labels" => $labels,
            "values" => $rezervariValues
        ],
        "incasari" => [
            "labels" => $monthsLabels,
            "values" => $incasariValues
        ]
    ]);
    exit;

} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Eroare la statistici."]);
    exit;
}
