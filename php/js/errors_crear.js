function validarFechaPosterior(fechaSeleccionada) {
    const avui = new Date();
    
    // 2. Crear el objeto de fecha con el valor del input
    const dataFin = new Date(fechaSeleccionada);

    // 3. Normalizar: Ponemos ambas horas a las 00:00:00 para comparar solo el día
    avui.setHours(0, 0, 0, 0);
    dataFin.setHours(0, 0, 0, 0);

    // 4. Comparación
    if (dataFin <= avui) {
        alert("La data de fin ha de ser posterior al dia d'avui.");
        return false;
    }
    
    console.log("Data vàlida");
    return true;
}