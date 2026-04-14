<?php
// favoritos.php — Guardar y obtener canciones favoritas
// GET  ?action=listar&email=...
// POST action=agregar|quitar, email, track_id, title, artist, artwork_url, duration
// URL: https://registro-nombres-production.up.railway.app/favoritos.php

require_once 'config.php';

$action = getParam('action');
$email  = getParam('email');

if (!$email) {
    echo json_encode(["status" => "error", "mensaje" => "email requerido"]);
    exit();
}

$conn = getConnection();

if ($action === 'listar') {
    $stmt = $conn->prepare("SELECT * FROM tracks WHERE user_email = ? AND is_favorite = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $tracks = [];
    while ($row = $result->fetch_assoc()) {
        $tracks[] = $row;
    }
    echo json_encode(["status" => "ok", "tracks" => $tracks]);
    $stmt->close();

} elseif ($action === 'agregar') {
    $trackId    = getParam('track_id');
    $title      = getParam('title');
    $artist     = getParam('artist');
    $artwork    = getParam('artwork_url');
    $duration   = (int) getParam('duration');

    $stmt = $conn->prepare(
        "INSERT INTO tracks (id, title, artist, artwork_url, duration, is_favorite, user_email)
         VALUES (?, ?, ?, ?, ?, 1, ?)
         ON DUPLICATE KEY UPDATE is_favorite = 1"
    );
    $stmt->bind_param("ssssis", $trackId, $title, $artist, $artwork, $duration, $email);
    if ($stmt->execute()) {
        echo json_encode(["status" => "ok"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
    $stmt->close();

} elseif ($action === 'quitar') {
    $trackId = getParam('track_id');
    $stmt = $conn->prepare("UPDATE tracks SET is_favorite = 0 WHERE id = ? AND user_email = ?");
    $stmt->bind_param("ss", $trackId, $email);
    if ($stmt->execute()) {
        echo json_encode(["status" => "ok"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
    $stmt->close();

} else {
    echo json_encode(["status" => "error", "mensaje" => "action invalida"]);
}

$conn->close();
