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
    // Obtenir el nom de la casa del formulari
    $id_departament = $_POST['id_departament'];
    $data_fin = $_POST['data_fin'];
    $prioridad = $_POST['prioridad'];
    $descripcio = $_POST['descripcio'];

    // Comprovar si el nom no està buit
    // Si l'html està ben escrit això no podria passar en els usuaris normals
    // Igualment SEMPRE s'ha de comprovar tot al backend ja que no tots els usuaris
    // són "bones persones" i des de les web tools es pot canviar tot el front per exemple.
    if (empty($nom)) {
        echo "<p class='error'>La incidencia no pot estar buida.</p>";
        return;
    }

    // Preparar la consulta SQL per inserir una nova casa
    

    // Executar la consulta i comprovar si s'ha inserit correctament
    if ($stmt->execute()) {
        echo "<p class='info'>Incidencia creada amb èxit!</p>";
    } else {
        echo "<p class='error'>Error al crear la incidencia: " . htmlspecialchars($stmt->error) . "</p>";
    }
    if ($id_departament == 'id_departament'){ 
        $sql = "INSERT INTO incidencia (data_fin, prioridad, descripcio) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);  //La variable $conn la tenim per haver inclòs el fitxer connexio.php
        $stmt->bind_param("sss", $id_departament, $data_fin, $prioridad, $descripcio);
    } else {
        echo "<p class='info'>No es pot assignar una incidencia en departament que no existeix.</p>";
    }
    // Tancar la declaració i la connexió
    $stmt->close();

}


?>
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear incidencia</title>
</head>

<body>
    <h1>Crear incidencia</h1>
    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Si el formulari s'ha enviatc (mètode POST), cridem a la funció per crear la casa
        crear_incidencia($conn);
    } else {
        //Mostrem el formulari per crear una nova casa
        //Tanquem el php per poder escriure el codi HTML de forma més còmoda.
        ?>
        <form method="POST" action="crear_incidencia.php">
                <label for="nom_dept">Depart</label>
                <input type="text" id="id_departament" name="id_departament" placeholder="XXXXXXXXXX">
                <label for="descripcio">Descripcio</label>
                <textarea name="descripcio" placeholder="INFO">
        </form>
        <?php
        //Tanquem l'else
    }
    ?>
    <div id="menu">
            <button type="submit"><a href="">Info incidencia</a></button>
            <button type="submit"><a href="">Temps consumit per departament</a></button>
    </div>
</body>

</html>

