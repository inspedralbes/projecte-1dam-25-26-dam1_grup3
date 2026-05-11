<?php
require_once 'connexio.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function crear_incidencia($conn)
{
    $id_departament = $_POST['ID_Departament'];
    $nom_dept       = $_POST['nom_dept'];
    $data_fin       = $_POST['data_fin'];
    $prioridad      = $_POST['Prioridad'];
    $descripcio     = $_POST['Descripcio'];

    $sql_check = "SELECT ID_Departament, Nom FROM DEPARTAMENT WHERE ID_Departament = ?";
    $stmt_check = $conn->prepare($sql_check);

    if ($stmt_check === false) {
        die("Error en preparar la consulta de verificació: " . $conn->error);
    }

    $stmt_check->bind_param("s", $id_departament);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($row = $result->fetch_assoc()) {
        if (empty($nom_dept)) {
            echo "<div class='alert alert-error'>La incidència no pot estar buida.</div>";
            $stmt_check->close();
            return;
        }

        $sql = "INSERT INTO INCIDENCIA (ID_Departament, Data_FIN, Prioridad, Descripcio) VALUES (?, ?, ?, ?)";
        $sentencia = $conn->prepare($sql);
        if ($sentencia === false) {
            die("Error en preparar la consulta d'inserció: " . $conn->error);
        }
        $sentencia->bind_param("isss", $id_departament, $data_fin, $prioridad, $descripcio);

        if ($sentencia->execute()) {
            echo "<div class='alert alert-success'>Incidència creada amb èxit! Departament: <strong>" . htmlspecialchars($row['Nom']) . "</strong></div>";
        } else {
            echo "<div class='alert alert-error'>Error al crear la incidència: " . htmlspecialchars($sentencia->error) . "</div>";
        }
        $sentencia->close();
    } else {
        echo "<div class='alert alert-error'>No es pot assignar una incidència en un departament que no existeix.</div>";
    }
    $stmt_check->close();
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

<div class="encabezado">
    <img src="img/logo.png" style="height:90px;position:absolute;top:50%;right:32px;transform:translateY(-50%);" alt="Logo">
    <div class="brand">GI3P</div>
    <h1>Institut Pedralbes</h1>
    <p>Nova incidència</p>
</div>

<div class="page-content" style="max-width:640px;">
    <div class="topbar">
        <a href="index_client.php" class="btn btn-secondary">← Tornar</a>
    </div>

    <h2 class="page-title">Crear incidència</h2>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <?php crear_incidencia($conn); ?>
        <a href="crear_incidencia.php" class="btn btn-secondary" style="margin-top:16px;">← Nova incidència</a>

    <?php else: ?>
        <div class="form-card">
            <form method="POST" action="crear_incidencia.php">

                <div class="form-group">
                    <label class="form-label" for="ID_Departament">ID departament</label>
                    <input type="text" id="ID_Departament" class="form-control" name="ID_Departament"
                           placeholder="Ex: 1, 2, 3" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="nom_dept">Nom del departament</label>
                    <input type="text" id="nom_dept" class="form-control" name="nom_dept"
                           placeholder="Ex: Informàtica" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="Descripcio">Descripció</label>
                    <textarea class="form-control" name="Descripcio" id="Descripcio"
                              rows="4" placeholder="Descriu la incidència…" required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="Prioridad">Prioritat</label>
                    <select class="form-control" name="Prioridad" id="Prioridad" required>
                        <option value="Baja">Baja</option>
                        <option value="Media" selected>Media</option>
                        <option value="Alta">Alta</option>
                        <option value="Crítica">Crítica</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="data_fin">Data de finalització</label>
                    <input type="date" id="data_fin" class="form-control" name="data_fin"
                           required value="2024-12-31">
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Crear incidència</button>
            </form>
        </div>
    <?php endif; ?>
</div>

</body>
</html>