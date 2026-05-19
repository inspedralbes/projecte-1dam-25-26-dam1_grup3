<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
require_once 'logger.php';
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
    <div class="nav_menu">
            <button type="submit" class="nav_btn"><a href="index.php"><img src="img/logo.png" style="height:90px;position:absolute;top:50%;right:32px;transform:translateY(-50%);" alt="Logo"></a></button>
        </div>
    <div class="brand">GI3P</div>
    <h1>Institut Pedralbes</h1>
    <p>Tens un problema? Digueu-nos</p>
</div>  

<div class="container flex-grow-1 d-flex flex-column justify-content-center align-items-center my-4 col-md-10 col-lg-8">
    <p>Com vols accedir-hi?</p>

    <div class="nav-grid d-flex flex-row gap-3 justify-content-center w-100">
        <a href="index_client.php" class="nav-card w-100">
            <div class="nav-label">Afectat</div>
            <div class="nav-desc">Crea o consulta una incidència</div>
        </a>
        <a href="index_tecnic.php" class="nav-card w-100">
            <div class="nav-label">Tècnic</div>
            <div class="nav-desc">Resol incidències</div>
        </a>
        <a href="index_admin.php" class="nav-card w-100">
            <div class="nav-label">Administrador</div>
            <div class="nav-desc">Gestiona incidències</div>
        </a>
    </div>
</div>
    <?php include_once "footer.php";?>
</body>
</html>