<?php
function optimizeImage($sourcePath, $destinationPath, $maxWidth, $maxHeight, $quality = 75) {
    $imageInfo = getimagesize($sourcePath);
    $mime = $imageInfo['mime'];

    // Utwórz obraz na podstawie MIME
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $image = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($sourcePath);
            break;
        default:
            return false; // Nieobsługiwany format
    }

    // Pobierz oryginalne wymiary
    $origWidth = imagesx($image);
    $origHeight = imagesy($image);

    // Oblicz proporcje
    $ratio = $origWidth / $origHeight;
    if ($maxWidth / $maxHeight > $ratio) {
        $newWidth = $maxHeight * $ratio;
        $newHeight = $maxHeight;
    } else {
        $newWidth = $maxWidth;
        $newHeight = $maxWidth / $ratio;
    }

    // Zmień rozmiar obrazu
    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    // Obsługa przezroczystości dla PNG i GIF
    if ($mime == 'image/png' || $mime == 'image/gif') {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
    }

    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

    // Zapisz obraz w zależności od formatu
    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($newImage, $destinationPath, $quality);
            break;
        case 'image/png':
            imagepng($newImage, $destinationPath, floor($quality / 10)); // Skala kompresji dla PNG (0-9)
            break;
        case 'image/gif':
            imagegif($newImage, $destinationPath);
            break;
    }

    // Uwolnij pamięć
    imagedestroy($image);
    imagedestroy($newImage);

    return true;
}
?>