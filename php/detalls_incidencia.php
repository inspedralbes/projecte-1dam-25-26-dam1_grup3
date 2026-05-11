<?php
require_once 'connexio.php';

$incidencia  = null;
$actuacions  = [];
$error_msg   = '';
$id_a_buscar = null;

if (!empty($_GET['id'])) {
    $id_a_buscar = (int) $_GET['id'];
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ID_Incidencia'])) {
    $id_a_buscar = (int) $_POST['ID_Incidencia'];
}

if ($id_a_buscar) {
    $stmt_inc = $conn->prepare(
            "SELECT i.ID_Incidencia, i.Descripcio, i.Prioridad, i.Data_FIN,
                d.Nom AS departament,
                t.Nom AS tecnic,
                tp.Nom AS tipologia
         FROM INCIDENCIA i
         LEFT JOIN DEPARTAMENT d  ON i.ID_Departament = d.ID_Departament
         LEFT JOIN TECNIC t       ON i.ID_Tecnic = t.ID_Tecnic
         LEFT JOIN TIPOLOGIA tp   ON i.ID_Tipo = tp.ID_Tipo
         WHERE i.ID_Incidencia = ?"
    );
    $stmt_inc->bind_param("i", $id_a_buscar);
    $stmt_inc->execute();
    $res_inc = $stmt_inc->get_result();
    if ($res_inc->num_rows > 0) {
        $incidencia = $res_inc->fetch_assoc();
    } else {
        $error_msg = "No s'ha trobat cap incidència amb l'ID #{$id_a_buscar}.";
    }
    $stmt_inc->close();

    if ($incidencia) {
        $stmt_act = $conn->prepare(
                "SELECT ID_Actuacion, Descripcio, Temps, Visible, FIN, Data_Actuacion
             FROM Actuaciones
             WHERE ID_Incidencia = ?
             ORDER BY Data_Actuacion ASC"
        );
        $stmt_act->bind_param("i", $id_a_buscar);
        $stmt_act->execute();
        $res_act = $stmt_act->get_result();
        while ($row = $res_act->fetch_assoc()) {
            $actuacions[] = $row;
        }
        $stmt_act->close();
    }
}

function prioritatBadge($p) {
    $map = [
            'Baja'    => 'badge-baja',
            'Media'   => 'badge-media',
            'Alta'    => 'badge-alta',
            'Crítica' => 'badge-critica',
    ];
    return $map[$p] ?? 'badge-media';
}

