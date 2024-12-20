<?php
session_start();
require 'db.php';
require 'resizeImage.php'; // Assuming this file contains your optimizeImage function

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

if (!isset($_SESSION['session_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$sessionID = $_SESSION['session_id'];
$adId = filter_input(INPUT_POST, 'adId', FILTER_VALIDATE_INT);

if (!$adId) {
    echo json_encode(['success' => false, 'message' => 'Invalid ad ID.']);
    exit();
}

// Log incoming data for debugging
file_put_contents('debug.log', print_r($_POST, true), FILE_APPEND);
file_put_contents('debug.log', print_r($_FILES, true), FILE_APPEND);

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

// Validate and sanitize input fields
$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$category = filter_input(INPUT_POST, 'category', FILTER_VALIDATE_INT);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$localization = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
$phoneNumber = filter_input(INPUT_POST, 'phoneNumber', FILTER_SANITIZE_STRING);
$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);

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
    echo json_encode(['success' => false, 'message' => 'Invalid phone number.']);
    exit();
}

$uploadDir = 'uploads/';
$thumbSizes = [
    ['folder' => 'thumb_150x113', 'width' => 150, 'height' => 113],
    ['folder' => 'thumb_300x200', 'width' => 300, 'height' => 200],
    ['folder' => 'thumb_600x400', 'width' => 600, 'height' => 400],
    ['folder' => 'thumb_900x600', 'width' => 900, 'height' => 600],
];

// Ensure directories exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
foreach ($thumbSizes as $size) {
    if (!is_dir($uploadDir . $size['folder'])) {
        mkdir($uploadDir . $size['folder'], 0777, true);
    }
}

$uploadedImages = [];

// Process new images
if (isset($_FILES['newImages']) && is_array($_FILES['newImages']['error'])) {
    foreach ($_FILES['newImages']['error'] as $key => $error) {
        if ($error === UPLOAD_ERR_OK) {
            // Get file details
            $tmpPath = $_FILES['newImages']['tmp_name'][$key];
            $name = $_FILES['newImages']['name'][$key];
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            // Generate unique file name
            $newFileName = uniqid() . '.' . $extension;
            $mainImagePath = $uploadDir . $newFileName;

            // Check if the file is an image
            $validExtensions = ['jpg', 'jpeg', 'png', 'svg'];
            if (!in_array($extension, $validExtensions)) {
                continue; // Skip invalid files
            }

            // Optimize and save the main image
            if (optimizeImage($tmpPath, $mainImagePath, 1920, 1080)) {
                $uploadedImages[] = $newFileName;

                // Generate thumbnails
                foreach ($thumbSizes as $size) {
                    $thumbPath = $uploadDir . $size['folder'] . '/' . $newFileName;
                    optimizeImage($tmpPath, $thumbPath, $size['width'], $size['height']);
                }
            }
        }
    }
}


try {
    // Fetch existing images
    $query = $db->prepare("SELECT image_path FROM ads WHERE id = :adId AND user_id = :userId");
    $query->execute([':adId' => $adId, ':userId' => $userId]);
    $existingAd = $query->fetch(PDO::FETCH_ASSOC);

    if (!$existingAd) {
        echo json_encode(['success' => false, 'message' => 'Advertisement not found or does not belong to the user.']);
        exit();
    }

    $existingImages = explode(',', $existingAd['image_path']);
    $removeImages = $_POST['removeImages'] ?? [];

    // Remove flagged images
    foreach ($removeImages as $index) {
        if (isset($existingImages[$index])) {
            $imageToRemove = $existingImages[$index];
    
            // Sprawdź, czy plik istnieje i czy jest plikiem
            if (is_file($uploadDir . $imageToRemove)) {
                unlink($uploadDir . $imageToRemove);
            }
    
            foreach ($thumbSizes as $size) {
                $thumbPath = $uploadDir . $size['folder'] . '/' . $imageToRemove;
                if (is_file($thumbPath)) {
                    unlink($thumbPath);
                }
            }
    
            unset($existingImages[$index]); // Usuń z listy
        }
    }
    

    // Merge new and existing images
    $finalImages = array_merge($existingImages, $uploadedImages);
    $finalImagePaths = implode(',', $finalImages);

    // Update ad details
    $update = $db->prepare("
        UPDATE ads 
        SET title = :title, description = :description, price = :price, localization = :localization, 
            category_id = :category_id, image_path = :image_path, phone_number = :phone_number
        WHERE id = :adId AND user_id = :userId
    ");

    $update->execute([
        ':title' => $title,
        ':description' => $description,
        ':price' => $price,
        ':localization' => $localization,
        ':category_id' => $category,
        ':image_path' => $finalImagePaths,
        ':phone_number' => $phoneNumber ?: null,
        ':adId' => $adId,
        ':userId' => $userId
    ]);

    echo json_encode(['success' => true, 'message' => 'Advertisement updated successfully!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
