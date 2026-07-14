<?php

header("Content-Type: application/json");

require_once("../includes/db.php");

try {

    $stmt = $pdo->prepare("
        SELECT
            id,
            title,
            artist,
            album,
            genre,
            duration,
            file_path,
            cover_image
        FROM songs
        ORDER BY id DESC
    ");

    $stmt->execute();

    $songs = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $songs[] = $row;
    }

    echo json_encode([
        "success" => true,
        "songs" => $songs
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}