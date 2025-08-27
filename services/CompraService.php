<?php

/**
 * Busca y devuelve los detalles de una compra por su número de folio, incluyendo 
 * los nombres del producto y la categoría asociados.
 *
 * Esta función realiza una consulta en la base de datos uniendo las tablas
 * 'compras', 'productos' y 'categorias' para obtener un informe completo
 * de una compra específica.
 *
 * @param PDO $conn Objeto de conexión PDO a la base de datos.
 * @param string $folio El folio de la compra a buscar.
 * @return object|null Devuelve un objeto con los datos de la compra si se encuentra.
 * Los atributos del objeto incluyen 'folio', 'compra_total',
 * 'nombre_producto' y 'nombre_categoria'.
 * Devuelve null si la compra no se encuentra o si ocurre un error.
 */
function buscarCompraPorFolio(PDO $conn, string $folio): ?object
{
    try {
        $sql = "SELECT 
                    c.folio,
                    c.compra_total AS precio,
                    p.nombre AS producto,
                    cat.nombre AS categoria
                FROM
                    compras AS c
                JOIN
                    productos AS p ON c.id_producto = p.id
                JOIN
                    categorias AS cat ON p.id_categoria = cat.id
                WHERE
                    c.folio = :folio";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':folio', $folio, PDO::PARAM_STR);
        $stmt->execute();
        
        // fetch(PDO::FETCH_OBJ) devuelve la primera fila como un objeto, o false si no hay resultados.
        $compra = $stmt->fetch(PDO::FETCH_OBJ);

        // Si se encuentra la compra, la función retorna el objeto, si no, retorna null.
        return $compra !== false ? $compra : null;
    } catch (PDOException $e) {
        // En caso de un error en la base de datos, la función devuelve null.
        return null;
    }
}