<?php
include_once "header.php";
require_once 'connexio.php';

$msg = $_GET['msg'] ?? '';
$tipo = $_GET['tipo'] ?? '';
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GI3P — Llistat d'incidències</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/estils.css">
    <link rel="icon" type="image/jpg" href="img/icon.jpg">
    <style>
        .llistar-wrapper { padding: 1.5rem 2rem; }

        .llistar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .llistar-header h1 {
            font-family: 'DM Sans', sans-serif;
            font-size: 1.4rem;
            font-weight: 600;
            margin: 0;
        }
        .llistar-actions { display: flex; gap: 0.5rem; }

        .llistar-filters {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
        }
        .llistar-filters span {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            opacity: 0.5;
            font-family: 'DM Sans', sans-serif;
        }
        .btn-filter {
            font-size: 0.8rem;
            padding: 0.3rem 0.75rem;
            border-radius: 6px;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.6);
            background: rgba(255,255,255,0.05);
            transition: all 0.15s;
            font-family: 'DM Sans', sans-serif;
        }
        .btn-filter:hover { background: rgba(255,255,255,0.1); color: #fff; }

        .inc-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 3px;
            font-family: 'DM Sans', sans-serif;
        }
        .inc-table thead th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 500;
            opacity: 0.45;
            padding: 0.4rem 0.9rem;
            border: none;
            background: transparent;
        }
        .inc-table tbody tr {
            background: rgba(255,255,255,0.04);
            transition: background 0.12s;
        }
        .inc-table tbody tr:hover { background: rgba(255,255,255,0.08); }
        .inc-table tbody td {
            padding: 0.75rem 0.9rem;
            border: none;
            font-size: 0.85rem;
            vertical-align: middle;
        }
        .inc-table tbody tr td:first-child { border-radius: 7px 0 0 7px; }
        .inc-table tbody tr td:last-child  { border-radius: 0 7px 7px 0; }

        .id-tag {
            font-family: 'DM Mono', monospace;
            font-size: 0.78rem;
            color: #7ba4ff;
            background: rgba(79,124,255,0.12);
            padding: 0.18rem 0.5rem;
            border-radius: 5px;
        }

        .cat-pill {
            display: inline-block;
            padding: 0.18rem 0.55rem;
            border-radius: 99px;
            font-size: 0.74rem;
            font-weight: 500;
        }
        .cat-hardware, .cat-ordinador, .cat-teclat, .cat-ratoli { background: rgba(79,124,255,0.15); color: #7ba4ff; }
        .cat-software    { background: rgba(16,185,129,0.13);  color: #34d399; }
        .cat-xarxa, .cat-xarxes { background: rgba(239,68,68,0.13); color: #f87171; }
        .cat-seguretat   { background: rgba(6,182,212,0.13);   color: #22d3ee; }
        .cat-projector   { background: rgba(245,158,11,0.13);  color: #fbbf24; }
        .cat-llum, .cat-electricitat { background: rgba(168,85,247,0.13); color: #c084fc; }
        .cat-altres      { background: rgba(107,114,128,0.13); color: #9ca3af; }

        .prio { display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; font-weight: 500; }
        .prio-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
        .prio-alta    .prio-dot { background: #ef4444; box-shadow: 0 0 5px #ef4444; }
        .prio-media   .prio-dot { background: #f59e0b; }
        .prio-baixa   .prio-dot { background: #10b981; }
        .prio-critica .prio-dot { background: #ff2d55; box-shadow: 0 0 7px #ff2d55; animation: blink 1.2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

        .row-alta    td:first-child { box-shadow: inset 3px 0 0 #ef4444; }
        .row-critica td:first-child { box-shadow: inset 3px 0 0 #ff2d55; }
        .row-media   td:first-child { box-shadow: inset 3px 0 0 #f59e0b; }
        .row-baixa   td:first-child { box-shadow: inset 3px 0 0 #10b981; }

        .date-mono { font-family: 'DM Mono', monospace; font-size: 0.77rem; opacity: 0.65; }
        .muted { opacity: 0.35; }
        .empty-box { text-align: center; padding: 3rem; opacity: 0.5; font-family: 'DM Sans', sans-serif; }
    </style>
</head>
<body>
<div class="page-content llistar-wrapper">

    <div class="llistar-header">
        <h1>Llistat d'incidències</h1>
        <div class="llistar-actions">
            <a href="index_tecnic.php" class="btn btn-sm btn-secondary">← Tornar</a>
            <a href="crear_incidencia.php" class="btn btn-sm btn-primary">+ Nova incidència</a>
        </div>
    </div>

    <div class="llistar-filters">
        <span>Ordenar:</span>
        <a href="llistar.php?orden=prioritat" class="btn-filter">Prioritat</a>
        <a href="llistar.php?orden=data" class="btn-filter">Data Inici</a>
        <a href="llistar.php" class="btn-filter">Per defecte</a>
    </div>

    <?php
    // Ordre per defecte: data descendent
    $criteri_ordenacio = "FIELD(i.Prioridad, 'Baja', 'Media', 'Alta', 'Crítica') DESC";

if (isset($_GET['orden'])) {
    if ($_GET['orden'] == 'data') {
        $criteri_ordenacio = "i.Data_Inici DESC";
    }
}

    $incidencies = [];
    $sql = "SELECT i.ID_Incidencia, i.ID_Tecnic, i.Descripcio, t.Nom AS Categoria, i.Data_Inici, i.Data_FIN, i.Prioridad
        FROM INCIDENCIA i
        INNER JOIN TIPOLOGIA t ON i.ID_Tipo = t.ID_Tipo
        ORDER BY $criteri_ordenacio";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $incidencies = $result->fetch_all(MYSQLI_ASSOC);
    }

    if (count($incidencies) > 0): ?>

    <table class="inc-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tècnic</th>
                <th>Descripció</th>
                <th>Tipus</th>
                <th>Data Inici</th>
                <th>Data Final</th>
                <th>Prioritat</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($incidencies as $incidencia):
            $cat_raw  = strtolower($incidencia['Categoria']);
            $prio_raw = strtolower($incidencia['Prioridad'] ?? '');

            // Classe de la pill de categoria
            $cat_class = 'cat-altres';
            foreach (['hardware','ordinador','teclat','ratoli','software','xarxa','xarxes','seguretat','projector','llum','electricitat'] as $k) {
                if (str_contains($cat_raw, $k)) { $cat_class = 'cat-' . $k; break; }
            }

            // Classe de la fila i del punt de prioritat
            $row_class = ''; $prio_class = 'prio-baixa';
            if (str_contains($prio_raw, 'cr'))                                        { $row_class = 'row-critica'; $prio_class = 'prio-critica'; }
            elseif (str_contains($prio_raw, 'alt'))                                   { $row_class = 'row-alta';    $prio_class = 'prio-alta'; }
            elseif (str_contains($prio_raw, 'med') || str_contains($prio_raw, 'mit')) { $row_class = 'row-media';   $prio_class = 'prio-media'; }
            elseif (str_contains($prio_raw, 'baj') || str_contains($prio_raw, 'bai')) { $row_class = 'row-baixa';   $prio_class = 'prio-baixa'; }
        ?>
        <tr class="<?= $row_class ?>">
            <td><span class="id-tag">#<?= $incidencia['ID_Incidencia'] ?></span></td>
            <td><?= $incidencia['ID_Tecnic'] ? htmlspecialchars($incidencia['ID_Tecnic']) : '<span class="muted">—</span>' ?></td>
            <td><?= htmlspecialchars($incidencia['Descripcio']) ?></td>
            <td><span class="cat-pill <?= $cat_class ?>"><?= htmlspecialchars($incidencia['Categoria']) ?></span></td>
            <td class="date-mono"><?= htmlspecialchars($incidencia['Data_Inici']) ?></td>
            <td class="date-mono"><?= $incidencia['Data_FIN'] ? htmlspecialchars($incidencia['Data_FIN']) : '<span class="muted">—</span>' ?></td>
            <td>
                <span class="prio <?= $prio_class ?>">
                    <span class="prio-dot"></span>
                    <?= htmlspecialchars($incidencia['Prioridad'] ?? '—') ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php else: ?>
        <div class="empty-box">No hi ha incidències a mostrar.</div>
    <?php endif; ?>

    <?php $conn->close(); include_once "footer.php"; ?>
</div>

<script>
    window.phpMessage = "<?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>";
    window.phpMessageType = "<?= htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') ?>";
</script>
<script src="js/errors_esborrar.js"></script>
</body>
</html>
