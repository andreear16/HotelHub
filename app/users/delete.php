<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);

require_once __DIR__ . '/../db/connection.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['flash_error'] = "ID invalid.";
    header("Location: read.php");
    exit;
}

if (
    !isset($_GET['csrf_token']) ||
    $_GET['csrf_token'] !== $_SESSION['csrf_token']
) {
    http_response_code(403);
    echo "CSRF invalid.";
    exit;
}

if ((int)$id === (int)$_SESSION['user_id']) {
    $_SESSION['flash_error'] = "Nu îți poți șterge propriul cont.";
    header("Location: read.php");
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM user WHERE id_user = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        $_SESSION['flash_error'] = "Utilizator inexistent.";
    } else {
        $_SESSION['flash_success'] = "Utilizator șters cu succes.";
    }
} catch (mysqli_sql_exception $e) {
    $_SESSION['flash_error'] = "Nu pot șterge utilizatorul.";
}

header("Location: read.php");
exit;
