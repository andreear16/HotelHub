<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db/connection.php';

$SITE_KEY   = "6Lc42jMsAAAAAAkxUeaLaDpjRnkXpap8Hy4mJgz0";
$SECRET_KEY = "6Lc42jMsAAAAANbMdDma8m3l0hys7-ZET7EQ1r23";

$eroare  = "";
$success = "";

verifyCSRF();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nume    = trim($_POST['nume'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $parola  = $_POST['parola'] ?? '';
    $captcha = $_POST['g-recaptcha-response'] ?? '';

    if ($nume === '' || $email === '' || $parola === '') {
        $eroare = "Completează toate câmpurile.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $eroare = "Email invalid.";
    }
    elseif (strlen($parola) < 5) {
        $eroare = "Parola trebuie să aibă minim 5 caractere.";
    }
    elseif ($captcha === '') {
        $eroare = "Confirmă că nu ești robot.";
    }
    else {
        $verify = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret="
            . urlencode($SECRET_KEY) .
            "&response=" . urlencode($captcha) .
            "&remoteip=" . $_SERVER['REMOTE_ADDR']
        );

        $resp = json_decode($verify, true);

        if (!$resp || empty($resp['success'])) {
            $eroare = "reCAPTCHA invalid.";
        }
        else {
            $stmt = $conn->prepare("SELECT id_user FROM user WHERE email = ? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $eroare = "Există deja un cont cu acest email.";
            }
            else {
                $stmt = $conn->prepare("
                    INSERT INTO user (nume, email, parola, rol)
                    VALUES (?, ?, ?, 'client')
                ");
                $stmt->bind_param("sss", $nume, $email, $parola);
                $stmt->execute();

                $success = "Cont creat cu succes. Te poți autentifica.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Înregistrare</title>
<?php require_once __DIR__ . '/../includes/bootstrap_head.php'; ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="text-center mb-3">Înregistrare client</h2>
                    <p class="text-center text-muted mb-4">Creează un cont nou în HotelHub</p>

                    <?php if ($eroare): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($eroare) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success" role="alert">
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="d-grid gap-3">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                        <div>
                            <label class="form-label">Nume complet</label>
                            <input type="text" name="nume" class="form-control" placeholder="Nume complet" required>
                        </div>

                        <div>
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>

                        <div>
                            <label class="form-label">Parolă</label>
                            <input type="password" name="parola" class="form-control" placeholder="Parolă" required>
                            <div class="form-text">Minim 5 caractere.</div>
                        </div>

                        <div class="d-flex justify-content-center">
                            <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($SITE_KEY) ?>"></div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Creează cont</button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        Ai deja cont? <a href="login.php">Autentificare</a>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
