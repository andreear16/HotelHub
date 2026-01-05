<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);

require_once __DIR__ . '/../db/connection.php';

$mesaj = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        exit('CSRF invalid');
    }

    $denumire = trim($_POST['denumire'] ?? '');
    $pret = filter_input(INPUT_POST, 'pret', FILTER_VALIDATE_FLOAT);

    if ($denumire === '' || $pret === false || $pret <= 0) {
        $mesaj = "Date invalide.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO serviciu (denumire, pret) VALUES (?, ?)"
        );
        $stmt->bind_param("sd", $denumire, $pret);
        $stmt->execute();

        $_SESSION['flash_success'] = "Serviciu adăugat cu succes.";
        header("Location: read.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Adaugă serviciu</title>
    <link rel="stylesheet" href="../../css/admin.css">
</head>
<body>

<div class="login-container" style="max-width:500px;">
    <h2>Adaugă serviciu</h2>

    <?php if ($mesaj): ?>
        <div class="error-box"><?= htmlspecialchars($mesaj) ?></div>
    <?php endif; ?>

    <form method="POST" class="login-box">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <input name="denumire" placeholder="Denumire serviciu" required>
        <input type="number" step="0.01" min="0.01" name="pret" placeholder="Preț" required>

        <button type="submit">Salvează</button>
    </form>

    <a class="logout-btn" href="read.php">Înapoi</a>
</div>

</body>
</html>
