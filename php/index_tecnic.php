<?php
require_once 'logger.php';
require_once 'header.php';
?>
<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GI3P — Tècnic</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/estils.css">
        <link rel="icon" type="image/jpg" href="img/icon.jpg">
    </head>
    <body>
    <div class="page-content">
        <div class="topbar" style="margin: 15px;">
            <a href="#" onclick="history.back(); return false;" class="btn btn-secondary"> Tornar</a>
        </div>
    <div class="page-content" style="max-width: 600px, height: 100%;">
        <p class="text-muted mb-3">Selecciona una opció</p>

        <div class="nav-grid">
            <a href="detalls_incidencia.php" class="nav-card">
                <div class="nav-label">Informació incidència</div>
                <div class="nav-desc">Consulta detalls i actuacions</div>
            </a>
            <a href="modificar_incidencia.php" class="nav-card">
                <div class="nav-label">Modificar incidència</div>
                <div class="nav-desc">Assigna tècnic, prioritat i tipus</div>
            </a>
            <a href="temps_consumit.php" class="nav-card">
                <div class="nav-label">Temps consumit</div>
                <div class="nav-desc">Temps per departament</div>
            </a>
            <a href="afegir_actuacio.php" class="nav-card">
                <div class="nav-label">Afegir actuació</div>
                <div class="nav-desc">Registra un nou temps d'actuació</div>
            </a>
        </div>
    </div>
    </div>
     <?php
    include_once "footer.php";
    ?>
    </body>
</html>