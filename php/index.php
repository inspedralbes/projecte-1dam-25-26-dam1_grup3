<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inici</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estils.css">
</head>

<body>
    <div class = "encabezado">
        <h1 class="text-center">GI3P</h1>
        <h1 class="text-center">Institut Pedralbes</h1>
        <p class="text-center">Tens un problema? Digueu-nos</p>
        <p>Les variables s'han d'utilitzar per a definir la cadena de connexió independentment del codi</p>
    </div>
    <?php
    $v1 = getenv('VAR1') ?: 'Ups, variable no definida';
    $v2 = getenv('VAR2') ?: 'Ups, variable no definida';
    echo "<p>El valor de la variable d'entorn VAR1 és: <strong>$v1</strong> </p>";
    echo "<p>El valor de la variable d'entorn VAR2 és: <strong>$v2</strong></p>";
    ?>
    <footer id="menu">
        <table>
            <tr>
                <td>
                    <button type="submit"><a href="crear_incidencia.php">Crear incidencia</a></button>
                </td>
                <td>
                    <button type="submit"><a href="">Info incidencia</a></button>
                </td>
                <td>
                    <button type="submit"><a href="">Temps consumit per departament</a></button>
                </td>
            </tr>
        </table>
    </footer>
</body>

</html>