<?php
session_start();
require_once "../db/connection.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET["id"];

$sql = "DELETE FROM camere WHERE id_camera = $id";
$conn->query($sql);

header("Location: read.php");
exit();
?>
