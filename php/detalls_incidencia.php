<?php
require_once 'connexio.php';
require_once 'logger.php';
include_once "header.php";

if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$resultados = [];
$error_msg  = "";
$busqueda_realizada = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['ID_Incidencia'])) {
    $busqueda_realizada = true;
    $id_a_buscar = $_POST['ID_Incidencia'];

    $sql = "SELECT ID_Actuacion, Descripcio, Data_Actuacion, Temps, FIN
        FROM Actuaciones
        WHERE ID_Incidencia = ? AND Visible = 1
        ORDER BY Data_Actuacion ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_a_buscar);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $resultados[] = $row;
        }
    } else {
        $error_msg = "No hi ha actuacions visibles per a la incidència #$id_a_buscar.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>GI3P — Veure incidència</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/estils.css">
    <link rel="icon" type="image/jpg" href="img/icon.jpg">
</head>
<body>
<div class="page-content" style="max-width: 50%,height: 100%;">
    <div class="topbar d-flex justify-content-start w-100" style="padding: 15px;margin-bottom: 0px;">
        <a href="index_client.php" class="btn btn-secondary"> Tornar</a>
    </div>
    <h1>Consultar incidència</h1>

    <div class="form-card mb-3">
        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label" for="ID_Incidencia">ID de la incidència</label>
                <input type="number" class="form-control" id="ID_Incidencia" name="ID_Incidencia"
                       placeholder="Ex: 123" required
                       value="<?= isset($_POST['ID_Incidencia']) ? (int)$_POST['ID_Incidencia'] : '' ?>">
            </div>
            <button type="submit" class="btn btn-primary">Consultar</button>
        </form>
    </div>

    <?php if ($error_msg): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>
<?php if (!empty($resultados)): ?>
        <h3 style="font-size:16px;font-weight:700;margin-bottom:12px;">
            Actuacions trobades <span style="color:var(--text-muted);font-weight:400;">(<?= count($resultados) ?>)</span>
        </h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID actuació</th>
                    <th>Descripció</th>
                    <th>Data</th>
                    <th>Temps consumit</th>
                    <th>Estat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultados as $actuacio): ?>
                <tr>
                    <td>
                        <span style="font-family:'DM Mono',monospace;font-size:13px;">
                            #<?= $actuacio['ID_Actuacion'] ?? 'N/A' ?>
                        </span>
                    </td>

                    <td><?= htmlspecialchars($actuacio['Descripcio'] ?? '') ?></td>

                    <td>
                        <span class="badge" style="color: #000000; padding: 5px 10px; border-radius: 4px;">
                            <?= htmlspecialchars($actuacio['Data_Actuacion'] ?? 'Sense data') ?>
                        </span>
                    </td>

                    <td>
                        <span class="badge" style="color: #000000;  padding: 5px 10px; border-radius: 4px;">
                            <?= htmlspecialchars($actuacio['Temps'] ?? '0') ?> minuts
                        </span>
                    </td>
                    <td>
                        <?= htmlspecialchars($actuacio['FIN'] ?? 'No finalitzada') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>

<?php
if (isset($conn)) {
    $conn->close();
}
include_once "footer.php";
?>