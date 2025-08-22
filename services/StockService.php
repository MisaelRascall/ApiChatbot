<?php
/**
 * Cambia la disponibilidad de un producto.
 * 
 * Esta función actualiza el atributo `isAvailable` de un producto
 * en la base de datos. Se puede usar para marcar un producto como
 * disponible (true) o no disponible (false).
 * 
 * @param PDO $conn Objeto de conexión PDO.
 * @param int $productoId ID del producto a actualizar.
 * @param bool $estaDisponible Indica el nuevo estado de disponibilidad:
 *              true = disponible, false = no disponible.
 * @return bool Devuelve true si la actualización fue exitosa, false en caso de error
 */
function cambiarDisponibilidad(PDO $conn, int $productoId, bool $estaDisponible): bool
{
    try {
        // Alternar disponibilidad
        $estaDisponible = !$estaDisponible;

        $sql = "UPDATE productos SET isAvailable = :estaDisponible WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':estaDisponible', $estaDisponible, PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $productoId, PDO::PARAM_INT);

        return $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Valida si un producto está disponible para venta
 * @param PDO $conn Objeto de conexión a la base de datos
 * @param int $productoId ID del producto a validar
 * @return bool true si hay stock y está disponible, false en caso contrario
 */
function validarDisponibilidad(PDO $conn, int $productoId): bool {
    $sql = "SELECT stock, isAvailable FROM productos WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $productoId, PDO::PARAM_INT);
    $stmt->execute();

    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        // No se encontró el producto
        return false;
    }

    // Verificamos stock y disponibilidad
    return ($producto['stock'] > 0) && ($producto['isAvailable'] == true);
}

/**
 * Resta unidades al stock de un producto
 * 
 * @param PDO $conn Objeto de conexión a la base de datos
 * @param int $productoId ID del producto
 * @param int $cantidad Cantidad a restar del stock
 * @return bool true si se actualizó el stock, false en caso contrario
 */
function restarStock(PDO $conn, int $productoId, int $cantidad): bool {
    try {
        // 1. Consultar stock actual
        $sql = "SELECT stock FROM productos WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $productoId, PDO::PARAM_INT);
        $stmt->execute();
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Validar si existe el producto
        if (!$producto) {
            return false; // Producto no encontrado
        }

        $stockActual = (int) $producto['stock'];

        // 3. Verificar que el stock sea suficiente
        if ($stockActual < $cantidad) {
            return false; // No hay stock suficiente
        }

        // 4. Restar stock
        $nuevoStock = $stockActual - $cantidad;

        $updateSql = "UPDATE productos SET stock = :nuevoStock WHERE id = :id";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bindParam(':nuevoStock', $nuevoStock, PDO::PARAM_INT);
        $updateStmt->bindParam(':id', $productoId, PDO::PARAM_INT);

        return $updateStmt->execute();
    } catch (PDOException $e) {
        error_log("Error al restar stock: " . $e->getMessage());
        return false;
    }
}
