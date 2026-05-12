$sql = "DELETE FROM INCIDENCIA WHERE ID_Incidencia = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_incidencia);
if ($stmt->execute()) {
    $missatge = "Incidència $id_incidencia eliminada correctament.";
    $missatge_tipus = "success";
} else {
    $missatge = "Error en eliminar: " . $conn->error;
    $missatge_tipus = "error";
}
$stmt->close();
?><!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>GI3P — Esborrar Incidència</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/estils.css">
    <link rel="icon" type="image/jpg" href="img/icon.jpg">
</head>
<body>
<div class="page-content">
     <div class="topbar" style="margin: 15px;">
        <a href="#" onclick="history.back(); return false;" class="btn btn-secondary"> Tornar</a>  
    </div>
    <h1>Esborrar Incidència</h1>
    <?php if (isset($missatge)): ?>
        <div class="alert alert-<?= $missatge_tipus ?>"><?= htmlspecialchars($missatge) ?></div>
    <?php endif; ?>
    <?php
include_once "footer.php";
?>
</body>
</html>
