<?php
include_once "header.php";
require_once 'connexio.php';
require_once 'logger.php';
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GI3P — Llistat d'incidències</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/estils.css">
    <link rel="icon" type="image/jpg" href="img/icon.jpg">
</head>
<body>
<div class="page-content" style="height: 100%;">
    <h1>Llistat d'incidències</h1>
    <div class="topbar" style="margin: 15px;">
         <a href="#" onclick="history.back(); return false;" class="btn btn-secondary"> Tornar</a>  
        <a href="crear_incidencia.php" class="btn btn-primary">Nova incidència</a>
    </div>

    <?php
    $sql = "SELECT ID_Incidencia, ID_Tecnic, Descripcio FROM INCIDENCIA";
    $result = $conn->query($sql);

    if ($result->num_rows > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tècnic</th>
                    <th>Descripció</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><span style="font-family:'DM Mono',monospace;font-size:13px;color:var(--text-muted)">#<?= $row['ID_Incidencia'] ?></span></td>
                    <td><?= $row['ID_Tecnic'] ? htmlspecialchars($row['ID_Tecnic']) : '<span class="text-muted">—</span>' ?></td>
                    <td><?= htmlspecialchars($row['Descripcio']) ?></td>
                    <td>
                        <a href="esborrar.php?id=<?= $row['ID_Incidencia'] ?>" class="btn btn-danger"
                           onclick="return confirm('Segur que vols esborrar la incidència #<?= $row['ID_Incidencia'] ?>?')">Esborrar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No hi ha incidències a mostrar.</div>
    <?php endif; ?>

    <?php $conn->close(); 
    include_once "footer.php";?>
</div>

</body>
</html>