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

$sql = "SELECT * FROM user WHERE id_user = $id";
$rez = $conn->query($sql);

if ($rez->num_rows != 1) {
    header("Location: read.php");
    exit();
}

$user = $rez->fetch_assoc();

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nume = $_POST["nume"];
    $email = $_POST["email"];
    $telefon = $_POST["telefon"];
    $rol = $_POST["rol"];
    $parola = $_POST["parola"];

    $sqlUpdate = "UPDATE user SET 
                    nume='$nume', 
                    email='$email', 
                    telefon='$telefon', 
                    rol='$rol',
                    parola='$parola'
                  WHERE id_user=$id";

    if ($conn->query($sqlUpdate)) {
        header("Location: read.php");
        exit();
    } else {
        $mesaj = "Eroare la actualizare!";
    }
}

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Editare Utilizator</title>
    <link rel="stylesheet" href="../../css/admin.css">
</head>

<body>

<div class="login-container" style="max-width:500px;">
    <h2>Editare Utilizator</h2>

    <?php if ($mesaj): ?>
        <div class="error-box"><?= $mesaj ?></div>
    <?php endif; ?>

    <form method="POST" class="login-box">
        <input type="text" name="nume" value="<?= $user['nume'] ?>" required>
        <input type="email" name="email" value="<?= $user['email'] ?>" required>
        <input type="text" name="telefon" value="<?= $user['telefon'] ?>">
        <input type="text" name="rol" value="<?= $user['rol'] ?>" required>
        <input type="text" name="parola" value="<?= $user['parola'] ?>" required>

        <button type="submit">Salvează modificările</button>
    </form>

    <a class="logout-btn" href="read.php">Înapoi</a>
</div>

</body>
</html>
