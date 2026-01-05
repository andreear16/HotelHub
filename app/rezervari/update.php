<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin', 'angajat']);

require_once __DIR__ . '/../db/connection.php';

function valid_date(string $d): bool {
    $dt = DateTime::createFromFormat("Y-m-d", $d);
    return $dt && $dt->format("Y-m-d") === $d;
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION["flash_error"] = "ID invalid.";
    header("Location: read.php");
    exit();
}

$mesaj = "";
$status_ok = ["confirmata", "anulata", "in_curs"];

try {
    $stmt = $conn->prepare("SELECT * FROM rezervari WHERE id_rezervare = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $rez = $stmt->get_result();

    if ($rez->num_rows !== 1) {
        $_SESSION["flash_error"] = "Rezervare inexistentă.";
        header("Location: read.php");
        exit();
    }

    $rezervare = $rez->fetch_assoc();

    $clienti  = $conn->query("SELECT id_user, nume FROM user WHERE rol='client' ORDER BY nume");
    $angajati = $conn->query("SELECT id_user, nume FROM user WHERE rol='angajat' ORDER BY nume");
    $camere   = $conn->query("SELECT id_camera, numar_camera FROM camere ORDER BY numar_camera");

} catch (mysqli_sql_exception $e) {
    $_SESSION["flash_error"] = "Eroare la încărcarea rezervării.";
    header("Location: read.php");
    exit();
}

$client_value   = (string)$rezervare["id_user_client"];
$angajat_value  = (string)$rezervare["id_user_angajat"];
$camera_value   = (string)$rezervare["id_camera"];
$checkin_value  = (string)$rezervare["data_checkin"];
$checkout_value = (string)$rezervare["data_checkout"];
$status_value   = (string)$rezervare["status_rezervare"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("Cerere invalidă.");
    }

    $client_value   = trim($_POST["id_user_client"] ?? "");
    $angajat_value  = trim($_POST["id_user_angajat"] ?? "");
    $camera_value   = trim($_POST["id_camera"] ?? "");
    $checkin_value  = trim($_POST["data_checkin"] ?? "");
    $checkout_value = trim($_POST["data_checkout"] ?? "");
    $status_value   = trim($_POST["status_rezervare"] ?? "");

    $client  = filter_var($client_value, FILTER_VALIDATE_INT);
    $angajat = filter_var($angajat_value, FILTER_VALIDATE_INT);
    $camera  = filter_var($camera_value, FILTER_VALIDATE_INT);

    if ($client === false || $client <= 0) {
        $mesaj = "Client invalid.";
    } elseif ($angajat === false || $angajat <= 0) {
        $mesaj = "Angajat invalid.";
    } elseif ($camera === false || $camera <= 0) {
        $mesaj = "Cameră invalidă.";
    } elseif (!valid_date($checkin_value) || !valid_date($checkout_value)) {
        $mesaj = "Date invalide.";
    } elseif (strtotime($checkout_value) <= strtotime($checkin_value)) {
        $mesaj = "Check-out trebuie să fie după check-in.";
    } elseif (!in_array($status_value, $status_ok, true)) {
        $mesaj = "Status invalid.";
    } else {
        try {
            $stmtBusy = $conn->prepare("
                SELECT 1 FROM rezervari
                WHERE id_camera = ?
                  AND id_rezervare <> ?
                  AND status_rezervare <> 'anulata'
                  AND NOT (data_checkout <= ? OR data_checkin >= ?)
                LIMIT 1
            ");
            $stmtBusy->bind_param("iiss", $camera, $id, $checkin_value, $checkout_value);
            $stmtBusy->execute();

            if ($stmtBusy->get_result()->num_rows > 0) {
                $mesaj = "Camera nu este disponibilă.";
            } else {
                $stmtUp = $conn->prepare("
                    UPDATE rezervari SET
                        id_user_client = ?,
                        id_user_angajat = ?,
                        id_camera = ?,
                        data_checkin = ?,
                        data_checkout = ?,
                        status_rezervare = ?
                    WHERE id_rezervare = ?
                ");
                $stmtUp->bind_param(
                    "iiisssi",
                    $client,
                    $angajat,
                    $camera,
                    $checkin_value,
                    $checkout_value,
                    $status_value,
                    $id
                );
                $stmtUp->execute();

                $_SESSION["flash_success"] = "Rezervare actualizată cu succes.";
                header("Location: read.php");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            $mesaj = "Eroare la actualizare.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Editare Rezervare</title>
<link rel="stylesheet" href="../../css/admin.css">
</head>
<body>

<div class="login-container" style="max-width:500px;">
<h2>Editare rezervare</h2>

<?php if ($mesaj): ?>
<div class="error-box"><?= htmlspecialchars($mesaj) ?></div>
<?php endif; ?>

<form method="POST" class="login-box">

<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

<label>Client:</label>
<select name="id_user_client" required>
<?php while ($row = $clienti->fetch_assoc()): ?>
<option value="<?= $row['id_user'] ?>" <?= ((string)$row['id_user'] === $client_value) ? "selected" : "" ?>>
<?= htmlspecialchars($row['nume']) ?>
</option>
<?php endwhile; ?>
</select>

<label>Angajat responsabil:</label>
<select name="id_user_angajat" required>
<?php while ($row = $angajati->fetch_assoc()): ?>
<option value="<?= $row['id_user'] ?>" <?= ((string)$row['id_user'] === $angajat_value) ? "selected" : "" ?>>
<?= htmlspecialchars($row['nume']) ?>
</option>
<?php endwhile; ?>
</select>

<label>Cameră:</label>
<select name="id_camera" required>
<?php while ($row = $camere->fetch_assoc()): ?>
<option value="<?= $row['id_camera'] ?>" <?= ((string)$row['id_camera'] === $camera_value) ? "selected" : "" ?>>
Camera <?= $row['numar_camera'] ?>
</option>
<?php endwhile; ?>
</select>

<label>Data check-in:</label>
<input type="date" name="data_checkin" required value="<?= htmlspecialchars($checkin_value) ?>">

<label>Data check-out:</label>
<input type="date" name="data_checkout" required value="<?= htmlspecialchars($checkout_value) ?>">

<label>Status:</label>
<select name="status_rezervare" required>
<option value="confirmata" <?= ($status_value === "confirmata") ? "selected" : "" ?>>confirmata</option>
<option value="in_curs" <?= ($status_value === "in_curs") ? "selected" : "" ?>>in_curs</option>
<option value="anulata" <?= ($status_value === "anulata") ? "selected" : "" ?>>anulata</option>
</select>

<button type="submit">Salvează modificările</button>
</form>

<a class="logout-btn" href="read.php">Înapoi</a>
</div>
</body>
</html>
