<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adId = $_POST['ad_id'] ?? null;
    $removeImages = $_POST['removeImages'] ?? [];
    $newImages = $_FILES['newImages'] ?? null;

    if (!$adId) {
        die("Invalid ad ID.");
    }

    // Pobierz istniejące obrazy
    $query = $db->prepare("SELECT image_path FROM ads WHERE id = :id AND user_id = :user_id");
    $query->bindParam(':id', $adId, PDO::PARAM_INT);
    $query->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $query->execute();
    $adData = $query->fetch(PDO::FETCH_ASSOC);

    if (!$adData) {
        die("Ad does not exist or you do not have permission.");
    }

    $existingImages = explode(',', $adData['image_path']);

    // Usuń oznaczone obrazy
    foreach ($removeImages as $index) {
        if (isset($existingImages[$index])) {
            $imagePath = './uploads/' . trim($existingImages[$index]);
            if (file_exists($imagePath)) {
                unlink($imagePath); // Usuń plik z serwera
            }
            unset($existingImages[$index]); // Usuń z tablicy
        }
    }

    // Obsługa nowych obrazów
    if ($newImages && $newImages['error'][0] === UPLOAD_ERR_OK) {
        foreach ($newImages['tmp_name'] as $tmpName) {
            $newFileName = uniqid() . '.' . pathinfo($newImages['name'][0], PATHINFO_EXTENSION);
            move_uploaded_file($tmpName, "./uploads/$newFileName");
            $existingImages[] = $newFileName;
        }
    }

    // Zaktualizuj obrazy w bazie danych
    $updatedImagePaths = implode(',', $existingImages);
    $updateQuery = $db->prepare("UPDATE ads SET image_path = :image_path WHERE id = :id AND user_id = :user_id");
    $updateQuery->bindParam(':image_path', $updatedImagePaths);
    $updateQuery->bindParam(':id', $adId, PDO::PARAM_INT);
    $updateQuery->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $updateQuery->execute();

    echo json_encode(['success' => true, 'message' => 'Ad updated successfully.']);
}