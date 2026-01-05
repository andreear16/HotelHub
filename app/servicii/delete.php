<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);

require_once __DIR__ . '/../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: read.php');
    exit();
}

if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    die('CSRF invalid');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['flash_error'] = 'ID invalid.';
    header('Location: read.php');
    exit();
}

try {
    $stmt = $conn->prepare("DELETE FROM serviciu WHERE id_serviciu = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        $_SESSION['flash_error'] = 'Serviciu inexistent.';
    } else {
        $_SESSION['flash_success'] = 'Serviciu șters cu succes.';
    }
} catch (mysqli_sql_exception $e) {
    $_SESSION['flash_error'] = 'Nu pot șterge serviciul.';
}

header('Location: read.php');
exit();
