<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);

require_once __DIR__ . '/../db/connection.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['flash_error'] = 'ID invalid.';
    header('Location: read.php');
    exit();
}

$mesaj = '';

$stmt = $conn->prepare(
    "SELECT denumire, pret FROM serviciu WHERE id_serviciu = ? LIMIT 1"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$rez = $stmt->get_result();

if ($rez->num_rows !== 1) {
    $_SESSION['flash_error'] = 'Serviciu inexistent.';
    header('Location: read.php');
    exit();
}

$serviciu = $rez->fetch_assoc();
$denumire = $serviciu['denumire'];
$pret     = $serviciu['pret'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('CSRF invalid');
    }

    $denumire = trim($_POST['denumire'] ?? '');
    $pret     = filter_input(INPUT_POST, 'pret', FILTER_VALIDATE_FLOAT);

    if ($denumire === '' || !$pret || $pret <= 0) {
        $mesaj = 'Date invalide.';
    } else {
        $stmt = $conn->prepare(
            "UPDATE serviciu SET denumire = ?, pret = ? WHERE id_serviciu = ?"
        );
        $stmt->bind_param("sdi", $denumire, $pret, $id);
        $stmt->execute();

        $_SESSION['flash_success'] = 'Serviciu actualizat cu succes.';
        header('Location: read.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Editare serviciu</title>
<link rel="stylesheet" href="../../css/admin.css">
</head>
<body>

<div class="login-container" style="max-width:500px;">
<h2>Editare serviciu</h2>

<?php if ($mesaj): ?>
<div class="error-box"><?= htmlspecialchars($mesaj) ?></div>
<?php endif; ?>

<form method="POST" class="login-box">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

<input type="text" name="denumire" required value="<?= htmlspecialchars($denumire) ?>">
<input type="number" step="0.01" min="0.01" name="pret" required value="<?= htmlspecialchars($pret) ?>">

<button type="submit">Salvează</button>
</form>

<a class="logout-btn" href="read.php">Înapoi</a>
</div>

</body>
</html>
