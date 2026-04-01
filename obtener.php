<?php
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$host     = "interchange.proxy.rlwy.net";
$puerto   = 23483;
$usuario  = "root";
$password = "hCagcydVnqAbFWGJDqaGILVVpFbdovry";
$base     = "railway";

$conn = new mysqli($host, $usuario, $password, $base, $puerto);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(["error" => $conn->connect_error]);
    exit();
}

$resultado = $conn->query("SELECT id, nombre FROM nombres ORDER BY id DESC");
$nombres = [];
while ($fila = $resultado->fetch_assoc()) {
    $nombres[] = ["id" => $fila["id"], "nombre" => $fila["nombre"]];
}

echo json_encode($nombres, JSON_UNESCAPED_UNICODE);
$conn->close();
