<?php
session_start();

require_once __DIR__ . '/../db/connection.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/analytics.php';
trackPageView();

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /proiect/app/auth/login.php");
        exit;
    }
}

function requireRole(array $roles) {
    requireLogin();

    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $roles, true)) {
        http_response_code(403);
        echo "Acces interzis.";
        exit;
    }
}

function verifyCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (
            empty($_POST['csrf_token']) ||
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        ) {
            http_response_code(400);
            die("CSRF invalid");
        }
    }
}
