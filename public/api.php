<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Responder a preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "../config/conexion.php"; // Conexión a la Base de Datos
require_once "../services/StockService.php"; // Servicio para stock y disponibilidad
require_once "../services/FolioService.php"; // Servicio para generar el Folio

$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
$resource = $path[1] ?? null;

switch ($resource) {
    case 'categorias':
        include "../endpoints/categorias.php";
        break;

    case 'productos':
        include "../endpoints/productos.php";
        break;

    case 'compras':
        include "../endpoints/compras.php";
        break;

    case 'ping':
        include "../endpoints/ping.php";
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Recurso no encontrado"]);
        break;
}
