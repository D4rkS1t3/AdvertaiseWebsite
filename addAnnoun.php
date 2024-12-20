<?php
session_start();
require 'db.php';
require 'resizeImage.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

if (!isset($_SESSION['session_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$sessionID = $_SESSION['session_id'];

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
$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$category = filter_input(INPUT_POST, 'category', FILTER_VALIDATE_INT);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$localization = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
$phoneNumber = filter_input(INPUT_POST, 'phoneNumber', FILTER_SANITIZE_STRING);
$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);

// Sprawdzenie danych
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

if (empty($localization) || strlen($localization) < 3) {
    echo json_encode(['success' => false, 'message' => 'Localization must be at least 3 characters.']);
    exit();
}

if (!is_numeric($price) || $price < 0) {
    echo json_encode(['success' => false, 'message' => 'Price must be a positive number.']);
    exit();
}

if (!empty($phoneNumber) && !preg_match('/^\+?[0-9]{9,15}$/', $phoneNumber)) {
    echo json_encode(['success' => false, 'message' => 'Invalid phone number. Provide a number with 9 to 15 digits.']);
    exit();
}

// Obsługa przesyłanych plików
$uploadedImages = ['noImage.jpg'];
$uploadDir = 'uploads/';
$thumbSizes = [
    ['folder' => 'thumb_150x113', 'width' => 150, 'height' => 113],
    ['folder' => 'thumb_300x200', 'width' => 300, 'height' => 200],
    ['folder' => 'thumb_600x400', 'width' => 600, 'height' => 400],
    ['folder' => 'thumb_900x600', 'width' => 900, 'height' => 600],
];
$allowedExtensions = ['jpg', 'jpeg', 'png', 'svg'];

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
    foreach ($thumbSizes as $size) {
        mkdir($uploadDir . $size['folder'], 0777, true);
    }
}

if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
    $uploadedImages = [];

    foreach ($_FILES['images']['name'] as $key => $imageName) {
        $imageTmpPath = $_FILES['images']['tmp_name'][$key];
        $imageError = $_FILES['images']['error'][$key];
        $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        if ($imageError !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => "Error uploading file: $imageName."]);
            exit();
        }

        if (!in_array($imageExtension, $allowedExtensions)) {
            echo json_encode(['success' => false, 'message' => "Invalid file format: $imageName."]);
            exit();
        }

        $newImageName = uniqid() . '.' . $imageExtension;
        $mainImagePath = $uploadDir . $newImageName;

        // Optimize main image
        if (optimizeImage($imageTmpPath, $mainImagePath, 1920, 1080)) {
            $uploadedImages[] = $newImageName;

            // Generate thumbnails
            foreach ($thumbSizes as $size) {
                $thumbPath = $uploadDir . $size['folder'] . '/' . $newImageName;
                optimizeImage($imageTmpPath, $thumbPath, $size['width'], $size['height']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => "Error optimizing image: $imageName."]);
            exit();
        }
    }
}

// Zapisanie danych w bazie
try {
    $stmt = $db->prepare("
        INSERT INTO ads (title, description, price, localization, category_id, image_path, user_id, phone_number, active) 
        VALUES (:title, :description, :price, :localization, :category_id, :image_path, :user_id, :phone_number, :active)
    ");

    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':price' => $price,
        ':localization' => $localization,
        ':category_id' => $category,
        ':image_path' => implode(',', $uploadedImages),
        ':user_id' => $userId,
        ':phone_number' => $phoneNumber ?: null,
        ':active' => 1
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Advertisement added successfully!',
        'redirect' => 'dashboard.php'
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
