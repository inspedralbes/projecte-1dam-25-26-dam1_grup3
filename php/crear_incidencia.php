<?php

//Sempre volem tenir una connexió a la base de dades, així que la creem al principi del fitxer
require_once 'connexio.php';
// Un cop inclòs el fitxer connexio.php, ja podeu utilitzar la variable $conn per a fer les consultes a la base de dades.

/**
 * Funció que llegeix els paràmetres del formulari i crea una nova casa a la base de dades.
 * @param mixed $conn
 * @return void
 */
function crear_incidencia($conn)
{
    // Obtenir el noms del formulari
    $id_departament = $_POST['ID_Departament'];
    $nom_dept = $_POST['nom_dept'];
    $data_fin = $_POST['data_fin'];
    $prioridad = $_POST['Prioridad'];
    $descripcio = $_POST['Descripcio'];

    $sql_check = "SELECT ID_Departament, Nom FROM DEPARTAMENT WHERE ID_Departament = ?";
    $stmt_check = $conn->prepare($sql_check);

    if ($stmt_check === false) {
        die("Error en preparar la consulta de verificació: " . $conn->error);
    }

    $stmt_check->bind_param("s", $id_departament);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($row = $result->fetch_assoc()) {
        $nom_dept_bd = $row['Nom'];
        echo "Departament trobat: " . htmlspecialchars($nom_dept_bd) . "<br>";

        $sql = "INSERT INTO INCIDENCIA (ID_Departament, Data_FIN, Prioridad, Descripcio) VALUES (?, ?, ?, ?)";
        $sentencia = $conn->prepare($sql);
        if ($sentencia === false) {
            die("Error en preparar la consulta d'inserció: " . $conn->error);
        }
        $sentencia->bind_param("isss", $id_departament, $data_fin, $prioridad, $descripcio);
        } else {
        echo "<p class='info'>No es pot assignar una incidència en un departament que no existeix.</p>";
        $stmt_check->close();
        return;
    }
    $stmt_check->close();
    if (empty($nom_dept)) {
        echo "<p class='error'>La incidència no pot estar buida.</p>";
        $sentencia->close();
        return;
    }
    if ($sentencia->execute()) {
        echo "<p class='info'>Incidència creada amb èxit!</p>";
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
</head>

<body>
    <div class="container mt-4">
        <h1>Crear incidència</h1>
        <?php

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            crear_incidencia($conn);
        } else {
            ?>
        <form method="POST" action="crear_incidencia.php">
                <div class="mb-3">
                     <label for="ID_Departament" class="form-label">ID departament</label>
                    <input type="text" id="ID_Departament" class="form-control" name="ID_Departament" placeholder="1, 2, 3" required>
                </div>
                <div class="mb-3">
                    <label for="nom_dept" class="form-label">Departament</label>
                    <input type="text" id="nom_dept" class="form-control" name="nom_dept" placeholder="Departament" required>
                </div>
                <div class="mb-3">
                    <label for="descripcio" class="form-label">Descripcio</label>
                    <textarea placeholder="Descripció" class="form-control" name="Descripcio" id="Descripcio" cols="5" required></textarea>
                </div>  
                <div class="mb-3">
                    <label for="Prioridad" class="form-label">Prioritat</label>
                    <select class="form-control" name="Prioridad" id="Prioridad" required>
                        <option value="Baja">Baja</option>
                        <option value="Media">Media</option>
                        <option value="Alta">Alta</option>
                        <option value="Crítica">Crítica</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="data_fin" class="form-label">Data de finalització</label>
                    <input type="text" id="data_fin" class="form-control" name="data_fin" required value = "2024-12-31">
                </div>
                <button class="btn btn-success">Crear</button></div>
        </form>
        <?php
    }
    ?>
    </div>
</body>

</html>

