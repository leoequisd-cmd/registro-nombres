<?php
// playlists.php — CRUD de playlists del usuario
// GET  ?action=listar&email=...
// POST action=crear,  email, nombre
// POST action=eliminar, playlist_id
// POST action=agregar_track,  playlist_id, track_id
// URL: https://registro-nombres-production.up.railway.app/playlists.php

require_once 'config.php';

$action = getParam('action');
$conn   = getConnection();

if ($action === 'listar') {
    $email = getParam('email');
    $stmt  = $conn->prepare("SELECT * FROM playlists WHERE user_email = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result    = $stmt->get_result();
    $playlists = [];
    while ($row = $result->fetch_assoc()) {
        $playlists[] = $row;
    }
    echo json_encode(["status" => "ok", "playlists" => $playlists]);
    $stmt->close();

} elseif ($action === 'crear') {
    $email  = getParam('email');
    $nombre = getParam('nombre');
    if (!$email || !$nombre) {
        echo json_encode(["status" => "error", "mensaje" => "campos incompletos"]);
        $conn->close(); exit();
    }
    $stmt = $conn->prepare("INSERT INTO playlists (nombre, user_email) VALUES (?, ?)");
    $stmt->bind_param("ss", $nombre, $email);
    if ($stmt->execute()) {
        echo json_encode(["status" => "ok", "playlist_id" => $conn->insert_id]);
    } else {
        echo json_encode(["status" => "error"]);
    }
    $stmt->close();

} elseif ($action === 'eliminar') {
    $playlistId = (int) getParam('playlist_id');
    $conn->query("DELETE FROM playlist_tracks WHERE playlist_id = $playlistId");
    $stmt = $conn->prepare("DELETE FROM playlists WHERE id = ?");
    $stmt->bind_param("i", $playlistId);
    if ($stmt->execute()) {
        echo json_encode(["status" => "ok"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
    $stmt->close();

} elseif ($action === 'agregar_track') {
    $playlistId = (int) getParam('playlist_id');
    $trackId    = getParam('track_id');
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO playlist_tracks (playlist_id, track_id) VALUES (?, ?)"
    );
    $stmt->bind_param("is", $playlistId, $trackId);
    if ($stmt->execute()) {
        // actualizar conteo
        $conn->query("UPDATE playlists SET track_count = track_count + 1 WHERE id = $playlistId");
        echo json_encode(["status" => "ok"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
    $stmt->close();

} else {
    echo json_encode(["status" => "error", "mensaje" => "action invalida"]);
}

$conn->close();
