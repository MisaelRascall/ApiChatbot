<?php
switch ($method) {
    case 'GET':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? null;

        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
            $stmt->execute([$id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($producto) {
                http_response_code(200);
                echo json_encode($producto);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Producto no encontrado"]);
            }
        } else {
            $stmt = $conn->query("SELECT * FROM productos");
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($productos) {
                http_response_code(200);
                echo json_encode($productos);
            } else {
                http_response_code(204);
                echo json_encode(["message" => "No hay productos disponibles"]);
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare(
            "INSERT INTO productos (nombre, precio, stock, color, ruta_imagen, id_categoria) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['nombre'],
            $data['precio'],
            $data['stock'],
            $data['color'],
            $data['ruta_imagen'],
            $data['id_categoria']
        ]);
        echo json_encode(["status" => "Producto creado"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $accion = $data['accion'] ?? null;

        if ($accion === 'aumentar_stock') {
            $id = $data['id'] ?? null;
            $cantidad = $data['cantidad'] ?? null;

            if (!$id || !$cantidad) {
                http_response_code(400); // Bad request
                echo json_encode(["error" => "ID y cantidad son requeridos"]);
                exit();
            }

            if ($cantidad <= 0) {
                http_response_code(422); // Unprocessable Entity
                echo json_encode(["error" => "La cantidad debe ser mayor a cero"]);
                exit();
            }

            $stmt = $conn->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?");
            $stmt->execute([$cantidad, $id]);

            $estaDisponible = validarDisponibilidad($conn, $id);

            if (!$estaDisponible) cambiarDisponibilidad($conn, $id, $estaDisponible);

            http_response_code(200);
            echo json_encode(["status" => "Stock aumentado correctamente"]);
        } else {
            $id = $data['id'] ?? null;
            $nombre = $data['nombre'] ?? null;
            $precio = $data['precio'] ?? null;
            $color = $data['color'] ?? null;
            $id_producto = $data['id_producto'] ?? null;

            if (!$id || !$nombre || !$precio || !$id_producto) {
                http_response_code(400);
                echo json_encode(["error" => "Faltan datos esenciales"]);
                exit();
            }

            $stmt = $conn->prepare("UPDATE productos SET nombre=?, precio=?, color=?, id_categoria=? WHERE id=?");
            $stmt->execute([$nombre, $precio, $color, $id_producto, $id]);
            http_response_code(200);
            echo json_encode(["status" => "Producto actualizado"]);
        }
        break;

    case 'DELETE':
        // Leyendo el ID desde el query string 
        $id = $_GET['id'] ?? null;
        // Leyendo el ID desde el Body (JSON o raw data)
        if (!$id) {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'] ?? null;
        }

        if (isset($id)) {
            $stmt = $conn->prepare("DELETE FROM productos WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "Producto eliminado"]);
            http_response_code(200);
        } else {
            echo json_encode(["error" => "ID requerido"]);
            http_response_code(400);
            exit;
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
}
