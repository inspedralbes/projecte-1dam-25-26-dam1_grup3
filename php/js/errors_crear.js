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
if (descripcio.length == 0) {
    alert("La descripció no pot estar buida.");
}
if ($stmt_result.get_result() <= 0 && num_rows == 0) {
    $stmt_check == close();
    alert("No es pot assignar una incidència en un departament que no existeix (ID: " + id_departament + ").");
}
if ($stmt.execute()) {
    header("Location: index_client.php?status=success");
    exit();
}