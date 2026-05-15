(function() {
    const formulario = document.querySelector('form');
    if (formulario) {
        formulario.addEventListener('submit', function(e) {
            const idInput = document.getElementById('ID_Incidencia').value;
            if (idInput.trim() === "") {
                alert("Per favor, introdueix un ID d'incidència.");
                e.preventDefault();
            }
        });
    }
    if (busquedaRealizada && numResultados === 0) {
        const errorMsg = "No hi ha actuacions visibles per a la incidència #" + idABuscar + ".";
        alert(errorMsg);
    }

})();