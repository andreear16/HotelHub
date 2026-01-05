<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);

require_once __DIR__ . '/../db/connection.php';

$result = $conn->query("SELECT id_user, nume, email, rol FROM user");
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Utilizatori</title>
    <link rel="stylesheet" href="../../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body class="hh-body">

<div class="page-container">

    <h1>Utilizatori</h1>

    <a href="create.php" class="btn btn-success">+ Adaugă utilizator</a>

    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nume</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($u = $result->fetch_assoc()): ?>
            <tr>
                <td><?= (int)$u['id_user'] ?></td>
                <td><?= htmlspecialchars($u['nume']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['rol']) ?></td>
                <td>
                    <a href="update.php?id=<?= (int)$u['id_user'] ?>" class="btn btn-primary">
                        Editează
                    </a>

                    <a href="delete.php?id=<?= (int)$u['id_user'] ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>"
                       class="btn btn-danger"
                       onclick="return confirm('Sigur ștergi utilizatorul?')">
                        Șterge
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <a href="/proiect/app/dashboard.php" class="btn btn-back">⬅ Dashboard</a>

</div>

</body>
</html>
