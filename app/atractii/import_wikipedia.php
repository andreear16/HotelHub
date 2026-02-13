<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin','angajat']);

require_once __DIR__ . '/../includes/external_fetch.php';

$pageTitle = "Lista parcurilor din București";
$oras = "Bucuresti";
$sursa = "Wikipedia (import live, parsare)";
$sursaUrl = "https://ro.wikipedia.org/wiki/Lista_parcurilor_din_Bucure%C8%99ti";

$importate = 0;
$existente = 0;
$erori = [];

try {
    $html = wikipedia_parse_html($conn, $pageTitle, 86400);
    $items = wiki_extract_list_items($html, 80);

    $stmt = $conn->prepare("
        INSERT IGNORE INTO atractii
        (titlu, sursa_url, sursa, oras, cheie_hash)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($items as $titlu) {
        $titlu = trim($titlu);
        if (mb_strlen($titlu) < 3) continue;

        $hash = md5($titlu . '|' . $oras);

        $stmt->bind_param("sssss", $titlu, $sursaUrl, $sursa, $oras, $hash);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $importate++;
        } else {
            $existente++;
        }
    }
} catch (Throwable $e) {
    $erori[] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Import Parcuri - Wikipedia</title>
<link rel="stylesheet" href="/proiect/css/admin.css">
</head>
<body>
<div class="dashboard-container">
    <h1>Import Parcuri din Wikipedia</h1>
    <p><strong>Importate:</strong> <?= (int)$importate ?></p>
    <p><strong>Existente:</strong> <?= (int)$existente ?></p>

    <?php if (!empty($erori)): ?>
        <div style="background:#ffecec;border:1px solid #ffb3b3;padding:12px;border-radius:10px;">
            <strong>Eroare:</strong> <?= htmlspecialchars($erori[0]) ?>
        </div>
    <?php endif; ?>

    <br>
    <a class="logout-btn" href="/proiect/atractii.php">Vezi atracțiile</a>
    <a class="logout-btn" href="/proiect/app/dashboard.php">Înapoi</a>
</div>
</body>
</html>
