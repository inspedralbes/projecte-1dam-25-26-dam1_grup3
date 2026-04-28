<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inici</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>

<body>
    <h1>GI3P</h1>
    <h1>Institut Pedralbes</h1>
    <p class="text-center">Tens un problema? Digueu-nos</p>
    <p>Les variables s'han d'utilitzar per a definir la cadena de connexió independentment del codi</p>
    <?php
    $v1 = getenv('VAR1') ?: 'Ups, variable no definida';
    $v2 = getenv('VAR2') ?: 'Ups, variable no definida';
    echo "<p>El valor de la variable d'entorn VAR1 és: <strong>$v1</strong> </p>";
    echo "<p>El valor de la variable d'entorn VAR2 és: <strong>$v2</strong></p>";
    ?>
    <div id="menu">
            <button type="submit"><a href="crear_incidencia.php">Crear incidencia</a></button>
            <button type="submit"><a href="">Info incidencia</a></button>
            <button type="submit"><a href="">Temps consumit per departament</a></button>
    </div>
</body>

</html>