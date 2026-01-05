<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin', 'angajat']);

require_once __DIR__ . '/../db/connection.php';

if (
    !isset($_GET['id'], $_GET['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])
) {
    die("Cerere invalidă.");
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['flash_error'] = "ID rezervare invalid.";
    header("Location: read.php");
    exit();
}

try {
    $stmt = $conn->prepare("DELETE FROM rezervari WHERE id_rezervare = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        $_SESSION['flash_error'] = "Rezervare inexistentă.";
    } else {
        $_SESSION['flash_success'] = "Rezervare ștearsă cu succes.";
    }
} catch (mysqli_sql_exception $e) {
    $_SESSION['flash_error'] = "Eroare la ștergerea rezervării.";
}

header("Location: read.php");
exit();
