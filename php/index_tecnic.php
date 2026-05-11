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

    <div class="encabezado">
        <img src="img/logo.png" style="height:90px;position:absolute;top:50%;right:32px;transform:translateY(-50%);" alt="Logo">
        <div class="brand">GI3P</div>
        <h1>Institut Pedralbes</h1>
        <p>Àrea de tècnics</p>
    </div>

    <div class="page-content" style="max-width: 600px;">
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
        </div>
    </div>
    </body>
</html>