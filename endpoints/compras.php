<?php
switch ($method) {
    case 'GET':
        $stmt = $conn->query("SELECT * FROM compras");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || !isset($data['folio'], $data['compra_total'], $data['id_producto'])) {
            echo json_encode(["error" => "Faltan datos obligatorios"]);
            http_response_code(400);
            exit;
        }
        $stmt = $conn->prepare("INSERT INTO compras (folio, compra_total, id_producto) VALUES (:folio, :compra_total, :id_producto)");
        $stmt->execute([
            ":folio" => $data['folio'],
            ":compra_total" => $data['compra_total'],
            ":id_producto" => $data['id_producto']
        ]);
        echo json_encode(["message" => "Compra creada correctamente"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$id || !$data) {
            echo json_encode(["error" => "Datos insuficientes"]);
            http_response_code(400);
            exit;
        }
        $stmt = $conn->prepare("UPDATE compras SET folio = :folio, compra_total = :compra_total, id_producto = :id_producto WHERE id = :id");
        $stmt->execute([
            ":folio" => $data['folio'],
            ":compra_total" => $data['compra_total'],
            ":id_producto" => $data['id_producto'],
            ":id" => $id
        ]);
        echo json_encode(["message" => "Compra actualizada correctamente"]);
        break;

    case 'DELETE':
        if (!$id) {
            echo json_encode(["error" => "ID requerido"]);
            http_response_code(400);
            exit;
        }
        $stmt = $conn->prepare("DELETE FROM compras WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(["message" => "Compra eliminada correctamente"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
