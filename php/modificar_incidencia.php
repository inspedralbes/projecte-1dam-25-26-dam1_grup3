<?php
require_once 'connexio.php';

// A. Processar l'actualització
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualitzar'])) {
    $id_inci   = $_POST['ID_Incidencia'];
    $id_tecnic = $_POST['ID_Tecnic'];
    $prioritat = $_POST['Prioridad'];
    $id_tipus  = $_POST['ID_Tipo'];

    $sql_update = "UPDATE INCIDENCIA SET ID_Tecnic = ?, Prioridad = ?, ID_Tipo = ? WHERE ID_Incidencia = ?";
    $stmt_upd = $conn->prepare($sql_update);
    $stmt_upd->bind_param("isii", $id_tecnic, $prioritat, $id_tipus, $id_inci);

    if ($stmt_upd->execute()) {
        $missatge = "Incidència $id_inci actualitzada correctament.";
        $missatge_tipus = "success";
    } else {
        $missatge = "Error en actualitzar: " . $conn->error;
        $missatge_tipus = "error";
    }
    $stmt_upd->close();
}

// B. Obtenir llistat d'incidències NO RESOLTES
$sql = "SELECT ID_Incidencia, Descripcio, Prioridad FROM INCIDENCIA WHERE Data_FIN IS NULL";
$resultat_incidencies = $conn->query($sql);

// C. Obtenir llistat de tècnics
$tecnics = $conn->query("SELECT ID_Tecnic, Nom FROM TECNIC");

// D. Obtenir llistat de tipus
$tipus = $conn->query("SELECT ID_Tipo, Nom FROM TIPOLOGIA");

// Helper: badge class for priority
function prioritatBadge($p) {
    $map = ['Baja' => 'badge-baja', 'Media' => 'badge-media', 'Alta' => 'badge-alta', 'Crítica' => 'badge-critica'];
    return $map[$p] ?? 'badge-media';
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>GI3P — Modificar Incidències</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/estils.css">
</head>
<body>

<div class="encabezado">
    <img src="img/logo.png" style="height:90px;position:absolute;top:50%;right:32px;transform:translateY(-50%);" alt="Logo">
    <div class="brand">GI3P</div>
    <h1>Institut Pedralbes</h1>
    <p>Gestió d'incidències</p>
</div>

<div class="page-content">
    <div class="topbar">
        <a href="index_tecnic.php" class="btn btn-secondary">← Tornar</a>
    </div>

    <h2 class="page-title">Modificar Incidències</h2>

    <?php if (isset($missatge)): ?>
        <div class="alert alert-<?= $missatge_tipus ?>"><?= htmlspecialchars($missatge) ?></div>
    <?php endif; ?>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripció</th>
                    <th>Prioritat actual</th>
                    <th>Assignar tècnic</th>
                    <th>Nova prioritat</th>
                    <th>Tipus</th>
                    <th>Acció</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultat_incidencies && $resultat_incidencies->num_rows > 0): ?>
                    <?php while ($row = $resultat_incidencies->fetch_assoc()): ?>
                    <tr>
                        <td><span style="font-family:'DM Mono',monospace;font-size:13px;color:var(--text-muted)">#<?= $row['ID_Incidencia'] ?></span></td>
                        <td><?= htmlspecialchars($row['Descripcio']) ?></td>
                        <td><span class="badge <?= prioritatBadge($row['Prioridad']) ?>"><?= $row['Prioridad'] ?></span></td>

                        <td>
                            <form method="POST" action="" id="form_<?= $row['ID_Incidencia'] ?>">
                                <input type="hidden" name="ID_Incidencia" value="<?= $row['ID_Incidencia'] ?>">
                                <select name="ID_Tecnic" required>
                                    <option value="">Selecciona tècnic…</option>
                                    <?php $tecnics->data_seek(0); while ($t = $tecnics->fetch_assoc()): ?>
                                        <option value="<?= $t['ID_Tecnic'] ?>"><?= htmlspecialchars($t['Nom']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </form>
                        </td>

                        <td>
                            <select name="Prioridad" form="form_<?= $row['ID_Incidencia'] ?>">
                                <option value="Baja"    <?= $row['Prioridad'] == 'Baja'    ? 'selected' : '' ?>>Baja</option>
                                <option value="Media"   <?= $row['Prioridad'] == 'Media'   ? 'selected' : '' ?>>Media</option>
                                <option value="Alta"    <?= $row['Prioridad'] == 'Alta'    ? 'selected' : '' ?>>Alta</option>
                                <option value="Crítica" <?= $row['Prioridad'] == 'Crítica' ? 'selected' : '' ?>>Crítica</option>
                            </select>
                        </td>

                        <td>
                            <select name="ID_Tipo" form="form_<?= $row['ID_Incidencia'] ?>" required>
                                <option value="">Selecciona tipus…</option>
                                <?php $tipus->data_seek(0); while ($t = $tipus->fetch_assoc()): ?>
                                    <option value="<?= $t['ID_Tipo'] ?>"><?= htmlspecialchars($t['Nom']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </td>

                        <td>
                            <button type="submit" name="actualitzar" form="form_<?= $row['ID_Incidencia'] ?>" class="btn btn-primary">Assignar</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:24px;">No hi ha incidències pendents.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>