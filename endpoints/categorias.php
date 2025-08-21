<?php
switch ($method) {
    case 'GET':
        $stmt = $conn->query("SELECT * FROM categorias");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['nombre']) || !isset($data['descripcion'])) {
            echo json_encode(["error" => "Faltan datos"]);
            break;
        }

        try {
            $stmt = $conn->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (:nombre, :descripcion)");
            $stmt->bindParam(":nombre", $data["nombre"]);
            $stmt->bindParam(":descripcion", $data["descripcion"]);
            $stmt->execute();

            echo json_encode([
                "message" => "Categoría creada correctamente",
                "id" => $conn->lastInsertId()
            ]);
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
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
