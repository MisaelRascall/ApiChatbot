<?php

/**
 * Obtiene una lista de los nombres de todas las categorías disponibles.
 *
 * @param PDO $conn Objeto de conexión PDO a la base de datos.
 * @return array Devuelve un array de strings con los nombres de las categorías o
 * un array vacío si no hay categorías o si ocurre un error.
 */
function getCategorias(PDO $conn): array
{
    try {
        $sql = "SELECT DISTINCT nombre FROM categorias";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $nombresCategorias = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $nombresCategorias;
    } catch (PDOException $e) {
        return [];
    }
}