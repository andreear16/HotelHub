<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin', 'angajat']);

require_once __DIR__ . '/../db/connection.php';

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION["flash_error"] = "ID invalid.";
    header("Location: read.php");
    exit();
}

$mesaj = $_SESSION["form_error"] ?? "";
$old   = $_SESSION["form_old"] ?? [];
unset($_SESSION["form_error"], $_SESSION["form_old"]);

try {
    $stmt = $conn->prepare("
        SELECT id_camera, numar_camera, tip_camera, pret_noapte, disponibilitate
        FROM camere
        WHERE id_camera = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $rez = $stmt->get_result();

    if ($rez->num_rows !== 1) {
        $_SESSION["flash_error"] = "Cameră inexistentă.";
        header("Location: read.php");
        exit();
    }

    $camera = $rez->fetch_assoc();

    $numar_value = $old["numar_camera"] ?? (string)$camera["numar_camera"];
    $tip_value   = $old["tip_camera"] ?? (string)$camera["tip_camera"];
    $pret_value  = $old["pret_noapte"] ?? (string)$camera["pret_noapte"];
    $disp_value  = $old["disponibilitate"] ?? (string)$camera["disponibilitate"];

} catch (mysqli_sql_exception $e) {
    error_log("Load camera error: " . $e->getMessage());
    $_SESSION["flash_error"] = "Eroare la încărcarea camerei.";
    header("Location: read.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("Cerere invalidă (CSRF).");
    }

    $numar_value = trim($_POST["numar_camera"] ?? "");
    $tip_value   = trim($_POST["tip_camera"] ?? "");
    $pret_value  = trim($_POST["pret_noapte"] ?? "");
    $disp_value  = trim($_POST["disponibilitate"] ?? "0");

    $numar = filter_var($numar_value, FILTER_VALIDATE_INT);
    $pret  = filter_var($pret_value, FILTER_VALIDATE_FLOAT);

    if ($numar === false || $numar <= 0) {
        $mesaj = "Numărul camerei trebuie să fie un întreg pozitiv.";
    } elseif ($tip_value === "" || mb_strlen($tip_value) < 2 || mb_strlen($tip_value) > 50) {
        $mesaj = "Tipul camerei este obligatoriu (2–50 caractere).";
    } elseif ($pret === false || $pret <= 0) {
        $mesaj = "Prețul pe noapte trebuie să fie un număr pozitiv.";
    } elseif (!in_array($disp_value, ["0", "1"], true)) {
        $mesaj = "Disponibilitatea este invalidă.";
    }

    if ($mesaj !== "") {
        $_SESSION["form_error"] = $mesaj;
        $_SESSION["form_old"] = [
            "numar_camera" => $numar_value,
            "tip_camera" => $tip_value,
            "pret_noapte" => $pret_value,
            "disponibilitate" => $disp_value
        ];
        header("Location: update.php?id=" . $id);
        exit();
    }

    try {
        $disp = (int)$disp_value;
        $stmtUp = $conn->prepare("
            UPDATE camere
            SET numar_camera = ?, tip_camera = ?, pret_noapte = ?, disponibilitate = ?
            WHERE id_camera = ?
        ");
        $stmtUp->bind_param("isdii", $numar, $tip_value, $pret, $disp, $id);
        $stmtUp->execute();

        $_SESSION["flash_success"] = "Cameră actualizată cu succes.";
        header("Location: read.php");
        exit();

    } catch (mysqli_sql_exception $e) {
        error_log("Update camera error: " . $e->getMessage());
        $err = ((int)$e->getCode() === 1062)
            ? "Există deja o cameră cu acest număr."
            : "Eroare la actualizare!";

        $_SESSION["form_error"] = $err;
        $_SESSION["form_old"] = [
            "numar_camera" => $numar_value,
            "tip_camera" => $tip_value,
            "pret_noapte" => $pret_value,
            "disponibilitate" => $disp_value
        ];
        header("Location: update.php?id=" . $id);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Editare Cameră</title>
    <link rel="stylesheet" href="../../css/admin.css">
</head>
<body>

<div class="login-container" style="max-width:500px;">
    <h2>Editare cameră</h2>

    <?php if ($mesaj): ?>
        <div class="error-box"><?= htmlspecialchars($mesaj) ?></div>
    <?php endif; ?>

    <form method="POST" class="login-box">

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <input type="number" name="numar_camera" required value="<?= htmlspecialchars($numar_value) ?>">
        <input type="text" name="tip_camera" required value="<?= htmlspecialchars($tip_value) ?>">
        <input type="number" step="0.01" min="0.01" name="pret_noapte" required value="<?= htmlspecialchars($pret_value) ?>">

        <label>Disponibilă?</label>
        <select name="disponibilitate" required>
            <option value="1" <?= ($disp_value === "1") ? "selected" : "" ?>>Da</option>
            <option value="0" <?= ($disp_value === "0") ? "selected" : "" ?>>Nu</option>
        </select>

        <button type="submit">Salvează modificările</button>
    </form>

    <a class="logout-btn" href="read.php">Înapoi</a>
</div>

</body>
</html>
