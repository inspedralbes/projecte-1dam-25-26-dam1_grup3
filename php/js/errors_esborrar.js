// <?php if (isset($missatge)): ?>
   //     <div class="alert alert-<?= $missatge_tipus ?>"><?= htmlspecialchars($missatge) ?></div>
 //   <?php endif; ?>
//    <?php
var missatge_tipus = "";
if (missatge.length == 0) {
    missatge_tipus = "failure";
    alert("No es pot deixar el missatge buit.");
} else {
    missatge_tipus = "success";
    alert("Incidencia esborrada correctament!");
}