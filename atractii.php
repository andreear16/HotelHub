<?php
require_once __DIR__ . '/app/db/connection.php';

$rez = $conn->query("
    SELECT titlu, oras, sursa
    FROM atractii
    ORDER BY titlu
");

function cleanTitle(string $t): string {
    $parts = explode('|', $t);
    return trim($parts[0]);
}

function cleanCity(string $oras, string $titlu): string {
    $oras = trim($oras);
    if ($oras !== '') return $oras;

    $parts = explode('|', $titlu);
    return isset($parts[1]) ? trim($parts[1]) : '';
}

function cleanSource(string $sursa, string $titlu): string {
    $sursa = trim($sursa);
    if ($sursa !== '') return $sursa;

    $parts = explode('|', $titlu);
    return isset($parts[2]) ? trim($parts[2]) : '';
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Atracții turistice</title>
<link rel="stylesheet" href="/proiect/css/admin.css">

<style>
    body {
        background: #eef3f9;
    }
    .atr-wrap {
        max-width: 980px;
        margin: 60px auto;
        padding: 0 16px;
    }
    .atr-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .atr-header {
        padding: 26px 26px 18px 26px;
        background: linear-gradient(135deg, #e8f1ff, #eef7f2);
        border-bottom: 1px solid rgba(0,0,0,0.06);
        text-align: center;
    }
    .atr-header h1 {
        margin: 0;
        font-size: 40px;
        letter-spacing: .3px;
    }
    .atr-header p {
        margin: 10px 0 0 0;
        color: #3b4a5a;
        font-size: 15px;
    }

    .atr-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
    }
    .atr-table thead th {
        text-align: left;
        background: #083b6b;
        color: #fff;
        padding: 14px 16px;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: .2px;
    }
    .atr-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #eef2f7;
        font-size: 15px;
        color: #12263a;
        vertical-align: middle;
    }
    .atr-table tbody tr:hover {
        background: #f6f9ff;
    }
    .badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 13px;
        background: #e7f6ea;
        color: #156a2a;
        border: 1px solid rgba(21,106,42,0.18);
        white-space: nowrap;
    }

    .atr-footer {
        padding: 18px 26px 24px 26px;
        text-align: center;
        background: #fff;
    }
    .back-btn {
        display: inline-block;
        margin-top: 6px;
        padding: 12px 22px;
        border-radius: 12px;
        background: #b30000;
        color: #fff;
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 10px 20px rgba(179,0,0,0.18);
    }
    .back-btn:hover {
        opacity: .92;
    }

    @media (max-width: 720px) {
        .atr-header h1 { font-size: 30px; }
        .atr-table thead { display: none; }
        .atr-table, .atr-table tbody, .atr-table tr, .atr-table td { display: block; width: 100%; }
        .atr-table tr { border-bottom: 1px solid #eef2f7; }
        .atr-table tbody td {
            border: none;
            padding: 10px 16px;
        }
        .atr-table tbody td::before {
            content: attr(data-label);
            display: block;
            font-size: 12px;
            color: #6b7a89;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: .4px;
        }
    }
</style>
</head>
<body>

<div class="atr-wrap">
    <div class="atr-card">

        <div class="atr-header">
            <h1>Atracții turistice</h1>
            <p>Conținut preluat de pe Wikipedia – import offline</p>
        </div>

        <table class="atr-table">
            <thead>
                <tr>
                    <th>Denumire</th>
                    <th>Oraș</th>
                    <th>Sursă</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($rez && $rez->num_rows > 0): ?>
                <?php while ($a = $rez->fetch_assoc()): ?>
                    <?php
                        $titlu = cleanTitle($a['titlu']);
                        $oras  = cleanCity($a['oras'] ?? '', $a['titlu']);
                        $sursa = cleanSource($a['sursa'] ?? '', $a['titlu']);
                        if ($sursa === '') $sursa = 'Wikipedia (import offline)';
                    ?>
                    <tr>
                        <td data-label="Denumire"><strong><?= htmlspecialchars($titlu) ?></strong></td>
                        <td data-label="Oraș"><span class="badge"><?= htmlspecialchars($oras ?: '—') ?></span></td>
                        <td data-label="Sursă"><?= htmlspecialchars($sursa) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="padding:18px 16px; text-align:center; color:#6b7a89;">
                        Nu există atracții încă.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <div class="atr-footer">
            <a class="back-btn" href="/proiect/app/dashboard.php">Înapoi</a>
        </div>

    </div>
</div>

</body>
</html>
