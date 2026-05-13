<?php
include_once 'header.php';
require_once 'connexio.php';
require_once 'logger.php';


$sql = 'SELECT d.ID_Departament, d.Nom AS departament,
        COUNT(DISTINCT i.ID_Incidencia) AS total_incidencies,
        SUM(a.Temps) AS temps
        FROM DEPARTAMENT d
        LEFT JOIN INCIDENCIA i ON i.ID_Departament = d.ID_Departament
        LEFT JOIN Actuacions a ON a.ID_Incidencia = i.ID_Incidencia
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
    <link rel="icon" type="image/jpg" href="img/icon.jpg">
</head>
<body>

<div class="page-content">
    <div class="topbar" style="margin: 15px;">
         <a href="#" onclick="history.back(); return false;" class="btn btn-secondary"> Tornar</a>  
    </div>
    <h1>Resum per departament</h1>

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
<?php
include_once "footer.php";
?>
</body>
</html>