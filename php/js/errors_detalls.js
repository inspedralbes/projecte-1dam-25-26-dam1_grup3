document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const inputID = document.getElementById('ID_Incidencia');
    if (form) {
        form.addEventListener('submit', function(event) {
            const valor = inputID.value.trim();
            if (valor === "") {
                alert("Si us plau, introdueix un ID d'incidència.");
                event.preventDefault();
                return;
            }
            if (parseInt(valor) <= 0) {
                alert("L'ID d'incidència ha de ser un número positiu.");
                event.preventDefault();
                return;
            }
        });
    }
    if (typeof busquedaRealizada !== 'undefined' && busquedaRealizada) {
        if (typeof numResultados !== 'undefined' && numResultados === 0) {
            console.log("Cerca finalitzada: No s'han trobat actuacions per a l'ID " + idABuscar);
        }
    }
});