<?php
require_once 'connexio.php';

$sql = 'SELECT d.ID_Departament, d.Nom AS departament,
        COUNT(DISTINCT i.ID_Incidencia) AS total_incidencies,
        COALESCE(SUM(a.Temps), 0) AS temps
        FROM DEPARTAMENT d
        LEFT JOIN INCIDENCIA i ON i.ID_Departament = d.ID_Departament
        LEFT JOIN Actuaciones a ON a.ID_Incidencia = i.ID_Incidencia
        GROUP BY d.ID_Departament, d.Nom
        ORDER BY d.Nom';

$result = $conn->query($sql);

$departaments = [];
$temps_valors = [];
$files        = [];

while ($row = $result->fetch_assoc()) {
    $departaments[] = $row['departament'];
    $temps_valors[] = (int) $row['temps'];
    $files[]        = $row;
}

$colors = [
        'rgba(26,86,232,0.85)',
        'rgba(28,200,138,0.85)',
        'rgba(54,185,204,0.85)',
        'rgba(246,194,62,0.85)',
        'rgba(214,63,63,0.85)',
        'rgba(133,95,230,0.85)',
        'rgba(255,140,66,0.85)',
        'rgba(60,179,113,0.85)',
];

$colors_json       = json_encode(array_slice(array_merge($colors, $colors), 0, count($departaments)));
$departaments_json = json_encode($departaments);
$temps_json        = json_encode($temps_valors);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GI3P — Temps per Departament</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estils.css">
    <link rel="icon" type="image/jpg" href="img/icon.jpg">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <style>
        .grafics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }
        @media (max-width: 720px) {
            .grafics-grid { grid-template-columns: 1fr; }
        }
        .grafic-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
        }
        .grafic-card h3 {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 20px;
        }
        .grafic-barres { grid-column: 1 / -1; }
        .progress-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .progress-label {
            width: 140px;
            flex-shrink: 0;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .progress-bar-wrap {
            flex: 1;
            background: var(--surface-2);
            border-radius: 4px;
            height: 10px;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            border-radius: 4px;
            background: var(--accent);
        }
        .progress-value {
            width: 90px;
            text-align: right;
            color: var(--text-muted);
            font-size: 13px;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

<div class="encabezado">
    <a href="index.php"><img src="img/logo.png" style="height:90px;position:absolute;top:50%;right:32px;transform:translateY(-50%);" alt="Logo"></a>
    <div class="brand">GI3P</div>
    <h1>Institut Pedralbes</h1>
    <p>Temps consumit per departament</p>
</div>

<div class="page-content">
    <div class="topbar">
        <a href="index_tecnic.php" class="btn btn-secondary">← Tornar</a>
    </div>

    <h2 class="page-title">Estadístiques per departament</h2>

    <div class="grafics-grid">

        <!-- Gràfic de barres -->
        <div class="grafic-card grafic-barres">
            <h3>Temps total d'atenció per departament (minuts)</h3>
            <canvas id="graficBarres" height="100"></canvas>
        </div>

        <!-- Gràfic de quesito -->
        <div class="grafic-card">
            <h3>Distribució del temps d'atenció per departament</h3>
            <canvas id="graficQuesito" height="220"></canvas>
        </div>

        <!-- Barres de progrés -->
        <div class="grafic-card">
            <h3>Temps relatiu per departament</h3>
            <?php
            $temps_max = max($temps_valors) ?: 1;
            foreach ($files as $i => $row):
                $pct   = round(($row['temps'] / $temps_max) * 100);
                $color = $colors[$i % count($colors)];
                $hores = floor($row['temps'] / 60);
                $min   = $row['temps'] % 60;
                ?>
                <div class="progress-row">
                <span class="progress-label" title="<?= htmlspecialchars($row['departament']) ?>">
                    <?= htmlspecialchars($row['departament']) ?>
                </span>
                    <div class="progress-bar-wrap">
                        <div class="progress-bar-fill" style="width:<?= $pct ?>%;background:<?= $color ?>"></div>
                    </div>
                    <span class="progress-value"><?= $hores ?>h <?= $min ?>min</span>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <!-- Taula de dades -->
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:24px;">
        <h3 style="font-size:14px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:16px;">
            Dades detallades
        </h3>
        <table class="data-table">
            <thead>
            <tr>
                <th>Departament</th>
                <th>Total incidències</th>
                <th>Temps total</th>
                <th>Temps (min)</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($files as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['departament']) ?></td>
                    <td><?= $row['total_incidencies'] ?></td>
                    <td><?= floor($row['temps'] / 60) ?>h <?= $row['temps'] % 60 ?>min</td>
                    <td style="font-family:'DM Mono',monospace;font-size:13px;color:var(--text-muted)">
                        <?= $row['temps'] ?> min
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    Chart.defaults.font.family = "'DM Sans', sans-serif";
    Chart.defaults.color = '#6b6b66';

    const labels    = <?= $departaments_json ?>;
    const tempsData = <?= $temps_json ?>;
    const bgColors  = <?= $colors_json ?>;

    // Gràfic de barres
    new Chart(document.getElementById('graficBarres'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Temps (minuts)',
                data: tempsData,
                backgroundColor: bgColors,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const min = ctx.parsed.y;
                            return ` ${Math.floor(min/60)}h ${min%60}min (${min} min)`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { callback: val => val + ' min' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Gràfic de quesito — mostra distribució del TEMPS (no nombre d'incidències)
    new Chart(document.getElementById('graficQuesito'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: tempsData,
                backgroundColor: bgColors,
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 14, boxWidth: 12, font: { size: 12 } }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct   = total ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                            const h     = Math.floor(ctx.parsed / 60);
                            const m     = ctx.parsed % 60;
                            return ` ${ctx.label}: ${h}h ${m}min (${pct}%)`;
                        }
                    }
                }
            }
        }
    });
</script>

<?php $conn->close(); ?>
</body>
</html>