<?php

/**
 * Consulta y devuelve una lista de productos de una categoría específica.
 *
 * Esta función filtra los productos por el nombre de la categoría,
 * ideal para mostrar un listado de productos de interés para el usuario del chatbot.
 *
 * @param PDO $conn Objeto de conexión PDO a la base de datos.
 * @param int $idCategoria ID de la categoría a consultar.
 * @return array Devuelve un array de objetos de producto o un array vacío si no hay
 * productos en la categoría o si ocurre un error.
 */
function consultarProductosPorCategoria(PDO $conn, int $idCategoria): array
{
    try {
        // La consulta utiliza un WHERE para filtrar por el nombre de la categoría.
        // Se usa un marcador de posición (:idCategoria) para prevenir inyecciones SQL.
        $sql = "SELECT DISTINCT
                    p.nombre,
                    p.precio,
                    c.nombre AS categoria
                FROM
                    productos AS p
                JOIN
                    categorias AS c ON p.id_categoria = c.id
                WHERE
                    c.id = :idCategoria";

        $stmt = $conn->prepare($sql);
        // Se enlaza el valor del parámetro
        $stmt->bindParam(':idCategoria', $idCategoria, PDO::PARAM_STR);
        $stmt->execute();
        
        $productos = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $productos;
    } catch (PDOException $e) {
        // Manejo de errores.
        return [];
    }
}