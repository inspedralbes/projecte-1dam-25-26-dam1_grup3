<?php
require_once 'logger.php';
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
    <div class="topbar d-flex justify-content-start w-100" style="padding: 15px;margin-bottom: 0px;">
         <a href="index.php" class="btn btn-secondary"> Tornar</a>
    </div>
<div class="container flex-grow-1 d-flex flex-column justify-content-center align-items-center my-4 col-12 col-md-11 col-lg-10">
    <p>Selecciona una opció</p>

    <div class="nav-grid d-flex flex-row gap-3 justify-content-center w-100" style="margin-top: 24px;">
        <a href="crear_incidencia.php" class="nav-card w-100">
            <div class="nav-label">Crear incidència</div>
            <div class="nav-desc">Reporta un nou problema</div>
        </a>
        <a href="detalls_incidencia.php" class="nav-card w-100">
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