<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin', 'angajat', 'client']);

require_once __DIR__ . '/../db/connection.php';

$rol = $_SESSION["rol"];
$id_user = (int)$_SESSION["user_id"];

$sql = "
SELECT
    f.id_factura,
    f.data_emitere,
    f.total,
    f.metoda_plata,
    r.id_rezervare,
    c.nume AS client,
    a.nume AS angajat
FROM factura f
JOIN rezervari r ON r.id_rezervare = f.id_rezervare
JOIN user c ON c.id_user = r.id_user_client
JOIN user a ON a.id_user = f.id_user_angajat
";

if ($rol === "client") {
    $sql .= " WHERE r.id_user_client = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $rez = $stmt->get_result();
} else {
    $rez = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Facturi</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
  body { font-family: Poppins, sans-serif; background:#f5f7fb; }
  .page-title { font-weight:700; }
</style>
</head>
<body>

<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
      <h1 class="page-title m-0">Facturi</h1>
      <div class="text-muted">Lista facturilor din sistem</div>
    </div>

    <div class="d-flex gap-2">
      <?php if (in_array($rol, ['angajat','admin'], true)): ?>
        <a href="create.php" class="btn btn-primary">+ Emite factură</a>
      <?php endif; ?>
      <a href="../dashboard.php" class="btn btn-outline-secondary">Înapoi</a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Client</th>
              <th>Angajat</th>
              <th>Data</th>
              <th>Total</th>
              <th>Plată</th>
              <th class="text-end">PDF</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($rez && $rez->num_rows > 0): ?>
              <?php while ($row = $rez->fetch_assoc()): ?>
                <tr>
                  <td><?= (int)$row["id_factura"] ?></td>
                  <td><?= htmlspecialchars($row["client"]) ?></td>
                  <td><?= htmlspecialchars($row["angajat"]) ?></td>
                  <td><?= htmlspecialchars($row["data_emitere"]) ?></td>
                  <td><?= htmlspecialchars($row["total"]) ?> lei</td>
                  <td><?= htmlspecialchars($row["metoda_plata"]) ?></td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-outline-primary"
                       href="/proiect/app/export/factura_pdf.php?id=<?= (int)$row['id_factura'] ?>">
                      Descarcă PDF
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center text-muted py-4">
                  Nu există facturi de afișat.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</body>
</html>
