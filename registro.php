<?php
// registro.php — Registrar nuevo usuario
// POST: nombre, email, password
// URL: https://registro-nombres-production.up.railway.app/registro.php

require_once 'config.php';

$nombre   = getParam('nombre');
$email    = getParam('email');
$password = getParam('password');

if (!$nombre || !$email || !$password) {
    echo json_encode(["status" => "error", "mensaje" => "campos incompletos"]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "mensaje" => "email invalido"]);
    exit();
}

$passwordHash = hash('sha256', $password);

$conn = getConnection();

// Verificar si ya existe
$check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo json_encode(["status" => "error", "mensaje" => "email ya registrado"]);
    $check->close();
    $conn->close();
    exit();
}
$check->close();

// Insertar
$stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password_hash) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nombre, $email, $passwordHash);

if ($stmt->execute()) {
    echo json_encode([
        "status"  => "ok",
        "mensaje" => "registro exitoso",
        "user"    => ["nombre" => $nombre, "email" => $email]
    ]);
} else {
    echo json_encode(["status" => "error", "mensaje" => "error al registrar"]);
}

$stmt->close();
$conn->close();
