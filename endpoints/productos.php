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
        $stmt = $conn->prepare(
            "UPDATE productos SET nombre=?, precio=?, stock=?, color=?, ruta_imagen=?, id_categoria=? WHERE id=?"
        );
        $stmt->execute([
            $data['nombre'],
            $data['precio'],
            $data['stock'],
            $data['color'],
            $data['ruta_imagen'],
            $data['id_categoria'],
            $data['id']
        ]);
        echo json_encode(["status" => "Producto actualizado"]);
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
