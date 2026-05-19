function validarFormulario(formulari) {
    const idInci = f.querySelector('input[name="ID_Incidencia"]').value;
    const idTecnic = f.querySelector('select[name="ID_Tecnic"]').value;
    const prioritat = document.querySelector(`select[name="Prioridad"][form="form_${idInci}"]`).value;
    const idTipo = document.querySelector(`select[name="ID_Tipo"][form="form_${idInci}"]`).value;
    if (!idTecnic || idTecnic === "") {
        alert("Has de seleccionar un tècnic per a la incidència #" + idInci);
        return false;
    }
    if (!idTipo || idTipo === "") {
        alert("Has de seleccionar un tipus de categoria.");
        return false;
    }
    const confirmar = confirm(`Estàs segur que vols actualitzar la incidència #${idInci}?\n\n- Tècnic: ${idTecnic}\n- Prioritat: ${prioritat}\n- Tipus: ${idTipo}`);
    
    if (confirmar) {
        return true;
    } else {
        return false;
    }
};