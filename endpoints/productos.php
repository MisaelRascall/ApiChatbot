<?php
switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $conn->prepare("SELECT * FROM productos WHERE id=?");
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            $stmt = $conn->query("SELECT * FROM productos");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
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
        if(!$id){
            parse_str(file_get_contents("php://input"), $data);
            $id = $data['id'] ?? null;
        }

        if (isset($id)) {
            $stmt = $conn->prepare("DELETE FROM productos WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "Producto eliminado"]);
        } else {
            echo json_encode(["error" => "ID requerido"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
}
