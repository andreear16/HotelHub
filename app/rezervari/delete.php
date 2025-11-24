<?php
session_start();
require_once "../db/connection.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET["id"];

$conn->query("DELETE FROM rezervari WHERE id_rezervare=$id");

header("Location: read.php");
exit();
?>
