<?php
session_start();
require 'db.php';
require 'resizeImage.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

if (!isset($_SESSION['session_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$sessionID = $_SESSION['session_id'];
$response = ['success' => false, 'message' => ''];

// Pobierz dane użytkownika
try {
    $select = $db->prepare("SELECT id FROM users WHERE session_id = :session_id");
    $select->bindParam(':session_id', $sessionID);
    $select->execute();
    $userData = $select->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        throw new Exception("Invalid session ID.");
    }

    $userId = $userData['id'];
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit();
}

// Walidacja danych z formularza
$title = trim($_POST['title']);
$category = $_POST['category'];
$description = trim($_POST['description']);
$price = $_POST['price'];

if (empty($title) || strlen($title) < 5) {
    echo json_encode(['success' => false, 'message' => 'Title must be at least 5 characters.']);
    exit();
}

if (empty($category)) {
    echo json_encode(['success' => false, 'message' => 'Category is required.']);
    exit();
}

if (empty($description) || strlen($description) < 40) {
    echo json_encode(['success' => false, 'message' => 'Description must be at least 40 characters.']);
    exit();
}

if (!is_numeric($price) || $price < 0) {
    echo json_encode(['success' => false, 'message' => 'Price must be a positive number.']);
    exit();
}

// Obsługa plików (jeśli przesłane)
$uploadedImages = ['noImage.jpg'];
$uploadDir = 'uploads/';
$allowedExtensions = ['jpg', 'jpeg', 'png', 'svg'];

if (!empty($_FILES['images']['name'][0])) {
    $uploadedImages = [];

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    foreach ($_FILES['images']['name'] as $key => $imageName) {
        $imageTmpPath = $_FILES['images']['tmp_name'][$key];
        $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        if (!in_array($imageExtension, $allowedExtensions)) {
            echo json_encode(['success' => false, 'message' => "Invalid file format: $imageName."]);
            exit();
        }

        $newImageName = uniqid() . '.' . $imageExtension;
        $imageDestination = $uploadDir . $newImageName;

        if (optimizeImage($imageTmpPath, $imageDestination, 1920, 1080)) {
            $uploadedImages[] = $newImageName;
        } else {
            echo json_encode(['success' => false, 'message' => "Error uploading file: $imageName."]);
            exit();
        }
    }
} 

// Zapis ogłoszenia do bazy danych
try {
    $stmt = $db->prepare("
        INSERT INTO ads (title, description, price, category_id, image_path, user_id) 
        VALUES (:title, :description, :price, :category_id, :image_path, :user_id)
    ");

    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':price' => $price,
        ':category_id' => $category,
        ':image_path' => implode(',', $uploadedImages),
        ':user_id' => $userId,
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Advertisement added successfully!',
        'redirect' => 'dashboard.php' 
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
