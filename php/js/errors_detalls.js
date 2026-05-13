var error_msg = "";
if ($result.num_rows > 0) {
        while ($row = $result.fetch_assoc()) {
            resultados.push($row);
        }
} else {
    error_msg = "No hi ha actuacions visibles per a la incidència #" + id_a_buscar + ".";
}
if (data.length === 0) {
    error_msg = "No es pot tenir incidencia sense data.";
    alert(error_msg);
}