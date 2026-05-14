<?php
require_once 'connexio.php';
require_once 'logger.php';
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
        $stmt_upd->close();
        $conn->close();
        header("Location: llistar.php?msg=" . urlencode("Incidència $id_inci actualitzada correctament.") . "&tipo=success");
        exit();
    } else {
        $missatge = "Error en actualitzar: " . $conn->error;
        $missatge_tipus = "error";
        $stmt_upd->close();
    }
}

// B. Obtenir llistat d'incidències NO RESOLTES
$sql = "SELECT i.ID_Incidencia, i.Descripcio, i.Prioridad, t.Nom AS Nom, te.Nom AS Nom_Tecnic
        FROM INCIDENCIA i
        LEFT JOIN TIPOLOGIA t ON i.ID_Tipo = t.ID_Tipo
        LEFT JOIN TECNIC te ON i.ID_Tecnic = te.ID_Tecnic
        WHERE i.Data_FIN IS NULL";
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
include_once "header.php";
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>GI3P — Modificar Incidències</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/estils.css">
    <link rel="icon" type="image/jpg" href="img/icon.jpg">
</head>
<body>

<div class="page-content" style="height: 100%; width: 100%;">
    <div class="topbar" style="margin: 15px;">
        <a href="#" onclick="history.back(); return false;" class="btn btn-secondary"> Tornar</a> 
    </div>

    <h1>Modificar Incidències</h1>

    <?php if (isset($missatge)): ?>
        <div class="alert alert-<?= $missatge_tipus ?>"><?= htmlspecialchars($missatge) ?></div>
    <?php endif; ?>

    <div style="overflow-x: auto;">
        <table class="data-table" style="width: 100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripció</th>
                    <th>Prioritat actual</th>
                    <th>Tipus actual</th>
                    <th>Tècnic actual</th>
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
                        <td><span class="badge badge-other text-dark"><?= htmlspecialchars($row['Nom'] ?? 'Sense tipus') ?></span></td>
                        <td><span class="badge badge-other text-dark"><?= htmlspecialchars($row['Nom_Tecnic'] ?? 'No assignat') ?></span></td>
                        <td>
                            <form method="POST" action="" id="form_<?= $row['ID_Incidencia'] ?>" onsubmit="return validarFormulario(this)">
                                <input type="hidden" name="ID_Incidencia" value="<?= $row['ID_Incidencia'] ?>">

                                <select name="ID_Tecnic" required>
                                    <option value="">Selecciona tècnic…</option>
                                    <?php
                                    $tecnics->data_seek(0);
                                    while ($t = $tecnics->fetch_assoc()):
                                    ?>
                                        <option value="<?= $t['ID_Tecnic'] ?>">
                                            <?= htmlspecialchars($t['Nom']) ?>
                                        </option>
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
<?php
include_once "footer.php";
?>
<script src="js/errors_modificar.js"></script>
</body>
</html>