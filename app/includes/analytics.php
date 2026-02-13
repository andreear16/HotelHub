<?php

function a_contains($haystack, $needle) {
    if ($haystack === null || $needle === null) return false;
    return strpos($haystack, $needle) !== false;
}

function a_ends_with($haystack, $needle) {
    if ($haystack === null || $needle === null) return false;
    $len = strlen($needle);
    if ($len === 0) return true;
    return substr($haystack, -$len) === $needle;
}

function a_trunc($s, $max) {
    if ($s === null) return null;
    $s = (string)$s;
    return (strlen($s) > $max) ? substr($s, 0, $max) : $s;
}

function shouldSkipAnalytics($path) {
    $lower = strtolower((string)$path);

    $skipExtensions = array('.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.webp', '.svg', '.ico', '.map');
    foreach ($skipExtensions as $ext) {
        if (a_ends_with($lower, $ext)) return true;
    }

    if (a_contains($lower, '/app/lib/')) return true;
    if (a_contains($lower, '/vendor/')) return true;
    if (a_contains($lower, '/app/export/')) return true;
    if (a_contains($lower, '/app/auth/')) return true;   

    return false;
}

function trackPageView() {
    try {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        if (strtoupper($method) !== 'GET') return;

        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $path = parse_url($uri, PHP_URL_PATH);
        if (!$path) return;

        if (shouldSkipAnalytics($path)) return;

        if (!isset($GLOBALS['conn']) || !($GLOBALS['conn'] instanceof mysqli)) return;
        $conn = $GLOBALS['conn'];

        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $role   = isset($_SESSION['rol']) ? $_SESSION['rol'] : null;

        $ip  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        $ua  = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

        $sid = session_id();
        if ($sid === '') $sid = null;

        $path   = a_trunc($path, 255);
        $method = a_trunc($method, 10);
        $role   = $role !== null ? a_trunc((string)$role, 30) : null;
        $ip     = $ip !== null ? a_trunc((string)$ip, 45) : null;
        $ua     = $ua !== null ? a_trunc((string)$ua, 255) : null;
        $ref    = $ref !== null ? a_trunc((string)$ref, 255) : null;
        $sid    = $sid !== null ? a_trunc((string)$sid, 128) : null;

        $userIdStr = ($userId === null) ? null : a_trunc((string)$userId, 20);

        $stmt = $conn->prepare("
            INSERT INTO page_views
            (path, method, user_id, role, ip, user_agent, referrer, session_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssssssss",
            $path,
            $method,
            $userIdStr,
            $role,
            $ip,
            $ua,
            $ref,
            $sid
        );

        $stmt->execute();

    } catch (Throwable $e) {
        error_log("Analytics error: " . $e->getMessage());
        return;
    }
}
