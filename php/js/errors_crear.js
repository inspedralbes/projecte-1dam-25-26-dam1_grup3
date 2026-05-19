document.addEventListener('DOMContentLoaded', function() {
    const formulario = document.querySelector('form');

    formulario.addEventListener('submit', function(event) {
        const idDepartament = document.getElementById('ID_Departament').value.trim();
        const descripcio = document.getElementById('Descripcio').value.trim();
        const dataFin = document.getElementById('Data_FIN').value;
        if (descripcio.length === 0) {
            alert("La descripció no pot estar buida.");
            event.preventDefault();
            return;
        }
        if (idDepartament === "" || isNaN(idDepartament)) {
            alert("L'ID del departament ha de ser un número vàlid.");
            event.preventDefault();
            return;
        }
        if (!dataFin) {
            alert("Per favor, selecciona una data de finalització.");
            event.preventDefault();
            return;
        }
    });
});