$referer  = $_SERVER['HTTP_REFERER'] ?? '';
$back_url = (strpos($referer, 'llistar.php') !== false) ? 'llistar.php' : 'index_client.php';
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GI3P — Detalls incidència</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estils.css">
    <link rel="icon" type="image/jpg" href="img/icon.jpg">
    <style>
        .info-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            margin-bottom: 24px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        @media (max-width: 540px) {
            .info-grid { grid-template-columns: 1fr; }
        }
        .info-item label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            display: block;
            margin-bottom: 4px;
        }
        .info-item span {
            font-size: 15px;
            color: var(--text);
        }
        .info-desc { grid-column: 1 / -1; }
        .actuacio-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 20px;
            margin-bottom: 12px;
        }
        .actuacio-card.finalitzada {
            border-left: 4px solid var(--success);
        }
        .actuacio-meta {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }
        .actuacio-id   { font-family: 'DM Mono', monospace; font-size: 12px; color: var(--text-muted); }
        .actuacio-data { font-size: 12px; color: var(--text-muted); }
        .chip {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .chip-visible { background: #e8f5ee; color: var(--success); }
        .chip-privat  { background: #f5f4f0; color: var(--text-muted); }
        .chip-fin     { background: #e8effe; color: var(--accent); }
        .actuacio-descripcio { font-size: 14px; color: var(--text); line-height: 1.6; }
        .actuacio-temps      { font-size: 12px; color: var(--text-muted); margin-top: 6px; }
        .temps-total {
            background: var(--accent-light);
            border: 1px solid var(--accent);
            border-radius: var(--radius-sm);
            padding: 10px 16px;
            font-size: 14px;
            color: var(--accent);
            font-weight: 600;
            margin-bottom: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="encabezado">
    <a href="index.php"><img src="img/logo.png" style="height:90px;position:absolute;top:50%;right:32px;transform:translateY(-50%);" alt="Logo"></a>
    <div class="brand">GI3P</div>
    <h1>Institut Pedralbes</h1>
    <p>Detalls de la incidència</p>
</div>

<div class="page-content" style="max-width:760px;">
    <div class="topbar">
        <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-secondary">← Tornar</a>
    </div>

    <h2 class="page-title">Consultar incidència</h2>

    <div class="form-card" style="margin-bottom:24px;">
        <form method="POST" action="detalls_incidencia.php">
            <div class="form-group" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
                <div style="flex:1;min-width:180px;">
                    <label class="form-label" for="ID_Incidencia">ID de la incidència</label>
                    <input type="number" class="form-control" id="ID_Incidencia" name="ID_Incidencia"
                           placeholder="Ex: 123" min="1" required
                           value="<?= $id_a_buscar ? (int)$id_a_buscar : '' ?>">
                </div>
                <button type="submit" class="btn btn-primary">Consultar</button>
            </div>
        </form>
    </div>

    <?php if ($error_msg): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <?php if ($incidencia): ?>
        <div class="info-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:8px;">
                <h3 style="font-size:16px;font-weight:700;margin:0;">
                    Incidència <span style="font-family:'DM Mono',monospace;color:var(--text-muted);">#<?= $incidencia['ID_Incidencia'] ?></span>
                </h3>
                <span class="badge <?= prioritatBadge($incidencia['Prioridad']) ?>"><?= htmlspecialchars($incidencia['Prioridad']) ?></span>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Departament</label>
                    <span><?= $incidencia['departament'] ? htmlspecialchars($incidencia['departament']) : '—' ?></span>
                </div>
                <div class="info-item">
                    <label>Tècnic assignat</label>
                    <span><?= $incidencia['tecnic'] ? htmlspecialchars($incidencia['tecnic']) : 'No assignat' ?></span>
                </div>
                <div class="info-item">
                    <label>Tipologia</label>
                    <span><?= $incidencia['tipologia'] ? htmlspecialchars($incidencia['tipologia']) : '—' ?></span>
                </div>
                <div class="info-item">
                    <label>Data FIN</label>
                    <span>
                        <?php if ($incidencia['Data_FIN']): ?>
                            <span style="color:var(--success);">✓ <?= htmlspecialchars($incidencia['Data_FIN']) ?></span>
                        <?php else: ?>
                            <span style="color:var(--danger);">Oberta</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-item info-desc">
                    <label>Descripció</label>
                    <span><?= htmlspecialchars($incidencia['Descripcio']) ?></span>
                </div>
            </div>
        </div>

        <h3 style="font-size:16px;font-weight:700;margin-bottom:12px;">
            Actuacions
            <span style="color:var(--text-muted);font-weight:400;">(<?= count($actuacions) ?>)</span>
        </h3>

        <?php if (!empty($actuacions)):
            $temps_total = array_sum(array_column($actuacions, 'Temps'));
            ?>
            <div class="temps-total">
                ⏱ Temps total: <?= floor($temps_total / 60) ?>h <?= $temps_total % 60 ?>min
            </div>

            <?php foreach ($actuacions as $act): ?>
            <div class="actuacio-card <?= $act['FIN'] ? 'finalitzada' : '' ?>">
                <div class="actuacio-meta">
                    <span class="actuacio-id">#<?= $act['ID_Actuacion'] ?></span>
                    <?php if ($act['Data_Actuacion']): ?>
                        <span class="actuacio-data"><?= htmlspecialchars($act['Data_Actuacion']) ?></span>
                    <?php endif; ?>
                    <span class="chip <?= $act['Visible'] ? 'chip-visible' : 'chip-privat' ?>">
                        <?= $act['Visible'] ? 'Visible' : 'Privat' ?>
                    </span>
                    <?php if ($act['FIN']): ?>
                        <span class="chip chip-fin">Finalitzada</span>
                    <?php endif; ?>
                </div>
                <div class="actuacio-descripcio"><?= htmlspecialchars($act['Descripcio']) ?></div>
                <div class="actuacio-temps">Temps: <?= floor($act['Temps'] / 60) ?>h <?= $act['Temps'] % 60 ?>min</div>
            </div>
        <?php endforeach; ?>

        <?php else: ?>
            <div class="alert alert-info">Aquesta incidència no té actuacions registrades encara.</div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php if (isset($conn)) $conn->close(); ?>
</body>
</html>