<?php
switch ($method) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        $request = $_GET['request'] ?? null;

        if(!$id && !$request){
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'] ?? null;
            $request = $data['request'] ?? null;
        }

        if (isset($id)) {
            $stmt = $conn->prepare("SELECT * FROM categorias WHERE id = ?");
            $stmt->execute([$id]);
            $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($categoria) {
                http_response_code(200);
                echo json_encode($categoria);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Categoría no encontrada"]);
            }
        } else if($request === "nombre"){
            $listaCategorias = getCategorias($conn);

            if(isset($listaCategorias)){
                http_response_code(200);
                echo json_encode($listaCategorias);
            } else{
                http_response_code(204);
                echo json_encode(["message" => "No hay categorias en existencia"]);
            }
        } else {
            $stmt = $conn->query("SELECT * FROM categorias");
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($categorias) {
                http_response_code(200);
                echo json_encode($categorias);
            } else {
                http_response_code(204);
                echo json_encode(["message" => "No hay categorías disponibles"]);
            }
        }
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

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data['id'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $descripcion = $data['descripcion'] ?? null;

        if (!$id || !$nombre || !$descripcion) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos obligatorios"]);
            exit();
        }

        try {
            $sql = "UPDATE categorias SET nombre = :nombre, descripcion = :descripcion WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(["message" => "Categoría actualizada con éxito"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "No se pudo actualizar la categoría"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }

        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'] ?? null;
        }

        if (isset($id)) {
            $stmt = $conn->prepare("DELETE FROM categorias WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "Categoría eliminada"]);
            http_response_code(200);
        } else {
            echo json_encode(["error" => "ID requerido"]);
            http_response_code(400);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
}
