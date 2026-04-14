<?php
// login.php — Iniciar sesión
// POST: email, password
// URL: https://registro-nombres-production.up.railway.app/login.php

require_once 'config.php';

$email    = getParam('email');
$password = getParam('password');

if (!$email || !$password) {
    echo json_encode(["status" => "error", "mensaje" => "campos incompletos"]);
    exit();
}

$passwordHash = hash('sha256', $password);
$conn = getConnection();

$stmt = $conn->prepare("SELECT id, nombre, email FROM usuarios WHERE email = ? AND password_hash = ?");
$stmt->bind_param("ss", $email, $passwordHash);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    echo json_encode([
        "status"  => "ok",
        "mensaje" => "login exitoso",
        "user"    => [
            "id"     => $user['id'],
            "nombre" => $user['nombre'],
            "email"  => $user['email']
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "mensaje" => "credenciales incorrectas"]);
}

$stmt->close();
$conn->close();
