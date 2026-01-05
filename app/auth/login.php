<?php
session_start();
require_once __DIR__ . '/../db/connection.php';
require_once __DIR__ . '/../includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("Cerere invalidă (CSRF).");
    }

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Completeaza toate campurile.';
    } else {

        $sql = "SELECT id_user, nume, email, parola, rol
                FROM user
                WHERE email = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ($password === $user['parola']) {

                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['nume']    = $user['nume'];
                $_SESSION['email']   = $user['email'];
                $_SESSION['rol']     = $user['rol'];

                header("Location: /proiect/app/dashboard.php");
                exit;
            }
        }

        $error = 'Email sau parola incorecta.';
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Autentificare</title>
    <?php require_once __DIR__ . '/../includes/bootstrap_head.php'; ?>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-7 col-lg-5">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="text-center mb-3">Autentificare</h2>
                    <p class="text-center text-muted mb-4">Intră în contul tău HotelHub</p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="d-grid gap-3">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                        <div>
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>

                        <div>
                            <label class="form-label">Parolă</label>
                            <input type="password" name="password" class="form-control" placeholder="Parola" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        Nu ai cont? <a href="register.php">Înregistrează-te</a>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
