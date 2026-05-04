<?php
require_once 'connexio.php';
if ($conn->connect_error) {
    echo "<p>Error de connexió: " . htmlspecialchars($conn->connect_error) . "</p>";
    die("Error de connexió: " . $conn->connect_error);
}
function mostrar_incidencia($conn){
    $id_departament = $_POST['id_departament'];
    $data_fin = $_POST['data_fin'];
    $prioridad = $_POST['prioridad'];
    $descripcio = $_POST['descripcio'];

    $sql = "SELECT * FROM incidencia
    ORDER BY prioridad DESC";
}
if (isset($stmt) && $stmt !== null) {
    $stmt->close();
}

// Y asegúrate de que $conn también se cierre solo si existe
if (isset($conn) && $conn !== null) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veure incidencia</title>
</head>
<body>
    <form>
    <label for="nom_inci">ID incidencia</label>
                    <input type="text" id="id_incidencia" name="id_incidencia" placeholder="XXXXXXXXXX" required>

    <button type="submit">Consultar</button>
    </form>

    <?php
    // 1. Capturamos el valor del input HTML (suponiendo que name="url_input")
    // Usamos el operador de coalescencia nula (??) para evitar errores si el campo viene vacío
    $url_a_buscar = $_POST['id_incidencia'] ?? '';

    if (!empty($url_a_buscar)) {

        // 2. Preparamos la sentencia (Protección contra SQL Injection)
        $stmt = $conn->prepare("SELECT * FROM incidencia WHERE ID_Incidencia = ?");

        // 3. Vinculamos el parámetro ("s" indica que es un string)
        $stmt->bind_param("i", $url_a_buscar);

        // 4. Ejecutamos
        $stmt->execute();

        // 5. Obtenemos los resultados
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { //Qyueda que esto este bien :)
                echo "Método: " . $row['metode'] . " | Usuario ID: " . $row['usuari_id'] . " | Respuesta: " . $row['temps_resposta_ms'] . "ms<br>";
            }
        } else {
            echo "No se encontraron registros para esa ID.";
        }

        $stmt->close();
    } else {
        echo "Por favor, introduce una ID válida.";
    }
    ?>
</body>
</html>
