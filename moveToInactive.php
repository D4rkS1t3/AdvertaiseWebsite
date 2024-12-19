<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

if (!isset($_SESSION['session_id'])) {
    header("Location: signin.php");
    exit();
}

$sessionID = $_SESSION['session_id'];

//pobranie danych z zadania
$input = json_decode(file_get_contents('php://input'), true);
$adId = $input['id'] ?? null;

if (!$adId) {
    echo json_encode(['success' => false, 'message' => 'Invalid ad ID']);
    exit();
}


try {
    $select = $db->prepare("SELECT id FROM users WHERE session_id = :session_id");
    $select->bindParam(':session_id', $sessionID);
    $select->execute();
    $userData = $select->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching data.']);
    exit();
}

try {
    //aktualizacja statusu ogloszenia
    $query = $db->prepare("UPDATE ads SET active = 0 WHERE id = :id AND user_id = :user_id");
    $query ->bindParam(':id', $adId, PDO::PARAM_INT);
    $query ->bindParam(':user_id', $userData['id'], PDO::PARAM_INT);
    $query->execute();

    if ($query->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => "the announcement does not exist or belongs to another user"]);
    }
} catch (PDOException $e) {
   echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}