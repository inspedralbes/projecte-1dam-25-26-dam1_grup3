function validarTecnic(formulari) {
    const formData = new FormData(formulari);
    const dades = Object.fromEntries(formData.entries());
    if (!dades.ID_Tecnic) {
        alert("Si us plau, seleccioni un tècnic.");
        return false;
    }
    if (!dades.ID_Incidencia) {
        alert("No es troba l'ID de la incidència.");
        return false;
    }
    const confirmar = confirm(`Assignar tècnic a la incidència #${dades.ID_Incidencia}?`);
    
    if (confirmar) {
        return true;
    } else {
        return false
    }
}
var missatge_tipus = "";
if (missatge.length > 0) {
    missatge_tipus = "failure";
    alert("No es pot deixar el missatge buit.");
} else {
    missatge_tipus = "success";
    alert("Incidencia modificada correctament!")
}
