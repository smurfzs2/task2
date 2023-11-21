<!DOCTYPE html>
<html>
<head>
    <title>Display Gallery</title>
    <style>
        /* Style the container where the image gallery will be displayed */
        .gallery-container {
            width: 800px; /* Adjust width as needed */
            margin: 20px auto;
            border: 3px solid #ccc;
            padding: 10px;
            overflow-y: auto; /* Add a vertical scrollbar when needed */
            max-height: 600px; /* Set a maximum height for the container */
        }
        .gallery-image {
            width: 200px;
            height: 200px;
            margin: 10px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="gallery-container">
        <h2>Image Gallery</h2>
        <?php
        $folderPath = $_SERVER['DOCUMENT_ROOT'] . '/Document Management System/Captured Images/'; // Update with your directory path

        $imageFiles = glob($folderPath . '*.{jpg,jpeg,png,gif}', GLOB_BRACE); // Get image files in the directory

        if ($imageFiles !== false && count($imageFiles) > 0) {
            foreach ($imageFiles as $file) {
                $imageUrl = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);
                echo "<img src='$imageUrl' alt='" . basename($file) . "' class='gallery-image'>";
            }
        } else {
            echo "<p>No image files found in the directory.</p>";
        }
        ?>
    </div>
</body>
</html>
