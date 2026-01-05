<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['angajat','admin']);

require_once __DIR__ . '/../db/connection.php';

$id_angajat = $_SESSION['user_id'];
$mesaj = "";

try {
    $rezervari = $conn->query("
        SELECT r.id_rezervare, u.nume
        FROM rezervari r
        JOIN user u ON u.id_user = r.id_user_client
        WHERE r.status_rezervare = 'confirmata'
        ORDER BY r.id_rezervare DESC
    ");
} catch (mysqli_sql_exception $e) {
    $mesaj = "Eroare la încărcarea rezervărilor.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        exit("CSRF invalid");
    }

    $id_rez = filter_input(INPUT_POST, "id_rezervare", FILTER_VALIDATE_INT);
    $total  = filter_input(INPUT_POST, "total", FILTER_VALIDATE_FLOAT);
    $metoda = $_POST["metoda"] ?? "";

    if (!$id_rez || !$total || $total <= 0) {
        $mesaj = "Date invalide.";
    } elseif (!in_array($metoda, ['cash','card'], true)) {
        $mesaj = "Metodă de plată invalidă.";
    } else {
        try {
            $stmt = $conn->prepare("
                INSERT INTO factura
                (id_rezervare, id_user_angajat, data_emitere, total, metoda_plata)
                VALUES (?, ?, CURDATE(), ?, ?)
            ");
            $stmt->bind_param("iids", $id_rez, $id_angajat, $total, $metoda);
            $stmt->execute();

            $_SESSION["flash_success"] = "Factura a fost emisă cu succes.";
            header("Location: read.php");
            exit();
        } catch (mysqli_sql_exception $e) {
            $mesaj = "Eroare la emiterea facturii.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Emite Factură</title>
<link rel="stylesheet" href="../../css/admin.css">
</head>
<body>

<div class="login-container" style="max-width:500px;">
<h2>Emite factură</h2>

<?php if ($mesaj): ?>
<div class="error-box"><?= htmlspecialchars($mesaj) ?></div>
<?php endif; ?>

<form method="POST" class="login-box">

<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

<label>Rezervare:</label>
<select name="id_rezervare" required>
<?php if (isset($rezervari)): ?>
<?php while ($r = $rezervari->fetch_assoc()): ?>
<option value="<?= (int)$r['id_rezervare'] ?>">
Rezervare #<?= (int)$r['id_rezervare'] ?> – <?= htmlspecialchars($r['nume']) ?>
</option>
<?php endwhile; ?>
<?php endif; ?>
</select>

<label>Total (lei):</label>
<input type="number" step="0.01" min="0.01" name="total" required>

<label>Metodă plată:</label>
<select name="metoda" required>
<option value="cash">cash</option>
<option value="card">card</option>
</select>

<button type="submit">Emite factura</button>
</form>

<a class="logout-btn" href="read.php">Înapoi</a>
</div>

</body>
</html>
