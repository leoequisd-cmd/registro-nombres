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
    echo json_encode(["status" => "error", "mensaje" => "conexion fallida"]);
    exit();
}

$nombre = "";
if (!empty($_GET["nombre"])) {
    $nombre = trim($_GET["nombre"]);
} elseif (!empty($_POST["nombre"])) {
    $nombre = trim($_POST["nombre"]);
} else {
    $body = file_get_contents("php://input");
    $datos = json_decode($body, true);
    if (isset($datos["nombre"])) $nombre = trim($datos["nombre"]);
}

if ($nombre == "") {
    echo json_encode(["status" => "error", "mensaje" => "nombre vacio"]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO nombres (nombre) VALUES (?)");
$stmt->bind_param("s", $nombre);

if ($stmt->execute()) {
    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "error"]);
}

$stmt->close();
$conn->close();
