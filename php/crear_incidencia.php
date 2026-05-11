<?php
require_once 'connexio.php';

//Sempre volem tenir una connexió a la base de dades, així que la creem al principi del fitxer
function crear_incidencia($conn)
{
    // 1. Recollir i validar que les dades existeixin
    $id_departament = isset($_POST['ID_Departament']) ? intval($_POST['ID_Departament']) : 0;
    $data_fin = $_POST['Data_FIN'] ?? '';
    $prioridad = $_POST['Prioridad'] ?? '';
    $descripcio = $_POST['Descripcio'] ?? '';

    // 2. Verificar si el departament existeix
    $sql_check = "SELECT ID_Departament FROM DEPARTAMENT WHERE ID_Departament = ?";
    $stmt_check = $conn->prepare($sql_check);
    
    if ($stmt_check === false) {
        die("Error en la consulta de verificació: " . $conn->error);
    }

    $stmt_check->bind_param("i", $id_departament);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows === 0) {
        // SI NO HI HA RESULTATS, PARAMOS AQUÍ
        echo "<p class='info'>No es pot assignar una incidència en un departament que no existeix (ID: $id_departament).</p>";
        $stmt_check->close();
        return;
    }
    $stmt_check->close();

    // 3. Validar que la descripció no estigui buida (usant la variable correcta)
    if (empty($descripcio)) {
        echo "<p class='error'>La descripció de la incidència no pot estar buida.</p>";
        return;
    }

    // 4. Inserir la incidència
    $sql = "INSERT INTO INCIDENCIA (ID_Departament, Data_FIN, Prioridad, Descripcio) VALUES (?, ?, ?, ?)";
    $sentencia = $conn->prepare($sql);
    
    if ($sentencia === false) {
        die("Error en la consulta d'inserció: " . $conn->error);
    }

    // "isss" -> i (integer), s (string), s (string), s (string)
    $sentencia->bind_param("isss", $id_departament, $data_fin, $prioridad, $descripcio);

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
    <title>GI3P — Crear incidència</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/estils.css">
    <link rel="icon" type="image/jpg" href="img/icon.jpg">
</head>
<body>
    <div class = "encabezado">
        <div class="nav_menu">
            <button type="submit" class="nav_btn"><a href="index.php"><img src="img/logo.png" style="height:90px;position:absolute;top:50%;right:32px;transform:translateY(-50%);" alt="Logo"></a></button>
            <div class="brand">GI3P</div>
            <h1>Institut Pedralbes</h1>
        </div>
    </div>
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
                    <label for="descripcio" class="form-label">Descripcio</label>
                    <textarea placeholder="Descripció" class="form-control" name="Descripcio" id="Descripcio" cols="5" required></textarea>
                </div>  
                <div class="mb-3">
                    <label for="Prioridad" class="form-label">Prioritat</label>
                    <select class="form-control" name="Prioridad" id="Prioridad" required>
                        <option value="Baja">Baja</option>
                        <option value="Media" selected>Media</option>
                        <option value="Alta">Alta</option>
                        <option value="Crítica">Crítica</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="data_fin" class="form-label">Data de finalització</label>
                    <input type="text" id="Data_FIN" class="form-control" name="Data_FIN" required value = "2024-12-31">
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Crear incidència</button>
            </form>
        </div>
    <?php } ?>
</div>

</body>
</html>