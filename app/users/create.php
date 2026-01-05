<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);

require_once __DIR__ . '/../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        !isset($_POST['csrf_token']) ||
        $_POST['csrf_token'] !== $_SESSION['csrf_token']
    ) {
        http_response_code(403);
        echo "CSRF invalid.";
        exit;
    }

    $nume   = trim($_POST['nume'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $rol    = $_POST['rol'] ?? '';
    $parola = $_POST['parola'] ?? '';

    if ($nume === '' || $email === '' || $parola === '') {
        $eroare = "Completează toate câmpurile.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $eroare = "Email invalid.";
    } elseif (!in_array($rol, ['admin','angajat','client'], true)) {
        $eroare = "Rol invalid.";
    } else {
        $hash = password_hash($parola, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO user (nume, email, parola, rol) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $nume, $email, $hash, $rol);
        $stmt->execute();

        header("Location: read.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Adaugă utilizator</title>
<link rel="stylesheet" href="../../css/admin.css">
</head>
<body>

<div class="login-container" style="max-width:500px;">
<h2>Adaugă utilizator</h2>

<?php if (!empty($eroare)): ?>
<div class="error-box"><?= htmlspecialchars($eroare) ?></div>
<?php endif; ?>

<form method="POST" class="login-box">

<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

<input name="nume" placeholder="Nume complet" required>
<input name="email" type="email" placeholder="Email" required>
<input name="parola" type="password" placeholder="Parolă" required>

<select name="rol" required>
<option value="admin">Admin</option>
<option value="angajat">Angajat</option>
<option value="client">Client</option>
</select>

<button type="submit">Salvează</button>
</form>

<a class="logout-btn" href="read.php">Înapoi</a>
</div>

</body>
</html>
