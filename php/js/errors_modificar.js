function validarTecnic(formulario) {
    const formData = new FormData(formulario);
    const datos = Object.fromEntries(formData.entries());
    if (!datos.ID_Tecnic) {
        alert("Por favor, selecciona un técnico.");
        return false;
    }
    if (!datos.ID_Incidencia) {
        alert("Error crítico: No se encuentra el ID de la incidencia.");
        return false;
    }
    const confirmar = confirm(`¿Asignar técnico a la incidencia #${datos.ID_Incidencia}?`);
    
    if (confirmar) {
        return true;
    } else {
        return false
    }
}