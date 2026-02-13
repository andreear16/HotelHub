<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin','angajat']);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Website Analytics - HotelHub</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/proiect/css/admin.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="dashboard-container">
  <h1>Website Analytics</h1>
  <p style="margin-top:-10px; color:#444;">
    Vizite, vizitatori unici, top pagini (ultimele 30 zile)
  </p>

  <div style="background:#fff; padding:15px; border-radius:12px; margin-top:20px;">
    <h3 style="margin:0 0 10px 0;">Pageviews pe zile (ultimele 30 zile)</h3>
    <canvas id="chartViews" height="120"></canvas>
  </div>

  <div style="background:#fff; padding:15px; border-radius:12px; margin-top:20px;">
    <h3 style="margin:0 0 10px 0;">Vizitatori unici pe zile (ultimele 30 zile)</h3>
    <canvas id="chartUniques" height="120"></canvas>
    <p style="margin:10px 0 0 0; color:#666; font-size:13px;">
      *Unic = combinație (IP + User-Agent) pe zi (aproximare).
    </p>
  </div>

  <div style="background:#fff; padding:15px; border-radius:12px; margin-top:20px;">
    <h3 style="margin:0 0 10px 0;">Top pagini (ultimele 30 zile)</h3>
    <table class="table table-striped" style="margin:0;">
      <thead>
        <tr>
          <th>Pagină</th>
          <th style="width:120px;">Accesări</th>
        </tr>
      </thead>
      <tbody id="topPagesBody"></tbody>
    </table>
  </div>

  <div style="background:#fff; padding:15px; border-radius:12px; margin-top:20px;">
    <h3 style="margin:0 0 10px 0;">Accesări pe rol (ultimele 30 zile)</h3>
    <canvas id="chartRoles" height="120"></canvas>
  </div>

  <br>
  <a class="logout-btn" href="/proiect/app/dashboard.php">Înapoi</a>
</div>

<script>
async function loadAnalytics() {
  const res = await fetch('/proiect/app/analytics/data.php', { credentials: 'include' });
  const data = await res.json();

  new Chart(document.getElementById('chartViews'), {
    type: 'line',
    data: {
      labels: data.views.labels,
      datasets: [{
        label: 'Pageviews',
        data: data.views.values,
        tension: 0.2
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: true } },
      scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
  });

  new Chart(document.getElementById('chartUniques'), {
    type: 'line',
    data: {
      labels: data.uniques.labels,
      datasets: [{
        label: 'Unici',
        data: data.uniques.values,
        tension: 0.2
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: true } },
      scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
  });

  const tbody = document.getElementById('topPagesBody');
  tbody.innerHTML = '';
  for (const row of data.top_pages) {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${escapeHtml(row.path)}</td><td><strong>${row.views}</strong></td>`;
    tbody.appendChild(tr);
  }

  new Chart(document.getElementById('chartRoles'), {
    type: 'bar',
    data: {
      labels: data.roles.labels,
      datasets: [{
        label: 'Accesări',
        data: data.roles.values
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: true } },
      scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
  });
}

function escapeHtml(s) {
  return String(s)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

loadAnalytics();
</script>

</body>
</html>
