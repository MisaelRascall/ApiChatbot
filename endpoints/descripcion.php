<?php
switch ($method) {
    case 'GET':
        $stmt = $conn->query("SELECT * FROM categorias");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
        $stmt->execute([
            $data['nombre'],
            $data['descripcion'],
        ]);
        echo json_encode(["status" => "Categoría creada"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("UPDATE categorias SET nombre=?, descripcion=? WHERE id=?");
        $stmt->execute([
            $data['nombre'],
            $data['descripcion'],
            $data['id'],
        ]);
        echo json_encode(["status" => "Categoría actualizada"]);
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;

        if(!$id){
            parse_str(file_get_contents("php://input"), $data);
            $id = $data['id'] ?? null;
        }

        if (isset($id)) {
            $stmt = $conn->prepare("DELETE FROM categorias WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "Categoría eliminada"]);
        } else {
            echo json_encode(["error" => "ID requerido"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
}
