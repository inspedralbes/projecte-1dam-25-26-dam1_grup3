<?php 
require_once 'header.php';
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GI3P — Afectat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/estils.css">
    <link rel="icon" type="image/jpg" href="img/icon.jpg">
</head>
<body>

<div class="page-content">
    <div class="topbar" style="margin: 15px;">
        <a href="#" onclick="history.back(); return false;" class="btn btn-secondary"> Tornar</a>  
    </div>
<div class="page-content" style="max-width: 760px, height: 100%;">
    <p class="text-muted mb-3">Selecciona una opció</p>

    <div class="nav-grid">
        <a href="crear_incidencia.php" class="nav-card">
            <div class="nav-label">Crear incidència</div>
            <div class="nav-desc">Reporta un nou problema</div>
        </a>
        <a href="detalls_incidencia.php" class="nav-card">
            <div class="nav-label">Informació incidència</div>
            <div class="nav-desc">Consulta l'estat de la teva incidència</div>
        </a>
    </div>
</div>
</div>
<?php
include_once "footer.php";
?>
</body>
</html>