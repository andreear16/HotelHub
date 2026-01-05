<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin','angajat']);

require_once __DIR__ . '/../db/connection.php';

/*
  import offline din fisier txt de pe wikipedia (am copiat manual)
*/

$path = __DIR__ . '/atractii.txt';

if (!file_exists($path)) {
    die("Fisierul atractii.txt nu exista.");
}

$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$oras  = "Bucuresti";
$sursa = "Wikipedia (import offline)";
$url   = "https://ro.wikipedia.org/wiki/Lista_atrac%C8%9Biilor_turistice_din_Bucure%C8%99ti";

$importate = 0;

foreach ($lines as $titlu) {
    $titlu = trim($titlu);

    if (strlen($titlu) < 3 || strlen($titlu) > 255) {
        continue;
    }

    $hash = md5($titlu . '|' . $oras);

    $stmt = $conn->prepare("
        INSERT IGNORE INTO atractii
        (titlu, sursa_url, sursa, oras, cheie_hash)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssss", $titlu, $url, $sursa, $oras, $hash);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $importate++;
    }
}

echo "Import finalizat. Atracții adăugate: $importate";
