<?php

header("Content-Type: application/json");

require_once("../includes/db.php");

try {

    $stmt = $pdo->prepare("
        SELECT
            id,
            title,
            artist,
            genre,
            filename,
            duration,
            cover,
            bpm,
            song_key
        FROM songs
        ORDER BY id DESC
    ");

    $stmt->execute();

    $songs = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $row["file_path"] =
            "../uploads/music/" . $row["filename"];

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