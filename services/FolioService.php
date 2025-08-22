<?php
/**
 * Genera un folio único para la tabla compras
 * @param PDO $conn Objeto de conexión a la base de datos
 * @return string Folio único
 */
function generarFolioUnico(PDO $conn): string {
    do {
        // Tomamos timestamp actual y lo convertimos a base 36
        $timestamp = microtime(true) * 1000; // milisegundos
        $folio = strtoupper(base_convert($timestamp, 10, 36));

        // Verificamos si ya existe en la tabla compras
        $stmt = $conn->prepare("SELECT COUNT(*) FROM compras WHERE folio = ?");
        $stmt->execute([$folio]);
        $existe = $stmt->fetchColumn();

    } while ($existe > 0); // Repetimos hasta que sea único

    return $folio;
}
