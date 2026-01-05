<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin','angajat']);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Statistici - HotelHub</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/proiect/css/admin.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="dashboard-container">
  <h1>Statistici</h1>
  <p style="margin-top:-10px; color:#444;">Rezervări (ultimele 30 zile) & Încasări (ultimele 6 luni)</p>

  <div style="background:#fff; padding:15px; border-radius:12px; margin-top:20px;">
    <h3 style="margin:0 0 10px 0;">Rezervări pe zile (ultimele 30 zile)</h3>
    <canvas id="chartRezervari" height="120"></canvas>
  </div>

  <div style="background:#fff; padding:15px; border-radius:12px; margin-top:20px;">
    <h3 style="margin:0 0 10px 0;">Încasări pe luni (ultimele 6 luni)</h3>
    <canvas id="chartIncasari" height="120"></canvas>
  </div>

  <br>
  <a class="logout-btn" href="/proiect/app/dashboard.php">Înapoi</a>
</div>

<script>
async function loadStats() {
  const res = await fetch('/proiect/app/stats/data.php', { credentials: 'include' });
  const data = await res.json();

  new Chart(document.getElementById('chartRezervari'), {
    type: 'line',
    data: {
      labels: data.rezervari.labels,
      datasets: [{
        label: 'Rezervări',
        data: data.rezervari.values,
        tension: 0.2
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: true } },
      scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
  });

  new Chart(document.getElementById('chartIncasari'), {
    type: 'bar',
    data: {
      labels: data.incasari.labels,
      datasets: [{
        label: 'Lei',
        data: data.incasari.values
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: true } },
      scales: { y: { beginAtZero: true } }
    }
  });
}

loadStats();
</script>

</body>
</html>
