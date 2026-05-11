<?php
require_once 'connexio.php';
require_once 'logger.php';

if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

$resultados = [];
$error_msg  = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['ID_Incidencia'])) {
    $id_a_buscar = $_POST['ID_Incidencia'];

    $stmt = $conn->prepare("SELECT ID_Actuacion, descripcio FROM Actuaciones WHERE ID_Incidencia = ?");
    $stmt->bind_param("i", $id_a_buscar);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $resultados[] = $row;
        }
    } else {
        $error_msg = "No s'han trobat actuacions per a aquesta ID.";
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

<div class="encabezado">
    <img src="img/logo.png" style="height:90px;position:absolute;top:50%;right:32px;transform:translateY(-50%);" alt="Logo">
    <div class="brand">GI3P</div>
    <h1>Institut Pedralbes</h1>
    <p>Detalls de la incidència</p>
</div>

<div class="page-content" style="max-width:640px;">
    <div class="topbar">
        <a href="index_client.php" class="btn btn-secondary">← Tornar</a>
    </div>

    <h2 class="page-title">Consultar incidència</h2>

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
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultados as $actuacio): ?>
                <tr>
                    <td><span style="font-family:'DM Mono',monospace;font-size:13px;">#<?= $actuacio['ID_Actuacion'] ?></span></td>
                    <td><?= htmlspecialchars($actuacio['descripcio']) ?></td>
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
?>