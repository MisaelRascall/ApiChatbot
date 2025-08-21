<?php
try {
    // Si $conn existe y es válido, la conexión fue exitosa
    $stmt = $conn->query("SELECT 1");
    echo json_encode([
        "status" => "success",
        "message" => "Conexión a la base de datos exitosa"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Error en la conexión: " . $e->getMessage()
    ]);
}
