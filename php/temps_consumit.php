<?php
include_once 'header.php';
require_once 'logger.php';
require_once 'connexio.php';


$resultat = $conn->query("
    SELECT d.Nom AS nom, 
           COALESCE(SUM(a.Temps), 0) AS temps,
           COUNT(DISTINCT i.ID_Incidencia) AS numInc
    FROM DEPARTAMENT d
    LEFT JOIN INCIDENCIA i ON i.ID_Departament = d.ID_Departament
    LEFT JOIN Actuaciones a ON a.ID_Incidencia = i.ID_Incidencia
    GROUP BY d.ID_Departament, d.Nom
    ORDER BY d.Nom
");

$departaments = $resultat->fetch_all(MYSQLI_ASSOC);
$tempsArray = array();
$deptsArray = array();
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GI3P — Temps per Departament</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estils.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
</head>
<body>

<div class="page-content">
    <div class="topbar d-flex justify-content-start w-100" style="padding: 15px;margin-bottom: 0px;">
        <a href="javascript:history.back()" class="btn btn-secondary"> Tornar</a>
    </div>
    <h1>Estadístiques per departament</h1>

    <div style="max-width: 380px; margin: 0 auto 40px auto;">
        <canvas id="myChart"></canvas>
    </div>

    <table class="data-table">
        <thead>
        <tr>
            <th>Departament</th>
            <th>Temps</th>
            <th>Núm. Incidències</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($departaments as $unDepartament) {
            $tempsArray[] = (int) $unDepartament["temps"];
            $deptsArray[] = $unDepartament["nom"]; ?>
            <tr>
                <td><?php echo htmlspecialchars($unDepartament["nom"]) ?></td>
                <td><?php echo floor($unDepartament["temps"] / 60) ?>h <?php echo $unDepartament["temps"] % 60 ?>min</td>
                <td><?php echo $unDepartament["numInc"] ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>

<script>
    const accentColor = getComputedStyle(document.documentElement)
        .getPropertyValue('--accent').trim();
    Chart.defaults.font.family = "'DM Sans', sans-serif";
    Chart.defaults.color = '#6b6b66';

    const ctx = document.getElementById('myChart');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($deptsArray); ?>,
            datasets: [{
                label: 'Minuts',
                data: <?php echo json_encode($tempsArray); ?>,
                borderWidth: 2,
                borderColor: '#fff',
                backgroundColor: [
                    'rgba(28,200,138,0.85)',
                    'rgba(54,185,204,0.85)',
                    'rgba(246,194,62,0.85)',
                    'rgba(214,63,63,0.85)',
                    'rgba(133,95,230,0.85)',
                    'rgba(255,140,66,0.85)',
                    'rgba(60,179,113,0.85)',
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 16, boxWidth: 12, font: { size: 13 } }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = ((ctx.parsed / total) * 100).toFixed(1);
                            return ` ${ctx.label}: ${ctx.parsed} min (${pct}%)`;
                        }
                    }
                }
            }
        }
    });
</script>

<?php $conn->close(); ?>
 <?php
    include_once "footer.php";
    ?>
</body>
</html>