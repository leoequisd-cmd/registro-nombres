<?php
// config.php — Configuración de conexión Railway MySQL
define('DB_HOST',     'interchange.proxy.rlwy.net');
define('DB_PORT',     23483);
define('DB_USER',     'root');
define('DB_PASS',     'hCagcydVnqAbFWGJDqaGILVVpFbdovry');
define('DB_NAME',     'railway');

function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    $conn->set_charset("utf8mb4");
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["status" => "error", "mensaje" => "conexion fallida"]);
        exit();
    }
    return $conn;
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Helper para leer body JSON o POST/GET
function getParam($key) {
    if (!empty($_GET[$key]))  return trim($_GET[$key]);
    if (!empty($_POST[$key])) return trim($_POST[$key]);
    $body = file_get_contents("php://input");
    $datos = json_decode($body, true);
    return isset($datos[$key]) ? trim($datos[$key]) : null;
}
