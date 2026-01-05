<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin', 'angajat']);

require_once __DIR__ . '/../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Metodă nepermisă.");
}

if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    die("Cerere invalidă (CSRF).");
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION["flash_error"] = "ID invalid.";
    header("Location: read.php");
    exit();
}

try {
    $stmt = $conn->prepare("DELETE FROM camere WHERE id_camera = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        $_SESSION["flash_error"] = "Cameră inexistentă.";
    } else {
        $_SESSION["flash_success"] = "Cameră ștearsă cu succes.";
    }

} catch (mysqli_sql_exception $e) {
    error_log("Delete camera error: " . $e->getMessage());
    $_SESSION["flash_error"] = "Nu pot șterge camera (posibil are rezervări asociate).";
}

header("Location: read.php");
exit();
