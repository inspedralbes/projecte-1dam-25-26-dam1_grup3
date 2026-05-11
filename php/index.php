<?php
require_once 'logger.php'
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GI3P — Inici</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/estils.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/jpg" href="img/icon.jpg">
</head>
<body>

<div class="encabezado">
    <img src="img/logo.png" style="height:90px;position:absolute;top:50%;right:32px;transform:translateY(-50%);" alt="Logo">
    <div class="brand">GI3P</div>
    <h1>Institut Pedralbes</h1>
    <p>Sistema de gestió d'incidències</p>
</div>

<div class="page-content" style="max-width: 600px;">
    <p class="text-muted mb-3">Com vols accedir-hi?</p>

    <div class="nav-grid">
        <a href="index_client.php" class="nav-card">
            <div class="nav-label">Afectat</div>
            <div class="nav-desc">Crea o consulta una incidència</div>
        </a>
        <a href="index_tecnic.php" class="nav-card">
            <div class="nav-label">Tècnic</div>
            <div class="nav-desc">Gestiona i resol incidències</div>
        </a>
    </div>
</div>

</body>
</html>