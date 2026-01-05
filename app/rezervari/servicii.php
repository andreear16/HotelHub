<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['angajat', 'admin']);

require_once __DIR__ . '/../db/connection.php';

$id_rezervare = (int)$_GET['id'];

$servicii = $conn->query("SELECT * FROM serviciu");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->query(
        "DELETE FROM rezervare_serviciu WHERE id_rezervare=$id_rezervare"
    );

    foreach ($_POST['servicii'] ?? [] as $id_serviciu) {
        $stmt = $conn->prepare(
            "INSERT INTO rezervare_serviciu VALUES (?, ?)"
        );
        $stmt->bind_param("ii", $id_rezervare, $id_serviciu);
        $stmt->execute();
    }

    header("Location: read.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<body>

<h2>Servicii rezervare #<?= $id_rezervare ?></h2>

<form method="POST">
    <?php while ($s = $servicii->fetch_assoc()): ?>
        <label>
            <input type="checkbox" name="servicii[]"
                   value="<?= $s['id_serviciu'] ?>">
            <?= htmlspecialchars($s['denumire']) ?>
            (<?= number_format($s['pret'],2) ?> lei)
        </label><br>
    <?php endwhile; ?>

    <button>Salvează servicii</button>
</form>

</body>
</html>
