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

<div class="encabezado">
    <div class="nav_menu">
            <button type="submit" class="nav_btn"><a href="index.php"><img src="img/logo.png" style="height:90px;position:absolute;top:50%;right:32px;transform:translateY(-50%);" alt="Logo"></a></button>
        </div>
    <div class="brand">GI3P</div>
    <h1>Institut Pedralbes</h1>
    <p>Tens un problema? Digueu-nos</p>
</div>
 <div class="topbar">
        <a href="#" onclick="history.back(); return false;" class="btn btn-secondary"> Tornar</a>  
    </div>
<div class="page-content" style="max-width: 760px;">
    <p class="text-muted mb-3">Selecciona una opció:</p>

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

</body>
</html>