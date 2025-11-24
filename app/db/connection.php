<?php
$host = "localhost";
$user = "aradussm_hotelhub";
$pass = "a7a2qeVPRpWuqdgjmqUg";
$db   = "aradussm_hotelhub";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
