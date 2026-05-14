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
    <link rel="stylesheet" href="../css/estils.css">
    <link rel="icon" type="image/jpg" href="img/icon.jpg">
</head>
<body>
<div class="page-content" style="height: 100%;">
    <h1>Llistat d'incidències</h1>
    <div class="topbar" style="margin: 15px;">
         <a href="index_admin.php" class="btn btn-secondary"> Tornar</a>
        <a href="crear_incidencia.php" class="btn btn-primary">Nova incidència</a>
    </div>
    <div class="mb-3">
        <span>Ordenar per: </span>
        <a href = "llistar.php?orden=prioritat" class="btn btn-secondary">Prioritat</a>
        <a href="llistar.php?orden=data" class="btn btn-secondary">Data Inici</a>
    </div>
    <?php
    $criteri_ordenacio = "i.Data_Inici DESC";
    if (isset($_GET['orden'])) {
        if ($_GET['orden'] == 'prioridad') {
            $criteri_ordenacio = "FIELD(i.Prioridad, 'Alta', 'Mitja', 'Baixa') ASC";
            } elseif ($_GET['orden'] == 'data') {
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
    <table class="table data-table">
        <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tècnic</th>
                    <th>Descripció</th>
                    <th>Tipus</th>
                    <th>Data Inici</th>
                    <th>Data Final</th>
                    <th>Prioritat</th>
                    <th>Accions</th>
                </tr>
        </thead> 
            <tbody>
                <?php foreach ($incidencies as $incidencia): 
                    $color = "";

                    if ($incidencia['Categoria'] == "Software") {
                        $color = "table-light";
                    } elseif ($incidencia['Categoria'] == "Hardware" || $incidencia['Categoria'] == "Teclat" || $incidencia['Categoria'] == "Ratoli") {
                        $color = "table-primary";
                    } elseif ($incidencia['Categoria'] == "Xarxa") {
                        $color = "table-danger";
                    } elseif ($incidencia['Categoria'] == "Seguretat") {
                        $color = "table-info";
                    } elseif ($incidencia['Categoria'] == "Altres") {
                        $color = "table-dark text-white";
                    }
                ?>
                <tr class="<?php echo $color; ?>">
                    <td><span style="font-family:'DM Mono',monospace;font-size:13px;">#<?= $incidencia['ID_Incidencia'] ?></span></td> 
                    <td><?= $incidencia['ID_Tecnic'] ? htmlspecialchars($incidencia['ID_Tecnic']) : '<span class="text-muted">—</span>' ?></td>
                    <td><?= htmlspecialchars($incidencia['Descripcio']) ?></td>
                    <td><?= htmlspecialchars($incidencia['Categoria']) ?></td>
                    <td><?= htmlspecialchars($incidencia['Data_Inici']) ?></td>
                    <td><?= $incidencia['Data_FIN'] ? htmlspecialchars($incidencia['Data_FIN']) : '<span class="text-muted">—</span>' ?></td>
                    <td><?= htmlspecialchars($incidencia['Prioridad']) ?></td>
                    <td>
                        <a href="esborrar.php?id=<?= $incidencia['ID_Incidencia'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Segur que vols esborrar la incidència #<?= $incidencia['ID_Incidencia'] ?>?')">Esborrar</a>
                        <a href="modificar_incidencia.php?id=<?= $incidencia['ID_Incidencia'] ?>" class="btn btn-sm btn-secondary" onclick="return confirm('Segur que vols modificar la incidència #<?= $incidencia['ID_Incidencia'] ?>?')">Modificar</a>
                    </td>
                </tr>
               <?php endforeach; ?>
            </tbody>
    </table>
    <?php else: ?>
        <div class="alert alert-info">No hi ha incidències a mostrar.</div>
    <?php endif; ?>

    <?php $conn->close(); 
    include_once "footer.php";?>
</div>
<script>
    window.phpMessage = "<?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>";
    window.phpMessageType = "<?= htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') ?>";
</script>
<script src="js/errors_esborrar.js"></script>
</body>
</html>