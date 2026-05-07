<?php
require_once 'connexio.php';

// A. Processar l'actualització
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualitzar'])) {
    $id_inci   = $_POST['ID_Incidencia'];
    $id_tecnic = $_POST['ID_Tecnic'];
    $prioritat = $_POST['Prioridad'];
    $id_tipus     = $_POST['ID_Tipo'];

    $sql_update = "UPDATE INCIDENCIA SET ID_Tecnic = ?, Prioridad = ?, ID_Tipo = ? WHERE ID_Incidencia = ?";
    $stmt_upd = $conn->prepare($sql_update);
    $stmt_upd->bind_param("isii", $id_tecnic, $prioritat, $id_tipus, $id_inci);

    if ($stmt_upd->execute()) {
        $missatge = "Incidència $id_inci actualitzada correctament.";
    } else {
        $missatge = "Error en actualitzar: " . $conn->error;
    }
    $stmt_upd->close();
}

// B. Obtenir llistat d'incidències NO RESOLTES
$sql = "SELECT ID_Incidencia, Descripcio, Prioridad FROM INCIDENCIA WHERE Data_FIN IS NULL";
$resultat_incidencies = $conn->query($sql);

// C. Obtenir llistat de tècnics
$tecnics = $conn->query("SELECT ID_Tecnic, Nom FROM TECNIC");


// D. Obtenir llistat de tipus
$tipus = $conn->query("SELECT ID_Tipo, Nom FROM TIPOLOGIA");
?>


<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Modificar Incidències</title>
    <style>
        table { width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f4f4f4; }
        .success { color: green; font-weight: bold; background-color: #e8f5e9; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h2>Modificar Incidències</h2>

    <?php if (isset($missatge)) echo "<p class='success'>$missatge</p>"; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Descripció</th>
                <th>Prioritat Actual</th>
                <th>Assignar Tècnic</th>
                <th>Nova Prioritat</th>
                <th>Tipus</th>
                <th>Acció</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultat_incidencies && $resultat_incidencies->num_rows > 0): ?>
                <?php while ($row = $resultat_incidencies->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['ID_Incidencia']; ?></td>
                    <td><?php echo htmlspecialchars($row['Descripcio']); ?></td>
                    <td><?php echo $row['Prioridad']; ?></td>

                    <!-- UN FORM PER FILA, amb tots els camps dins dels td -->
                    <td>
                        <form method="POST" action="" id="form_<?php echo $row['ID_Incidencia']; ?>">
                            <input type="hidden" name="ID_Incidencia" value="<?php echo $row['ID_Incidencia']; ?>">

                            <select name="ID_Tecnic" required>
                                <option value="">Selecciona tècnic...</option>
                                <?php
                                $tecnics->data_seek(0);
                                while ($t = $tecnics->fetch_assoc()): ?>
                                    <option value="<?php echo $t['ID_Tecnic']; ?>"><?php echo $t['Nom']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </form>
                    </td>

                    <td>
                        <select name="Prioridad" form="form_<?php echo $row['ID_Incidencia']; ?>">
                            <option value="Baja"   <?= $row['Prioridad'] == 'Baja'   ? 'selected' : '' ?>>Baja</option>
                            <option value="Media"  <?= $row['Prioridad'] == 'Media'  ? 'selected' : '' ?>>Media</option>
                            <option value="Alta"   <?= $row['Prioridad'] == 'Alta'   ? 'selected' : '' ?>>Alta</option>
                            <option value="Crítica"<?= $row['Prioridad'] == 'Crítica'? 'selected' : '' ?>>Crítica</option>
                        </select>
                    </td>

                    <td>
                        <!-- Sin form aquí, solo el atributo form= -->
                        <select name="ID_Tipo" form="form_<?php echo $row['ID_Incidencia']; ?>" required>
                            <option value="">Selecciona tipus...</option>
                            <?php
                            $tipus->data_seek(0);
                            while ($t = $tipus->fetch_assoc()): ?>
                                <option value="<?php echo $t['ID_Tipo']; ?>"><?php echo $t['Nom']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </td>

                    <td>
                        <button type="submit" name="actualitzar" form="form_<?php echo $row['ID_Incidencia']; ?>">Assignar</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No hi ha incidències pendents.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>