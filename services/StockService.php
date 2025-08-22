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
 * Resta 1 al stock de un producto
 * @param PDO $conn Objeto de conexión a la BD
 * @param int $productoId ID del producto a decrementar
 * @return bool true si se actualizó correctamente, false si hubo error
 */
function restarStock(PDO $conn, int $productoId): bool { // FALTA APLICAR LA FUNCIÓN PARA CAMBIAR LA DISPONIBILIDAD
    // Primero obtenemos el stock actual
    $sql = "SELECT stock FROM productos WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $productoId, PDO::PARAM_INT);
    $stmt->execute();
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto || $producto['stock'] <= 0) {
        return false; // No hay stock para restar
    }

    $nuevoStock = $producto['stock'] - 1;

    // Actualizamos en la tabla
    $sql = "UPDATE productos SET stock = :stock WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':stock', $nuevoStock, PDO::PARAM_INT);
    $stmt->bindParam(':id', $productoId, PDO::PARAM_INT);

    return $stmt->execute();
}
