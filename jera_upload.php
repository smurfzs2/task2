<?php
session_start();

function getIndex($cparId, $lotNumber)
{
    $data = '';
    $extensions = ['jpg', 'jpeg', 'pdf', 'png']; // List of file extensions to check
    $pattern = "/^{$cparId}\({$lotNumber}\)(_\d+)?\.(pdf|jpg|png|jpeg)$/i"; // Pattern for capturing related files

    $existingIndexes = [];

    foreach ($extensions as $extension) {
        $files = glob($_SERVER['DOCUMENT_ROOT'] . "/Document Management System/CPAR Folder/*.$extension");

        foreach ($files as $file) {
            if (preg_match($pattern, basename($file))) { // Check if the file matches the pattern
                preg_match('/_(\d+)\./', $file, $matches);
                if (isset($matches[1])) {
                    $existingIndexes[] = (int)$matches[1];
                }
            }
        }
    }

    // If no matching files found, start from index 0
    if (empty($existingIndexes)) {
        return 0;
    }

    // Find the highest existing index and return the next index to use
    $maxIndex = max($existingIndexes);
    return $maxIndex + 1;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $targetDirectory = $_SERVER['DOCUMENT_ROOT'] . '/Document Management System/CPAR Folder/';
    $cparId = $_GET["cparId"];
    $lotNumber = $_GET["lotNumber"];

    chmod($targetDirectory, 0777);

    $uploadedFiles = $_FILES["my_file"];

    print_r($uploadedFiles);
    $numFilesUploaded = count($uploadedFiles["name"]);
    $uploadSuccess = true;

    $index = getIndex($cparId, $lotNumber);

    for ($i = 0; $i < $numFilesUploaded; $i++) {

        $fileName = basename($uploadedFiles["name"][$i]);
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Generate a new filename based on cparId, lotNumber, and the next available index
        $newFileName = "{$cparId}({$lotNumber})_" . $index . '.' . $fileType;

        $targetFilePath = $targetDirectory . $newFileName;

        $allowedExtensions = array("jpg", "jpeg", "png", "mp3", "mp4");

        if (in_array($fileType, $allowedExtensions)) {
            if (move_uploaded_file($uploadedFiles["tmp_name"][$i], $targetFilePath)) {
                $index++; // Increment the index for the next file
                continue;
            } else {
                echo "Sorry, there was an error uploading " . htmlspecialchars($fileName) . ".<br>";
                $uploadSuccess = false;
            }
        } else {
            echo "Sorry, " . htmlspecialchars($fileName) . " is not a supported file type.<br>";
            $uploadSuccess = false;
        }
    }

    if ($uploadSuccess) {
        $_SESSION['uploadSuccess'] = true;
        echo '<script>alert("All files have been uploaded successfully.");</script>';
        // Output a success message as a JSON response
        header('Location: jera_fileUpload.php');
        exit;
    }
}
?>
