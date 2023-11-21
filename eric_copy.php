<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming the 'selected_images' key is used to send the selected image URLs
    $requestData = json_decode(file_get_contents('php://input'), true);

    if (isset($requestData['selected_images'])) {
        // Specify the destination directory
        $targetDirectory = $_SERVER['DOCUMENT_ROOT'] . '/Document Management System/CPAR Folder/';

        // Copy selected images to the destination directory
        foreach ($requestData['selected_images'] as $imageUrl) {

//TODO-RENAME FILE






















            $sourcePath = $_SERVER['DOCUMENT_ROOT'] . $imageUrl;
            $destinationPath = $targetDirectory . basename($imageUrl);
            copy($sourcePath, $destinationPath);
        }

        // You can perform additional actions or send a response back to the client if needed
        echo json_encode(['success' => true]);
    } else {
        // Handle missing or invalid data
        echo json_encode(['error' => 'Invalid data']);
    }
} else {
    // Handle invalid requests
    echo json_encode(['error' => 'Invalid request']);
}


