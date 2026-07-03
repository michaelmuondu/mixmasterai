<?php

session_start();

header('Content-Type: application/json');

require_once("../includes/db.php");

if(!isset($_SESSION['user_id'])){
    echo json_encode([]);
    exit();
}

$stmt=$pdo->prepare("
SELECT *
FROM songs
WHERE uploaded_by=?
ORDER BY uploaded_at DESC
");

$stmt->execute([$_SESSION['user_id']]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));