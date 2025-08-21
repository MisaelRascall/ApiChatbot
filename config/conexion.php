<?php
$host = "localhost";
$dbname = "bd_chatbot";
$username = "invitado";
$password = "pass";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8",$username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
}