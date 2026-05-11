<?php

//Sempre volem tenir una connexió a la base de dades, així que la creem al principi del fitxer
require_once 'connexio.php';
require_once 'logger.php';
// Un cop inclòs el fitxer connexio.php, ja podeu utilitzar la variable $conn per a fer les consultes a la base de dades.

/**
 * Funció que llegeix els paràmetres del formulari i crea una nova casa a la base de dades.
 * @param mixed $conn
 * @return void0
 */

// Mostrar todos los errores y advertencias en pantalla
function afegir_actuacio($conn)
{
    // Obtenir el noms del formulari
    $id_incidencia = $_POST['ID_Incidencia'];
    $fin = $_POST['FIN'];
    $visible = $_POST['Visible'];
    $descripcio = $_POST['Descripcio'];
    $temps = $_POST['Temps'];

    $sql_check = "SELECT ID_Departament FROM DEPARTAMENT WHERE ID_Departament = ?";
    $stmt_check = $conn->prepare($sql_check);

    if ($stmt_check === false) {
        die("Error en preparar la consulta de verificació: " . $conn->error);
    }

    $stmt_check->bind_param("i", $id_incidencia);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($row = $result->fetch_assoc()) {

        $sql = "INSERT INTO Actuaciones (ID_Incidencia, FIN, Descripcio, Visible, Temps, Data_Actuacion) VALUES (?, ?, ?, ?, ?, NOW())";
        $sentencia = $conn->prepare($sql);
        if ($sentencia === false) {
            die("Error en preparar la consulta d'inserció: " . $conn->error);
        }
        $sentencia->bind_param("iisid", $id_incidencia, $fin, $descripcio, $visible, $temps);
        } else {
        echo "<p class='info'>No es pot assignar una actuació en unaincidencia que no existeix.</p>";
        $stmt_check->close();
        return;
    }
    $stmt_check->close();
    if (empty($id_incidencia)) {
        echo "<p class='error'>La actuació no pot estar buida.</p>";
        $sentencia->close();
        return;
    }
    if ($sentencia->execute()) {
        echo "<p class='info'>Actuació creada amb èxit!</p>";
    } else {
        echo "<p class='error'>Error al crear la incidència: " . htmlspecialchars($sentencia->error) . "</p>";
    }
    $sentencia->close();
}
    // Comprovar si el nom no està buit
    // Si l'html està ben escrit això no podria passar en els usuaris normals
    // Igualment SEMPRE s'ha de comprovar tot al backend ja que no tots els usuaris
    // són "bones persones" i des de les web tools es pot canviar tot el front per exemple.

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear incidencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/estils.css">
    <link rel="icon" type="image/jpg" href="img/icon.jpg">
</head>

<body>
    <div class="container mt-4">
        <h1>Afegir actuació</h1>
        <?php

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            afegir_actuacio($conn);
        } else {
            ?>
        <form method="POST" action="afegir_actuacio.php">
                <div class="mb-3">
                     <label for="ID_Incidencia" class="form-label">ID Incidencia</label>
                    <input type="text" id="ID_Incidencia" class="form-control" name="ID_Incidencia" placeholder="1, 2, 3" required>
                </div>

                <div class="mb-3">
                    <label for="descripcio" class="form-label">Descripcio</label>
                    <textarea placeholder="Descripció" class="form-control" name="Descripcio" id="Descripcio" cols="5" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="Temps" class="form-label">Temps en minuts</label>
                    <input type="text" id="Temps" class="form-control" name="Temps" placeholder="10.00" required>
                </div>

                <div class="mb-3">
                    <input type="hidden" id="Visible" name="Visible" value="0">
                    <input type="checkbox" id="Visible" name="Visible" value="1"> Visible
                </div>
                <div class="mb-3">
                    <input type="hidden" name="FIN" id="FIN" value="0">
                    <input type="checkbox" name="FIN" id="FIN" value="1"> Finalitzat
                </div>
                <button class="btn btn-success">Crear</button></div>
        </form>
        <?php
    }
    ?>
    </div>
</body>

</html>

