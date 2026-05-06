<?php
require_once 'connexio.php';

// Verificamos la conexión
if ($conn->connect_error) {
    die("Error de connexió: " . $conn->connect_error);
}

// 1. Lógica de procesamiento de datos (antes de cualquier HTML)
$resultados = [];
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['ID_Incidencia'])) {
    $id_a_buscar = $_POST['ID_Incidencia'];

    // Preparamos la sentencia
    $stmt = $conn->prepare("SELECT ID_Actuacion, descripcio FROM Actuaciones WHERE ID_Incidencia = ?");

    // "i" si el ID es un número entero, "s" si es texto.
    $stmt->bind_param("i", $id_a_buscar);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $resultados[] = $row;
        }
    } else {
        $error_msg = "No s'han trobat actuacions per a aquesta ID.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Veure incidència</title>
</head>
<body>
    <!-- Importante: method="POST" -->
    <form method="POST" action="">
        <label for="ID_Incidencia">ID incidència</label>
        <input type="number" id="ID_Incidencia" name="ID_Incidencia" placeholder="Ej: 123" required>
        <button type="submit">Consultar</button>
    </form>

    <hr>

    <?php
    // Mostrar errores si existen
    if ($error_msg) {
        echo "<p style='color:red;'>$error_msg</p>";
    }

    // Mostrar resultados
    if (!empty($resultados)) {
        echo "<h3>Actuacions trobades:</h3>";
        foreach ($resultados as $actuacio) {
            echo "<p><strong>ID actuació:</strong> " . $actuacio["ID_Actuacion"] .
                 " - <strong>Descripció:</strong> " . htmlspecialchars($actuacio["descripcio"]) . "</p>";
        }
    }
    ?>

</body>
</html>

<?php
// CERRAR la conexión al final de todo el archivo
if (isset($conn)) {
    $conn->close();
}
?>