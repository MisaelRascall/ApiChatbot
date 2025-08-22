<?php
switch ($method) {
    case 'GET':
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data['id'] ?? null;

        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM compras WHERE id = ?");
            $stmt->execute([$id]);
            $compra = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($compra) {
                http_response_code(200);
                echo json_encode($compra);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Compra no encontrada"]);
            }
        } else {
            $stmt = $conn->query("SELECT * FROM compras");
            $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($compras) {
                http_response_code(200);
                echo json_encode($compras);
            } else {
                http_response_code(204);
                echo json_encode(["message" => "No hay compras disponibles"]);
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        $compra_total = $data['compra_total'] ?? null;
        $id_producto = $data['id_producto'] ?? null;

        if (!$compra_total || !$id_producto) {
            echo json_encode(["error" => "Faltan datos"]);
            http_response_code(400);
            exit;
        }

        $id_producto = (int)$data['id_producto'];
        
        if(!validarDisponibilidad($conn, $id_producto)) {
            http_response_code(409); // Conflict
            echo json_encode(["error" => "El producto no está disponible"]);
            exit();
        }

        $folio = generarFolioUnico($conn);

        try {
            $sql = "INSERT INTO compras (folio, compra_total, id_producto) VALUES (:folio, :compra_total, :id_producto)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ":folio" => $folio,
                ":compra_total" => $data['compra_total'],
                ":id_producto" => $data['id_producto']
            ]);
            echo json_encode(["message" => "Compra creada correctamente"]);
        } catch (PDOException $e) {
            echo json_encode([
                "error" => $e->getMessage(),
                "message" => "No se pudo registrar la compra"
            ]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data['id'] ?? null;
        $folio = $data['folio'] ?? null;
        $compra_total = $data['compra_total'] ?? null;
        $id_producto = $data['id_producto'] ?? null;

        if (!$id || !$folio || !$compra_total || !$id_producto) {
            echo json_encode(["error" => "Datos insuficientes"]);
            http_response_code(400);
            exit;
        }

        try {
            $sql = "UPDATE compras SET folio = :folio, compra_total = :compra_total, id_producto = :id_producto WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ":folio" => $data['folio'],
                ":compra_total" => $data['compra_total'],
                ":id_producto" => $data['id_producto'],
                ":id" => $id
            ]);
            echo json_encode(["message" => "Compra actualizada correctamente"]);
        } catch (PDOException $e) {
            echo json_encode([
                "error" => $e->getMessage(),
                "message" => "No se puede actualizar la compra con ID: " + $id
            ]);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'] ?? null;
        }

        if (isset($id)) {
            $stmt = $conn->prepare("DELETE FROM compras WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "Compra eliminada"]);
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
        break;
}
