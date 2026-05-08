<?php
require_once 'connexio.php';

$sql = 'SELECT d.ID_Departament, d.Nom AS departament,
        COUNT(DISTINCT i.ID_Incidencia) AS total_incidencies,
        SUM(a.Temps) AS temps
        FROM DEPARTAMENT d
        LEFT JOIN INCIDENCIA i ON i.ID_Departament = d.ID_Departament
        LEFT JOIN Actuaciones a ON a.ID_Incidencia = i.ID_Incidencia
        GROUP BY d.ID_Departament, d.Nom
        ORDER BY d.Nom';

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>GI3P — Temps per Departament</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/estils.css">
</head>
<body>

<div class="encabezado">
    <img src="img/logo.png" style="height:90px;position:absolute;top:50%;right:32px;transform:translateY(-50%);" alt="Logo">
    <div class="brand">GI3P</div>
    <h1>Institut Pedralbes</h1>
    <p>Temps consumit per departament</p>
</div>

<div class="page-content">
    <div class="topbar">
        <a href="index_client.php" class="btn btn-secondary">← Tornar</a>
    </div>

    <h2 class="page-title">Resum per departament</h2>

    <table class="data-table">
        <thead>
            <tr>
                <th>Departament</th>
                <th>Total incidències</th>
                <th>Temps total</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['departament']) ?></td>
                <td><?= $row['total_incidencies'] ?></td>
                <td><?= floor($row['temps'] / 60) ?>h <?= $row['temps'] % 60 ?>min</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>