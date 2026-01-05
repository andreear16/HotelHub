<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin', 'angajat', 'client']);

require_once __DIR__ . '/../db/connection.php';

$rol = $_SESSION['rol'];
$id_user_logat = (int)($_SESSION['user_id'] ?? 0);

function valid_date(string $d): bool {
    $dt = DateTime::createFromFormat("Y-m-d", $d);
    return $dt && $dt->format("Y-m-d") === $d;
}

$mesaj = "";

$client_value   = "";
$angajat_value  = "";
$camera_value   = "";
$checkin_value  = "";
$checkout_value = "";
$status_value   = "confirmata";

$status_ok = ["confirmata", "anulata", "in_curs", "in_asteptare"];

try {
    if ($rol !== 'client') {
        $clienti  = $conn->query("SELECT id_user, nume FROM user WHERE rol='client' ORDER BY nume");
        $angajati = $conn->query("SELECT id_user, nume FROM user WHERE rol='angajat' ORDER BY nume");
    }
    $camere = $conn->query("SELECT id_camera, numar_camera FROM camere ORDER BY numar_camera");
} catch (mysqli_sql_exception $e) {
    $mesaj = "Eroare la încărcarea datelor.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("Cerere invalidă.");
    }

    $camera_value   = trim($_POST["id_camera"] ?? "");
    $checkin_value  = trim($_POST["data_checkin"] ?? "");
    $checkout_value = trim($_POST["data_checkout"] ?? "");

    $camera = filter_var($camera_value, FILTER_VALIDATE_INT);

    if ($rol === 'client') {
        $client = $id_user_logat;
        $angajat = null;
        $status = "in_asteptare";
    } else {
        $client_value  = trim($_POST["id_user_client"] ?? "");
        $angajat_value = trim($_POST["id_user_angajat"] ?? "");
        $status_value  = trim($_POST["status_rezervare"] ?? "");

        $client  = filter_var($client_value, FILTER_VALIDATE_INT);
        $angajat = filter_var($angajat_value, FILTER_VALIDATE_INT);
        $status  = $status_value;
    }

    if ($camera === false || $camera <= 0) {
        $mesaj = "Cameră invalidă.";
    } elseif (!valid_date($checkin_value) || !valid_date($checkout_value)) {
        $mesaj = "Date invalide.";
    } elseif (strtotime($checkout_value) <= strtotime($checkin_value)) {
        $mesaj = "Check-out trebuie să fie după check-in.";
    } elseif ($rol !== 'client' && ($client === false || $client <= 0)) {
        $mesaj = "Client invalid.";
    } elseif ($rol !== 'client' && ($angajat === false || $angajat <= 0)) {
        $mesaj = "Angajat invalid.";
    } elseif (!in_array($status, $status_ok, true)) {
        $mesaj = "Status invalid.";
    } else {
        try {
            $stmtBusy = $conn->prepare("
                SELECT 1 FROM rezervari
                WHERE id_camera = ?
                  AND status_rezervare <> 'anulata'
                  AND NOT (data_checkout <= ? OR data_checkin >= ?)
                LIMIT 1
            ");
            $stmtBusy->bind_param("iss", $camera, $checkin_value, $checkout_value);
            $stmtBusy->execute();

            if ($stmtBusy->get_result()->num_rows > 0) {
                $mesaj = "Camera nu este disponibilă.";
            } else {

                if ($rol === 'client') {
                    $stmt = $conn->prepare("
                        INSERT INTO rezervari
                        (id_user_client, id_camera, data_checkin, data_checkout, status_rezervare)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->bind_param(
                        "iisss",
                        $client,
                        $camera,
                        $checkin_value,
                        $checkout_value,
                        $status
                    );
                } else {
                    $stmt = $conn->prepare("
                        INSERT INTO rezervari
                        (id_user_client, id_user_angajat, id_camera, data_checkin, data_checkout, status_rezervare)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->bind_param(
                        "iiisss",
                        $client,
                        $angajat,
                        $camera,
                        $checkin_value,
                        $checkout_value,
                        $status
                    );
                }

                $stmt->execute();

                $_SESSION["flash_success"] = ($rol === 'client')
                    ? "Cererea ta de rezervare a fost trimisă (în așteptare)."
                    : "Rezervare adăugată cu succes.";

                header("Location: read.php");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            $mesaj = "Eroare la salvare.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Adaugă Rezervare</title>
<link rel="stylesheet" href="../../css/admin.css">
</head>
<body>

<div class="login-container" style="max-width:500px;">
<h2>Adaugă rezervare</h2>

<?php if ($mesaj): ?>
<div class="error-box"><?= htmlspecialchars($mesaj) ?></div>
<?php endif; ?>

<form method="POST" class="login-box">

<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

<?php if ($rol !== 'client'): ?>
<label>Client:</label>
<select name="id_user_client" required>
<?php while ($row = $clienti->fetch_assoc()): ?>
<option value="<?= (int)$row['id_user'] ?>"><?= htmlspecialchars($row['nume']) ?></option>
<?php endwhile; ?>
</select>

<label>Angajat responsabil:</label>
<select name="id_user_angajat" required>
<?php while ($row = $angajati->fetch_assoc()): ?>
<option value="<?= (int)$row['id_user'] ?>"><?= htmlspecialchars($row['nume']) ?></option>
<?php endwhile; ?>
</select>
<?php endif; ?>

<label>Cameră:</label>
<select name="id_camera" required>
<?php while ($row = $camere->fetch_assoc()): ?>
<option value="<?= (int)$row['id_camera'] ?>">Camera <?= htmlspecialchars($row['numar_camera']) ?></option>
<?php endwhile; ?>
</select>

<label>Data check-in:</label>
<input type="date" name="data_checkin" required>

<label>Data check-out:</label>
<input type="date" name="data_checkout" required>

<?php if ($rol !== 'client'): ?>
<label>Status:</label>
<select name="status_rezervare">
<option value="confirmata">confirmata</option>
<option value="in_curs">in_curs</option>
<option value="in_asteptare">in_asteptare</option>
<option value="anulata">anulata</option>
</select>
<?php endif; ?>

<button type="submit">Salvează</button>
</form>

<a class="logout-btn" href="read.php">Înapoi</a>
</div>
</body>
</html>
