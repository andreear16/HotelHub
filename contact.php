<?php
require_once __DIR__ . '/app/includes/auth.php';
require_once __DIR__ . '/app/includes/mail.php';

$SITE_KEY   = "6Lc42jMsAAAAAAkxUeaLaDpjRnkXpap8Hy4mJgz0";
$SECRET_KEY = "6Lc42jMsAAAAANbMdDma8m3l0hys7-ZET7EQ1r23";

$eroare = "";
$success = "";

verifyCSRF();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nume    = trim($_POST['nume'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subiect = trim($_POST['subiect'] ?? '');
    $mesaj   = trim($_POST['mesaj'] ?? '');
    $captcha = $_POST['g-recaptcha-response'] ?? '';

    if ($nume === '' || $email === '' || $subiect === '' || $mesaj === '') {
        $eroare = "Completează toate câmpurile.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $eroare = "Email invalid.";
    } elseif ($captcha === '') {
        $eroare = "Confirmă că nu ești robot.";
    } else {
        $verify = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret="
            . urlencode($SECRET_KEY) .
            "&response=" . urlencode($captcha) .
            "&remoteip=" . $_SERVER['REMOTE_ADDR']
        );
        $resp = json_decode($verify, true);

        if (!$resp || empty($resp['success'])) {
            $eroare = "reCAPTCHA invalid.";
        } else {
            $html = "
                <h2>Mesaj nou din Contact - HotelHub</h2>
                <p><b>Nume:</b> " . htmlspecialchars($nume) . "</p>
                <p><b>Email:</b> " . htmlspecialchars($email) . "</p>
                <p><b>Subiect:</b> " . htmlspecialchars($subiect) . "</p>
                <p><b>Mesaj:</b><br>" . nl2br(htmlspecialchars($mesaj)) . "</p>
                <hr>
                <p><i>Trimis de pe formularul public contact.php</i></p>
            ";

            $ok = sendEmail(
                HOTEL_CONTACT_TO,
                "Contact HotelHub: " . $subiect,
                $html,
                $email,
                $nume
            );

            if ($ok) {
                $success = "Mesaj trimis cu succes! Îți vom răspunde în curând.";
            } else {
                $eroare = "Nu s-a putut trimite mesajul. Încearcă mai târziu.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Contact - HotelHub</title>
<link rel="stylesheet" href="/proiect/css/admin.css">
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>

<div class="login-container" style="max-width:600px;">
  <h2>Contact</h2>

  <?php if ($eroare): ?>
    <div class="error-box"><?= htmlspecialchars($eroare) ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="error-box" style="background:#e7ffe7; color:#0b5d1e;">
      <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>

  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

    <input type="text" name="nume" placeholder="Nume complet" required value="<?= htmlspecialchars($_POST['nume'] ?? '') ?>">
    <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    <input type="text" name="subiect" placeholder="Subiect" required value="<?= htmlspecialchars($_POST['subiect'] ?? '') ?>">

    <textarea name="mesaj" placeholder="Mesaj" required style="width:100%; min-height:120px; padding:12px; border-radius:10px;"><?= htmlspecialchars($_POST['mesaj'] ?? '') ?></textarea>

    <div style="margin-top:12px;" class="g-recaptcha" data-sitekey="<?= htmlspecialchars($SITE_KEY) ?>"></div>

    <button type="submit" style="margin-top:12px;">Trimite mesaj</button>
  </form>

  <p style="margin-top:15px;">
    <a href="/proiect/app/dashboard.php">Înapoi la dashboard</a>
  </p>
</div>

</body>
</html>
