<?php
// stats.php — Panell d'estadístiques d'accés (només administrador)
// Requereix: MongoDB actiu, logger.php funcionant

error_reporting(E_ALL & ~E_DEPRECATED);
session_start();
require_once 'logger.php';
include_once 'header.php';

// ── Connexió MongoDB ───────────────────────────────────────────────────────
require_once __DIR__ . '/vendor/autoload.php';

    $mongoUri   = getenv('MONGO_URI') ?: 'mongodb://root:example@mongo:27017';
    $client     = new MongoDB\Client($mongoUri);
    $collection = $client->gi3p_logs->access_logs;

    // ── Filtres de cerca ───────────────────────────────────────────────────
    $filter_user  = trim($_GET['user']  ?? '');
    $filter_page  = trim($_GET['page']  ?? '');
    $filter_from  = trim($_GET['from']  ?? '');
    $filter_to    = trim($_GET['to']    ?? '');

    // Construïm el filtre MongoDB
    $query = [];
    if ($filter_user !== '') {
        $query['user'] = $filter_user;
    }
    if ($filter_page !== '') {
        // Cerca parcial a la URL (regex)
        $query['url'] = new MongoDB\BSON\Regex(preg_quote($filter_page), 'i');
    }
    if ($filter_from !== '' || $filter_to !== '') {
        $query['timestamp'] = [];
        if ($filter_from !== '') {
            $query['timestamp']['$gte'] = new MongoDB\BSON\UTCDateTime(
                (new DateTime($filter_from . ' 00:00:00'))->getTimestamp() * 1000
            );
        }
        if ($filter_to !== '') {
            $query['timestamp']['$lte'] = new MongoDB\BSON\UTCDateTime(
                (new DateTime($filter_to . ' 23:59:59'))->getTimestamp() * 1000
            );
        }
    }

    // ── AGREGACIÓ 1: Total d'accessos (amb filtre) ─────────────────────────
    $total_accessos = $collection->countDocuments($query);

    // ── AGREGACIÓ 2: Pàgines més visitades ────────────────────────────────
    $pipeline_pagines = [];
    if (!empty($query)) $pipeline_pagines[] = ['$match' => $query];
    $pipeline_pagines[] = [
        '$addFields' => [
            'url_neta' => [
                '$arrayElemAt' => [
                    ['$split' => ['$url', '?']], 0
                ]
            ]
        ]
    ];
    $pipeline_pagines[] = ['$group' => ['_id' => '$url_neta', 'visites' => ['$sum' => 1]]];
    $pipeline_pagines[] = ['$sort'  => ['visites' => -1]];
    $pipeline_pagines[] = ['$limit' => 10];

    $pagines_top = iterator_to_array($collection->aggregate($pipeline_pagines));

    // ── AGREGACIÓ 3: Usuaris més actius ────────────────────────────────────
    $pipeline_usuaris = [];
    if (!empty($query)) $pipeline_usuaris[] = ['$match' => $query];
    $pipeline_usuaris[] = ['$group' => ['_id' => '$user', 'accessos' => ['$sum' => 1]]];
    $pipeline_usuaris[] = ['$sort'  => ['accessos' => -1]];
    $pipeline_usuaris[] ['$limit'] = 10;
    // Fix: afegim limit correctament
    $pipeline_usuaris = [];
    if (!empty($query)) $pipeline_usuaris[] = ['$match' => $query];
    $pipeline_usuaris[] = ['$group'  => ['_id' => '$user', 'accessos' => ['$sum' => 1]]];
    $pipeline_usuaris[] = ['$sort'   => ['accessos' => -1]];
    $pipeline_usuaris[] = ['$limit'  => 10];

    $usuaris_top = iterator_to_array($collection->aggregate($pipeline_usuaris));

    // ── AGREGACIÓ 4: Accessos per dia (últims 30 dies, per gràfic) ─────────
    $trenta_dies = new MongoDB\BSON\UTCDateTime((time() - 30 * 86400) * 1000);
    $query_grafic = array_merge($query, ['timestamp' => ['$gte' => $trenta_dies]]);
    // Si ja hi havia filtre de dates, prioritzem el del filtre
    if (isset($query['timestamp'])) {
        $query_grafic['timestamp'] = $query['timestamp'];
    }

    $pipeline_dies = [
        ['$match' => $query_grafic],
        ['$group' => [
            '_id' => [
                'any' => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$timestamp']]
            ],
            'total' => ['$sum' => 1]
        ]],
        ['$sort'  => ['_id.any' => 1]],
        ['$limit' => 30],
    ];

    $accessos_per_dia = iterator_to_array($collection->aggregate($pipeline_dies));

    // ── AGREGACIÓ 5: Mètodes HTTP ──────────────────────────────────────────
    $pipeline_metodes = [];
    if (!empty($query)) $pipeline_metodes[] = ['$match' => $query];
    $pipeline_metodes[] = ['$group' => ['_id' => '$method', 'total' => ['$sum' => 1]]];
    $pipeline_metodes[] = ['$sort'  => ['total' => -1]];

    $metodes = iterator_to_array($collection->aggregate($pipeline_metodes));

    // ── AGREGACIÓ 6: IPs úniques ───────────────────────────────────────────
    $pipeline_ips = [];
    if (!empty($query)) $pipeline_ips[] = ['$match' => $query];
    $pipeline_ips[] = ['$group'  => ['_id' => '$ip', 'accessos' => ['$sum' => 1]]];
    $pipeline_ips[] = ['$sort'   => ['accessos' => -1]];
    $pipeline_ips[] = ['$limit'  => 8];

    $ips_top = iterator_to_array($collection->aggregate($pipeline_ips));

    // ── Logs recents (últims 50, amb filtre) ──────────────────────────────
    $logs_recents = iterator_to_array(
        $collection->find($query, [
            'sort'  => ['timestamp' => -1],
            'limit' => 50,
        ])
    );

    // ── Dades per gràfics (JSON per Chart.js) ─────────────────────────────
    $chart_dies_labels = [];
    $chart_dies_data   = [];
    foreach ($accessos_per_dia as $d) {
        $chart_dies_labels[] = $d['_id']['any'];
        $chart_dies_data[]   = $d['total'];
    }

    $chart_pagines_labels = [];
    $chart_pagines_data   = [];
    foreach ($pagines_top as $p) {
        // Escurcem la URL per mostrar-la bé al gràfic
        $parts = explode('/', $p['_id']);
        $chart_pagines_labels[] = end($parts) ?: '/';
        $chart_pagines_data[]   = $p['visites'];
    }

    $chart_metodes_labels = [];
    $chart_metodes_data   = [];
    foreach ($metodes as $m) {
        $chart_metodes_labels[] = $m['_id'] ?: 'UNKNOWN';
        $chart_metodes_data[]   = $m['total'];
    }
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GI3P — Estadístiques d'Accés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/estils.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/jpg" href="img/icon.jpg">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px 28px;
            box-shadow: var(--shadow-sm);
            text-align: center;
        }
        .stat-card .stat-num {
            font-size: 42px;
            font-weight: 700;
            color: var(--accent);
            line-height: 1;
            font-family: 'DM Mono', monospace;
        }
        .stat-card .stat-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-top: 6px;
        }
        .chart-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
        }
        .chart-card h3 {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 20px;
        }
        .filter-bar {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px 24px;
            margin-bottom: 28px;
            box-shadow: var(--shadow-sm);
        }
        .filter-bar h3 {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 16px;
        }
        .login-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 40px;
            max-width: 420px;
            margin: 60px auto;
            box-shadow: var(--shadow-md);
        }
        .section-title {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin: 32px 0 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
        }
        .url-cell {
            max-width: 280px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-family: 'DM Mono', monospace;
            font-size: 12px;
        }
        .badge-get    { background: #e8f5ee; color: #1a7a4a; }
        .badge-post   { background: #e8effe; color: #1a56e8; }
        .badge-other  { background: var(--surface-2); color: var(--text-muted); }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }
    </style>
</head>
<body>

<div class="page-content" style="max-width: 1100px; padding: 32px 24px;">

    <!-- ── TOPBAR ─────────────────────────────────────────────────────── -->
    <div class="topbar">
        <a href="index_admin.php" class="btn btn-secondary">Tornar</a>
    </div>
    <h1>Panell d'estadístiques d'accés</h1>

    <!-- ── RESUM NUMÈRIC ──────────────────────────────────────────────── -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-num"><?= number_format($total_accessos) ?></div>
            <div class="stat-label">Accessos totals</div>
        </div>
        <div class="stat-card">
            <div class="stat-num"><?= count($pagines_top) ?></div>
            <div class="stat-label">Pàgines úniques</div>
        </div>
        <div class="stat-card">
            <div class="stat-num"><?= count($usuaris_top) ?></div>
            <div class="stat-label">Usuaris únics</div>
        </div>
        <div class="stat-card">
            <div class="stat-num"><?= count($ips_top) ?></div>
            <div class="stat-label">IPs distintes</div>
        </div>
    </div>

    <!-- ── FILTRES ────────────────────────────────────────────────────── -->
    <div class="filter-bar">
        <h3>Filtrar logs</h3>
        <form method="GET" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;align-items:end;">
            <div class="form-group">
                <label class="form-label">Usuari</label>
                <input type="text" name="user" class="form-control" placeholder="Nom d'usuari o null"
                       value="<?= htmlspecialchars($filter_user) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Pàgina (URL)</label>
                <input type="text" name="page" class="form-control" placeholder="ex: crear_incidencia"
                       value="<?= htmlspecialchars($filter_page) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Des de</label>
                <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($filter_from) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Fins a</label>
                <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($filter_to) ?>">
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="stats.php" class="btn btn-secondary">Netejar</a>
            </div>
        </form>
    </div>

    <!-- ── GRÀFICS ────────────────────────────────────────────────────── -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:8px;">

        <!-- Gràfic: Accessos per dia -->
        <div class="chart-card" style="grid-column:1/-1;">
            <h3>Accessos per dia (últims 30 dies)</h3>
            <canvas id="chartDies" height="90"></canvas>
        </div>

        <!-- Gràfic: Pàgines més visitades -->
        <div class="chart-card">
            <h3>Pàgines més visitades</h3>
            <canvas id="chartPagines" height="200"></canvas>
        </div>

        <!-- Gràfic: Mètodes HTTP -->
        <div class="chart-card">
            <h3>Mètodes HTTP</h3>
            <canvas id="chartMetodes" height="200"></canvas>
        </div>

    </div>

    <!-- ── TAULES ─────────────────────────────────────────────────────── -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

        <!-- Pàgines top -->
        <div>
            <div class="section-title">Pàgines més visitades</div>
            <table class="data-table">
                <thead><tr><th>#</th><th>Pàgina</th><th>Visites</th></tr></thead>
                <tbody>
                <?php foreach ($pagines_top as $i => $p):
                    $parts = explode('/', rtrim($p['_id'], '?'));
                    $nom   = end($parts) ?: '/';
                ?>
                <tr>
                    <td style="color:var(--text-muted);font-size:12px;"><?= $i + 1 ?></td>
                    <td><span class="url-cell" title="<?= htmlspecialchars($p['_id']) ?>"><?= htmlspecialchars($nom) ?></span></td>
                    <td><strong><?= $p['visites'] ?></strong></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($pagines_top)): ?>
                    <tr><td colspan="3" style="text-align:center;color:var(--text-muted);">Sense dades</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Usuaris top -->
        <div>
            <div class="section-title">Usuaris més actius</div>
            <table class="data-table">
                <thead><tr><th>#</th><th>Usuari</th><th>Accessos</th></tr></thead>
                <tbody>
                <?php foreach ($usuaris_top as $i => $u): ?>
                <tr>
                    <td style="color:var(--text-muted);font-size:12px;"><?= $i + 1 ?></td>
                    <td><?= $u['_id'] !== null ? htmlspecialchars($u['_id']) : '<span style="color:var(--text-muted);font-style:italic;">no autenticat</span>' ?></td>
                    <td><strong><?= $u['accessos'] ?></strong></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($usuaris_top)): ?>
                    <tr><td colspan="3" style="text-align:center;color:var(--text-muted);">Sense dades</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- IPs top -->
        <div>
            <div class="section-title">IPs més actives</div>
            <table class="data-table">
                <thead><tr><th>IP</th><th>Accessos</th></tr></thead>
                <tbody>
                <?php foreach ($ips_top as $ip): ?>
                <tr>
                    <td style="font-family:'DM Mono',monospace;font-size:13px;"><?= htmlspecialchars($ip['_id'] ?? 'unknown') ?></td>
                    <td><strong><?= $ip['accessos'] ?></strong></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mètodes HTTP -->
        <div>
            <div class="section-title">Mètodes HTTP</div>
            <table class="data-table">
                <thead><tr><th>Mètode</th><th>Total</th></tr></thead>
                <tbody>
                <?php foreach ($metodes as $m):
                    $cls = strtolower($m['_id']) === 'get' ? 'badge-get' : (strtolower($m['_id']) === 'post' ? 'badge-post' : 'badge-other');
                ?>
                <tr>
                    <td><span class="badge <?= $cls ?>"><?= htmlspecialchars($m['_id'] ?? 'UNKNOWN') ?></span></td>
                    <td><strong><?= $m['total'] ?></strong></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- ── LOG RECENT ─────────────────────────────────────────────────── -->
    <div class="section-title">Últims 50 accessos</div>
    <table class="data-table" style="font-size:13px;">
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>Mètode</th>
                <th>URL</th>
                <th>Usuari</th>
                <th>IP</th>
                <th>Navegador</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($logs_recents as $log):
            $ts  = $log['timestamp']->toDateTime();
            $ts->setTimezone(new DateTimeZone('Europe/Madrid'));
            $met = strtolower($log['method'] ?? '');
            $cls = $met === 'get' ? 'badge-get' : ($met === 'post' ? 'badge-post' : 'badge-other');
        ?>
        <tr>
            <td style="font-family:'DM Mono',monospace;white-space:nowrap;font-size:12px;">
                <?= $ts->format('d/m/Y H:i:s') ?>
            </td>
            <td><span class="badge <?= $cls ?>"><?= htmlspecialchars($log['method'] ?? '') ?></span></td>
            <td class="url-cell" title="<?= htmlspecialchars($log['url'] ?? '') ?>">
                <?= htmlspecialchars($log['url'] ?? '') ?>
            </td>
            <td>
                <?php if ($log['user'] ?? null): ?>
                    <?= htmlspecialchars($log['user']) ?>
                <?php else: ?>
                    <span style="color:var(--text-muted);font-style:italic;font-size:12px;">null</span>
                <?php endif; ?>
            </td>
            <td style="font-family:'DM Mono',monospace;font-size:12px;"><?= htmlspecialchars($log['ip'] ?? '') ?></td>
            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:11px;color:var(--text-muted);" title="<?= htmlspecialchars($log['browser'] ?? '') ?>">
                <?= htmlspecialchars(substr($log['browser'] ?? '', 0, 60)) ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($logs_recents)): ?>
            <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:32px;">No hi ha logs per als filtres seleccionats.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- ── CHART.JS ───────────────────────────────────────────────────── -->
    <script>
    const ACCENT       = '#1a56e8';
    const ACCENT_LIGHT = '#e8effe';
    const BORDER       = '#d8d7d2';
    const TEXT_MUTED   = '#6b6b66';

    Chart.defaults.font.family = "'DM Sans', sans-serif";
    Chart.defaults.color       = TEXT_MUTED;

    // Gràfic 1: Línia — Accessos per dia
    new Chart(document.getElementById('chartDies'), {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_dies_labels) ?>,
            datasets: [{
                label: 'Accessos',
                data:  <?= json_encode($chart_dies_data) ?>,
                borderColor:     ACCENT,
                backgroundColor: ACCENT_LIGHT,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: ACCENT,
                fill: true,
                tension: 0.3,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: BORDER } },
                y: { grid: { color: BORDER }, beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // Gràfic 2: Barres horitzontals — Pàgines més visitades
    new Chart(document.getElementById('chartPagines'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($chart_pagines_labels) ?>,
            datasets: [{
                label: 'Visites',
                data:  <?= json_encode($chart_pagines_data) ?>,
                backgroundColor: ACCENT_LIGHT,
                borderColor:     ACCENT,
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: BORDER }, beginAtZero: true },
                y: { grid: { display: false } }
            }
        }
    });

    // Gràfic 3: Donut — Mètodes HTTP
    new Chart(document.getElementById('chartMetodes'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($chart_metodes_labels) ?>,
            datasets: [{
                data:            <?= json_encode($chart_metodes_data) ?>,
                backgroundColor: ['#e8effe','#e8f5ee','#fff3e0','#fdeaea'],
                borderColor:     ['#1a56e8','#1a7a4a','#b06000','#d63f3f'],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, font: { size: 13 } } }
            },
            cutout: '65%',
        }
    });
    </script>


</div><!-- .page-content -->
<?php
include_once "footer.php";
?>
</body>
</html>
