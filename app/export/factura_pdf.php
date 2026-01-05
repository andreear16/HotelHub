<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin', 'angajat', 'client']);

require_once __DIR__ . '/../db/connection.php';
require_once __DIR__ . '/../lib/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$rol = $_SESSION['rol'] ?? '';
$id_user = (int)($_SESSION['user_id'] ?? 0);

$id_factura = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_factura || $id_factura <= 0) {
    http_response_code(400);
    exit("ID factură invalid.");
}

try {
    $sql = "
        SELECT
            f.id_factura,
            f.data_emitere,
            f.total,
            f.metoda_plata,
            r.id_rezervare,
            r.data_checkin,
            r.data_checkout,
            c.nume AS client_nume,
            c.email AS client_email,
            c.telefon AS client_telefon,
            a.nume AS angajat_nume,
            ca.numar_camera,
            ca.tip_camera,
            ca.pret_noapte
        FROM factura f
        JOIN rezervari r ON r.id_rezervare = f.id_rezervare
        JOIN user c ON c.id_user = r.id_user_client
        JOIN user a ON a.id_user = f.id_user_angajat
        JOIN camere ca ON ca.id_camera = r.id_camera
        WHERE f.id_factura = ?
    ";

    if ($rol === 'client') {
        $sql .= " AND r.id_user_client = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_factura, $id_user);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_factura);
    }

    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res ? $res->fetch_assoc() : null;

    if (!$data) {
        http_response_code(403);
        exit("Factura nu există sau nu ai acces la ea.");
    }
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit("Eroare la generarea PDF-ului.");
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$hotelName = "HotelHub";
$today = date('Y-m-d');

$html = '
<!doctype html>
<html lang="ro">
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
    .header { border-bottom: 2px solid #003366; padding-bottom: 10px; margin-bottom: 12px; }
    .title { font-size: 20px; font-weight: bold; color: #003366; }
    .muted { color: #555; }
    .box { border: 1px solid #ddd; padding: 10px; border-radius: 6px; margin-top: 10px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    th { background: #f2f6ff; text-align: left; }
    .right { text-align: right; }
    .total { font-size: 14px; font-weight: bold; }
</style>
</head>
<body>

<div class="header">
  <div class="title">FACTURĂ #' . e($data['id_factura']) . '</div>
  <div class="muted">Emisă la: ' . e($data['data_emitere']) . ' | Generată: ' . e($today) . '</div>
  <div class="muted">' . e($hotelName) . '</div>
</div>

<div class="box">
  <strong>Client</strong><br>
  Nume: ' . e($data['client_nume']) . '<br>
  Email: ' . e($data['client_email']) . '<br>
  Telefon: ' . e($data['client_telefon']) . '<br>
</div>

<div class="box">
  <strong>Rezervare</strong><br>
  ID Rezervare: #' . e($data['id_rezervare']) . '<br>
  Cameră: ' . e($data['numar_camera']) . ' (' . e($data['tip_camera']) . ')<br>
  Check-in: ' . e($data['data_checkin']) . ' &nbsp;&nbsp; Check-out: ' . e($data['data_checkout']) . '<br>
  Angajat: ' . e($data['angajat_nume']) . '<br>
</div>

<table>
  <thead>
    <tr>
      <th>Descriere</th>
      <th class="right">Preț/noapte</th>
      <th class="right">Total</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Cazare cameră ' . e($data['numar_camera']) . ' (' . e($data['tip_camera']) . ')</td>
      <td class="right">' . e($data['pret_noapte']) . ' lei</td>
      <td class="right">' . e($data['total']) . ' lei</td>
    </tr>
  </tbody>
</table>

<div class="box">
  <div>Metodă plată: <strong>' . e($data['metoda_plata']) . '</strong></div>
  <div class="right total">TOTAL: ' . e($data['total']) . ' lei</div>
</div>

</body>
</html>
';

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = "factura_" . (int)$data['id_factura'] . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit;
