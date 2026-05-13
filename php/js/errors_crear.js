function validarFechaPosterior(fechaSeleccionada) {
    const avui = new Date();
    const dataFin = new Date(fechaSeleccionada);
    avui.setHours(0, 0, 0, 0);
    dataFin.setHours(0, 0, 0, 0);
    if (dataFin <= avui) {
        alert("La data de fin ha de ser posterior al dia d'avui.");
        return false;
    }
    
    console.log("Data vàlida");
    return true;
}