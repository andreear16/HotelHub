<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);

require_once __DIR__ . '/../db/connection.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: read.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        !isset($_POST['csrf_token']) ||
        $_POST['csrf_token'] !== $_SESSION['csrf_token']
    ) {
        http_response_code(403);
        echo "CSRF invalid.";
        exit;
    }

    $nume  = trim($_POST['nume'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol   = $_POST['rol'] ?? '';

    if ($nume === '' || $email === '') {
        $eroare = "Completează toate câmpurile.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $eroare = "Email invalid.";
    } elseif (!in_array($rol, ['admin','angajat','client'], true)) {
        $eroare = "Rol invalid.";
    } else {
        $stmt = $conn->prepare(
            "UPDATE user SET nume = ?, email = ?, rol = ? WHERE id_user = ?"
        );
        $stmt->bind_param("sssi", $nume, $email, $rol, $id);
        $stmt->execute();

        header("Location: read.php");
        exit;
    }
}

$stmt = $conn->prepare(
    "SELECT nume, email, rol FROM user WHERE id_user = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: read.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Editare utilizator</title>
<link rel="stylesheet" href="../../css/admin.css">
</head>
<body>

<div class="login-container" style="max-width:500px;">
<h2>Editare utilizator</h2>

<?php if (!empty($eroare)): ?>
<div class="error-box"><?= htmlspecialchars($eroare) ?></div>
<?php endif; ?>

<form method="POST" class="login-box">

<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

<input name="nume" required value="<?= htmlspecialchars($user['nume']) ?>">
<input name="email" type="email" required value="<?= htmlspecialchars($user['email']) ?>">

<select name="rol" required>
<option value="admin"   <?= $user['rol']==='admin'?'selected':'' ?>>Admin</option>
<option value="angajat" <?= $user['rol']==='angajat'?'selected':'' ?>>Angajat</option>
<option value="client"  <?= $user['rol']==='client'?'selected':'' ?>>Client</option>
</select>

<button type="submit">Salvează</button>
</form>

<a class="logout-btn" href="read.php">Înapoi</a>
</div>

</body>
</html>
