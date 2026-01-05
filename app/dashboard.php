<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$nume = $_SESSION['nume'];
$rol  = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>HotelHub Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: Poppins, sans-serif; background: #f5f7fb; }

        .topbar {
            background:#0b3a68;
            color:#fff;
            padding:16px 24px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .brand { font-weight:700; font-size:20px; }
        .user { opacity:.95; }

        .hub-card {
            display:flex;
            gap:14px;
            align-items:flex-start;
            padding:18px;
            border-radius:18px;
            background:#fff;
            border:1px solid rgba(0,0,0,.06);
            box-shadow: 0 10px 24px rgba(0,0,0,.06);
            transition: transform .12s ease, box-shadow .12s ease;
            text-decoration:none;
            color:inherit;
        }
        .hub-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(0,0,0,.09);
        }

        .hub-card::before,
        .hub-card::after {
            content:none !important;
            display:none !important;
        }

        .hub-icon {
            width:46px;
            height:46px;
            border-radius:14px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:#eef4ff;
            font-size:22px;
            flex:0 0 auto;
        }

        .hub-title { font-weight:700; margin:0; }
        .hub-desc { margin:0; color:#5b6776; }

        .logout-btn {
            display:inline-block;
            padding:10px 18px;
            border-radius:12px;
            background:#d33;
            color:#fff;
            text-decoration:none;
            font-weight:600;
        }

        .footer {
            text-align:center;
            color:#667085;
            padding:22px 0;
        }
    </style>
</head>
<body>

<header class="topbar">
    <div class="brand">🏨 HotelHub</div>
    <div class="user">
        Salut, <strong><?= htmlspecialchars($nume) ?></strong>
        (<?= htmlspecialchars($rol) ?>)
    </div>
</header>

<main class="container py-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <h1 class="m-0">Dashboard</h1>
        <a class="logout-btn" href="auth/logout.php">Logout</a>
    </div>

    <div class="row g-4">

        <?php if ($rol === 'admin'): ?>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="/proiect/app/users/read.php" class="hub-card">
                    <div class="hub-icon">👤</div>
                    <div>
                        <p class="hub-title">Utilizatori</p>
                        <p class="hub-desc">Administrare conturi angajați + clienți</p>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="camere/read.php" class="hub-card">
                    <div class="hub-icon">🛏️</div>
                    <div>
                        <p class="hub-title">Camere</p>
                        <p class="hub-desc">Camere și tarife</p>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="servicii/read.php" class="hub-card">
                    <div class="hub-icon">🧩</div>
                    <div>
                        <p class="hub-title">Servicii</p>
                        <p class="hub-desc">Administrare servicii hotel</p>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="facturi/read.php" class="hub-card">
                    <div class="hub-icon">🧾</div>
                    <div>
                        <p class="hub-title">Facturi</p>
                        <p class="hub-desc">Vizualizare facturi</p>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="/proiect/app/stats/index.php" class="hub-card">
                    <div class="hub-icon">📊</div>
                    <div>
                        <p class="hub-title">Statistici</p>
                        <p class="hub-desc">Rezervări și încasări</p>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="/proiect/app/atractii/import_local.php" class="hub-card">
                    <div class="hub-icon">🗺️</div>
                    <div>
                        <p class="hub-title">Import Atracții</p>
                        <p class="hub-desc">Import din atractii.txt (Wikipedia)</p>
                    </div>
                </a>
            </div>

        <?php endif; ?>

        <?php if ($rol === 'angajat'): ?>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="camere/read.php" class="hub-card">
                    <div class="hub-icon">🛏️</div>
                    <div>
                        <p class="hub-title">Camere</p>
                        <p class="hub-desc">Disponibilitate camere</p>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="rezervari/read.php" class="hub-card">
                    <div class="hub-icon">📅</div>
                    <div>
                        <p class="hub-title">Rezervări</p>
                        <p class="hub-desc">Check-in / Check-out</p>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="facturi/read.php" class="hub-card">
                    <div class="hub-icon">🧾</div>
                    <div>
                        <p class="hub-title">Facturi</p>
                        <p class="hub-desc">Emiterea și gestionarea facturilor</p>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="/proiect/app/stats/index.php" class="hub-card">
                    <div class="hub-icon">📊</div>
                    <div>
                        <p class="hub-title">Statistici</p>
                        <p class="hub-desc">Rezervări și încasări</p>
                    </div>
                </a>
            </div>

        <?php endif; ?>

        <?php if ($rol === 'client'): ?>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="camere/search.php" class="hub-card">
                    <div class="hub-icon">🔍</div>
                    <div>
                        <p class="hub-title">Caută camere</p>
                        <p class="hub-desc">Vezi camere disponibile</p>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="rezervari/create.php" class="hub-card">
                    <div class="hub-icon">➕</div>
                    <div>
                        <p class="hub-title">Rezervare nouă</p>
                        <p class="hub-desc">Fă o rezervare online</p>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="rezervari/read.php" class="hub-card">
                    <div class="hub-icon">📜</div>
                    <div>
                        <p class="hub-title">Rezervările mele</p>
                        <p class="hub-desc">Istoric rezervări</p>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="facturi/read.php" class="hub-card">
                    <div class="hub-icon">🧾</div>
                    <div>
                        <p class="hub-title">Facturile mele</p>
                        <p class="hub-desc">Vizualizare facturi</p>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="/proiect/contact.php" class="hub-card">
                    <div class="hub-icon">✉️</div>
                    <div>
                        <p class="hub-title">Contact</p>
                        <p class="hub-desc">Trimite un mesaj către recepție</p>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="/proiect/atractii.php" class="hub-card">
                    <div class="hub-icon">📍</div>
                    <div>
                        <p class="hub-title">Atracții turistice</p>
                        <p class="hub-desc">Obiective din oraș (Wikipedia)</p>
                    </div>
                </a>
            </div>

        <?php endif; ?>

    </div>
</main>

<footer class="footer">
    © <?= date('Y') ?> HotelHub
</footer>

</body>
</html>
