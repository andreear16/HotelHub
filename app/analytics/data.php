<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin','angajat']);

require_once __DIR__ . '/../db/connection.php';

header('Content-Type: application/json; charset=utf-8');

$days = 30;

$views = $conn->query("
  SELECT DATE(created_at) AS d, COUNT(*) AS c
  FROM page_views
  WHERE created_at >= (CURDATE() - INTERVAL $days DAY)
  GROUP BY DATE(created_at)
  ORDER BY d
");

$uniques = $conn->query("
  SELECT DATE(created_at) AS d,
         COUNT(DISTINCT CONCAT(IFNULL(ip,''), '|', IFNULL(user_agent,''))) AS c
  FROM page_views
  WHERE created_at >= (CURDATE() - INTERVAL $days DAY)
  GROUP BY DATE(created_at)
  ORDER BY d
");

$topPages = $conn->query("
  SELECT path, COUNT(*) AS c
  FROM page_views
  WHERE created_at >= (CURDATE() - INTERVAL $days DAY)
  GROUP BY path
  ORDER BY c DESC
  LIMIT 10
");

$roles = $conn->query("
  SELECT IFNULL(role, 'guest') AS r, COUNT(*) AS c
  FROM page_views
  WHERE created_at >= (CURDATE() - INTERVAL $days DAY)
  GROUP BY IFNULL(role, 'guest')
  ORDER BY c DESC
");

function fillDays(array $map, int $days): array {
    $labels = [];
    $values = [];

    for ($i = $days; $i >= 0; $i--) {
        $date = (new DateTime())->modify("-$i day")->format('Y-m-d');
        $labels[] = $date;
        $values[] = (int)($map[$date] ?? 0);
    }

    return ['labels' => $labels, 'values' => $values];
}

$mapViews = [];
while ($row = $views->fetch_assoc()) {
    $mapViews[$row['d']] = (int)$row['c'];
}

$mapUniques = [];
while ($row = $uniques->fetch_assoc()) {
    $mapUniques[$row['d']] = (int)$row['c'];
}

$outTop = [];
while ($row = $topPages->fetch_assoc()) {
    $outTop[] = ['path' => $row['path'], 'views' => (int)$row['c']];
}

$roleLabels = [];
$roleValues = [];
while ($row = $roles->fetch_assoc()) {
    $roleLabels[] = $row['r'];
    $roleValues[] = (int)$row['c'];
}

echo json_encode([
    'views' => fillDays($mapViews, $days),
    'uniques' => fillDays($mapUniques, $days),
    'top_pages' => $outTop,
    'roles' => [
        'labels' => $roleLabels,
        'values' => $roleValues
    ]
], JSON_UNESCAPED_UNICODE);
