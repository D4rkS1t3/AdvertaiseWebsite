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

    // Zmieniamy rozmiar obrazu do dokładnych wymiarów
    $newImage = imagecreatetruecolor($maxWidth, $maxHeight);

    // Obsługa przezroczystości dla PNG i GIF
    if ($mime == 'image/png' || $mime == 'image/gif') {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $maxWidth, $maxHeight, $transparent);
    }

    // Dopasuj obraz do nowych wymiarów (można przyciąć, jeśli wymagane)
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $maxWidth, $maxHeight, imagesx($image), imagesy($image));

    // Zapisz obraz w odpowiednim formacie
    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($newImage, $destinationPath, $quality);
            break;
        case 'image/png':
            imagepng($newImage, $destinationPath, floor($quality / 10));
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