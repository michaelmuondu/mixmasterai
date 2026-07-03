<?php

header('Content-Type: application/json');
require_once("../includes/db.php");

try {

    $stmt = $pdo->prepare("SELECT id, title, artist, genre, filename FROM songs ORDER BY id DESC");
    $stmt->execute();

    $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($songs);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}