<?php
function cache_get(mysqli $conn, string $key): ?string {
    $stmt = $conn->prepare("SELECT content FROM external_cache WHERE cache_key=? AND expires_at > NOW() LIMIT 1");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) return $row['content'];
    return null;
}

function cache_set(mysqli $conn, string $key, string $content, int $ttlSeconds): void {
    $expires = (new DateTime())->modify("+$ttlSeconds seconds")->format("Y-m-d H:i:s");
    $stmt = $conn->prepare("
        INSERT INTO external_cache (cache_key, content, expires_at)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE content=VALUES(content), fetched_at=NOW(), expires_at=VALUES(expires_at)
    ");
    $stmt->bind_param("sss", $key, $content, $expires);
    $stmt->execute();
}

function http_get(string $url, int $timeout = 12): string {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_USERAGENT, 'HotelHub-DAW/1.0');
    $body = curl_exec($ch);
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($body === false || $code >= 400) {
        throw new Exception("HTTP $code: $err");
    }
    return $body;
}

function wikipedia_parse_html(mysqli $conn, string $pageTitle, int $ttlSeconds = 86400): string {
    $key = "wiki_parse_ro:" . mb_strtolower($pageTitle);
    $cached = cache_get($conn, $key);
    if ($cached !== null) return $cached;

    $url = "https://ro.wikipedia.org/w/api.php?action=parse&format=json&prop=text&redirects=1&page=" . rawurlencode($pageTitle);
    $body = http_get($url);
    $json = json_decode($body, true);

    if (!is_array($json) || !isset($json['parse']['text']['*'])) {
        throw new Exception("Wikipedia parse invalid");
    }

    $html = $json['parse']['text']['*'];
    cache_set($conn, $key, $html, $ttlSeconds);
    return $html;
}

function wiki_extract_list_items(string $html, int $limit = 40): array {
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    $xp = new DOMXPath($doc);

    $nodes = $xp->query("//div[contains(@class,'mw-parser-output')]//ul/li");
    $items = [];
    if ($nodes) {
        foreach ($nodes as $li) {
            $t = trim(preg_replace('/\s+/', ' ', $li->textContent));
            $t = preg_replace('/\[\d+\]/', '', $t);
            if (mb_strlen($t) < 3) continue;
            if (mb_strlen($t) > 255) $t = mb_substr($t, 0, 255);
            $items[] = $t;
            if (count($items) >= $limit) break;
        }
    }

    $unique = [];
    $seen = [];
    foreach ($items as $it) {
        $k = mb_strtolower($it);
        if (isset($seen[$k])) continue;
        $seen[$k] = true;
        $unique[] = $it;
    }
    return $unique;
}
