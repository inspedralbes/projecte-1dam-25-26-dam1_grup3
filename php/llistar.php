<?php
require_once 'connexio.php';

// Filtre opcional per prioritat
$filtre_prioritat = $_GET['prioritat'] ?? '';

$sql = "SELECT i.ID_Incidencia, i.Descripcio, i.Prioridad, i.Data_FIN,
               d.Nom AS departament,
               t.Nom AS tecnic
        FROM INCIDENCIA i
        LEFT JOIN DEPARTAMENT d ON i.ID_Departament = d.ID_Departament
        LEFT JOIN TECNIC t ON i.ID_Tecnic = t.ID_Tecnic";

if ($filtre_prioritat !== '') {
    $sql .= " WHERE i.Prioridad = ?";
}
$sql .= " ORDER BY i.ID_Incidencia DESC";

$stmt = $conn->prepare($sql);
if ($filtre_prioritat !== '') {
    $stmt->bind_param("s", $filtre_prioritat);
}
$stmt->execute();
$result = $stmt->get_result();

function prioritatBadge($p) {
    $map = [
            'Baja'    => 'badge-baja',
            'Media'   => 'badge-media',
            'Alta'    => 'badge-alta',
            'Crítica' => 'badge-critica',
    ];
    return $map[$p] ?? 'badge-media';
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GI3P — Llistat d'incidències</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estils.css">
    <link rel="icon" type="image/jpg" href="img/icon.jpg">
    <style>
        .filter-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }
        .filter-bar label {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-muted);
        }
        .filter-bar select {
            padding: 6px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            background: var(--surface);
            color: var(--text);
            cursor: pointer;
        }
        .filter-bar button {
            padding: 6px 16px;
            font-size: 14px;
        }
        .stat-bar {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .stat-chip {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 8px 16px;
            font-size: 13px;
            color: var(--text-muted);
        }
        .stat-chip strong {
            color: var(--text);
            font-weight: 700;
        }
        .btn-detall {
            display: inline-block;
            padding: 4px 12px;
            background: var(--accent-light);
            color: var(--accent);
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid var(--accent);
            transition: background 0.15s;
        }
        .btn-detall:hover {
            background: var(--accent);
            color: #fff;
        }
    </style>
</head>
<body>

<div class="encabezado">
    <a href="index.php"><img src="img/logo.png" style="height:90px;position:absolute;top:50%;right:32px;transform:translateY(-50%);" alt="Logo"></a>
    <div class="brand">GI3P</div>
    <h1>Institut Pedralbes</h1>
    <p>Llistat d'incidències</p>
</div>

<div class="page-content">
    <div class="topbar">
        <a href="index_tecnic.php" class="btn btn-secondary">← Tornar</a>
        <a href="crear_incidencia.php" class="btn btn-primary">+ Nova incidència</a>
    </div>

    <h2 class="page-title">Totes les incidències</h2>

    <!-- Filtre per prioritat -->
    <form method="GET" action="llistar.php" class="filter-bar">
        <label for="prioritat">Filtrar per prioritat:</label>
        <select name="prioritat" id="prioritat">
            <option value="" <?= $filtre_prioritat === '' ? 'selected' : '' ?>>Totes</option>
            <option value="Baja"    <?= $filtre_prioritat === 'Baja'    ? 'selected' : '' ?>>Baja</option>
            <option value="Media"   <?= $filtre_prioritat === 'Media'   ? 'selected' : '' ?>>Media</option>
            <option value="Alta"    <?= $filtre_prioritat === 'Alta'    ? 'selected' : '' ?>>Alta</option>
            <option value="Crítica" <?= $filtre_prioritat === 'Crítica' ? 'selected' : '' ?>>Crítica</option>
        </select>
        <button type="submit" class="btn btn-secondary">Aplicar filtre</button>
        <?php if ($filtre_prioritat !== ''): ?>
            <a href="llistar.php" class="btn btn-secondary">× Netejar</a>
        <?php endif; ?>
    </form>

    <?php if ($result && $result->num_rows > 0):
        $incidencies = [];
        while ($row = $result->fetch_assoc()) {
            $incidencies[] = $row;
        }
        $total    = count($incidencies);
        $obertes  = count(array_filter($incidencies, fn($r) => $r['Data_FIN'] === null));
        $resoltes = $total - $obertes;
        ?>

        <!-- Estadístiques ràpides -->
        <div class="stat-bar">
            <div class="stat-chip">Total: <strong><?= $total ?></strong></div>
            <div class="stat-chip">Obertes: <strong><?= $obertes ?></strong></div>
            <div class="stat-chip">Resoltes: <strong><?= $resoltes ?></strong></div>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Departament</th>
                    <th>Descripció</th>
                    <th>Prioritat</th>
                    <th>Tècnic</th>
                    <th>Data FIN</th>
                    <th>Acció</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($incidencies as $row): ?>
                    <tr>
                        <td>
                        <span style="font-family:'DM Mono',monospace;font-size:13px;color:var(--text-muted)">
                            #<?= $row['ID_Incidencia'] ?>
                        </span>
                        </td>
                        <td><?= $row['departament'] ? htmlspecialchars($row['departament']) : '<span style="color:var(--text-muted)">—</span>' ?></td>
                        <td><?= htmlspecialchars($row['Descripcio']) ?></td>
                        <td>
                        <span class="badge <?= prioritatBadge($row['Prioridad']) ?>">
                            <?= htmlspecialchars($row['Prioridad']) ?>
                        </span>
                        </td>
                        <td><?= $row['tecnic'] ? htmlspecialchars($row['tecnic']) : '<span style="color:var(--text-muted)">No assignat</span>' ?></td>
                        <td>
                            <?php if ($row['Data_FIN']): ?>
                                <span style="color:var(--success);font-size:13px;">✓ <?= htmlspecialchars($row['Data_FIN']) ?></span>
                            <?php else: ?>
                                <span style="color:var(--danger);font-size:13px;">Oberta</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="detalls_incidencia.php?id=<?= $row['ID_Incidencia'] ?>" class="btn-detall">
                                Veure detall
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="alert alert-info">No hi ha incidències a mostrar<?= $filtre_prioritat ? " amb prioritat «{$filtre_prioritat}»" : '' ?>.</div>
    <?php endif; ?>
</div>

<?php
$stmt->close();
$conn->close();
?>
</body>
</html>