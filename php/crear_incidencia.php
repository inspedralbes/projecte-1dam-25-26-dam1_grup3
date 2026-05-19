<?php
require_once 'connexio.php';
require_once 'logger.php';
include_once "header.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function crear_incidencia($conn)
{
    if (!$conn) return "<p class='error'>Error: No hi ha connexió a la base de dades.</p>";

    $id_departament = isset($_POST['ID_Departament']) ? intval($_POST['ID_Departament']) : 0;
    $descripcio = trim($_POST['Descripcio'] ?? '');

    if ($id_departament <= 0) {
        return "<div class='alert alert-danger'>L'ID del departament ha de ser un número vàlid.</div>";
    }

    $sql_check = "SELECT ID_Departament FROM DEPARTAMENT WHERE ID_Departament = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id_departament);
    $stmt_check->execute();

    if ($stmt_check->get_result()->num_rows === 0) {
        $stmt_check->close();
        return "<div class='alert alert-danger'>No es pot assignar una incidència en un departament que no existeix (ID: $id_departament).</div>";
    }
    $stmt_check->close();

    $sql = "INSERT INTO INCIDENCIA (ID_Departament, Descripcio, Data_Inici) VALUES (?, ?, NOW())";
    $sentencia = $conn->prepare($sql);
    $sentencia->bind_param("is", $id_departament, $descripcio);

    if ($sentencia->execute()) {
        $id = $conn->insert_id;
        $resultado = "<div class='alert alert-success'>
                        Incidència creada amb èxit! <br>
                        <strong>ID de la incidència: #$id</strong>
                      </div>";
    } else {
        $resultado = "<div class='alert alert-danger'>Error: " . htmlspecialchars($sentencia->error) . "</div>";
    }
    $sentencia->close();
    return $resultado;
}
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
    <div class="page-content" style="max-width: 50%; height: 100%;">
        <div class="topbar" style="margin: 15px;">
            <a href="index_client.php" class="btn btn-secondary">← Tornar</a>
        </div>
        <div class="container mt-4">
            <h1>Crear incidència</h1>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['ID_Departament']) && !empty($_POST['Descripcio'])) {
                echo crear_incidencia($conn);
            }
            ?>
            <div class="form-card mb-3">
                <form method="POST" action="crear_incidencia.php">
                    <div class="mb-3">
                        <label for="ID_Departament" class="form-label">ID departament</label>
                        <input type="text" id="ID_Departament" class="form-control" name="ID_Departament" placeholder="1, 2, 3" required
                            oninvalid="this.setCustomValidity('L\'ID del departament no pot estar buit.')"
                            oninput="this.setCustomValidity('')">
                    </div>
                    <div class="mb-3">
                        <label for="Descripcio" class="form-label">Descripció</label>
                        <textarea placeholder="Descripció" class="form-control" name="Descripcio" id="Descripcio" rows="3" required
                            oninvalid="this.setCustomValidity('La descripció no pot estar buida.')"
                            oninput="this.setCustomValidity('')"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Crear incidència</button>
                </form>
            </div>
        </div>
    </div>
    <?php include_once "footer.php"; ?>
    <script src="js/errors_crear.js"></script>
</body>
</html>
