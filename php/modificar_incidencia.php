<?php
require_once 'connexio.php';

// A. Processar l'actualització si es rep el formulari
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualitzar'])) {
    $id_inci = $_POST['ID_Incidencia'];
    $tecnic = $_POST['ID_Tecnic'];
    $prioritat = $_POST['Prioridad'];

    $sql_update = "UPDATE INCIDENCIA SET ID_Tecnic = ?, prioridad = ? WHERE id = ?";
    $stmt_upd = $conn->prepare($sql_update);
    $stmt_upd->bind_param("isi", $tecnic, $prioritat, $id_inci);

    if ($stmt_upd->execute()) {
        $missatge = "Incidència $id_inci actualitzada correctament.";
    } else {
        $missatge = "Error en actualitzar: " . $conn->error;
    }
    $stmt_upd->close();
}

// B. Obtenir llistat d'incidències NO RESOLTES
// Suposem que 'estat' != 'resolta'
$sql = "SELECT ID_Incidencia, descripcio, prioridad FROM INCIDENCIA WHERE Data_FIN IS NULL";
$resultat_incidencies = $conn->query($sql);

// C. Obtenir llistat de tècnics per al desplegable
$tecnics = $conn->query("SELECT ID_Tecnic, Nom FROM TECNIC");
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Modificar Incidències</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        .success { color: green; font-weight: bold; }
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
                <th>Acció</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultat_incidencies->fetch_assoc()): ?>
            <tr>
                <form method="POST" action="">
                    <td>
                        <?php echo $row['ID_Incidencia']; ?>
                        <input type="hidden" name="id_incidencia" value="<?php echo $row['ID_Incidencia']; ?>">
                    </td>
                    <td><?php echo htmlspecialchars($row['Descripcio']); ?></td>
                    <td><?php echo $row['Prioridad']; ?></td>
                    <td>
                        <select name="tecnic_assignat" required>
                            <option value="">Selecciona tècnic...</option>
                            <?php
                            $tecnics->data_seek(0); // Reiniciar el punter dels tècnics
                            while ($t = $tecnics->fetch_assoc()): ?>
                                <option value="<?php echo $t['ID_Tecnic']; ?>"><?php echo $t['Nom']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </td>
                    <td>
                        <select name="prioritat">
                            <option value="Baja" <?= $row['Prioridad'] == 'Baja' ? 'selected' : '' ?>>Baja</option>
                            <option value="Media" <?= $row['Prioridad'] == 'Media' ? 'selected' : '' ?>>Media</option>
                            <option value="Alta" <?= $row['Prioridad'] == 'Alta' ? 'selected' : '' ?>>Alta</option>
                            <option value="Crítica" <?= $row['Prioridad'] == 'Crítica' ? 'selected' : '' ?>>Crítica</option>
                        </select>
                    </td>
                    <td>
                        <button type="submit" name="actualitzar">Assignar</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
