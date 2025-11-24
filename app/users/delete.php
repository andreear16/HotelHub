<?php
session_start();
require_once "../db/connection.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: read.php");
    exit();
}

$id = $_GET["id"];

$sql = "DELETE FROM user WHERE id_user = $id";

$conn->query($sql);

header("Location: read.php");
exit();
?>
