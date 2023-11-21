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

        /* Additional styles for individual images */
        .image-container {
            width: 200px;
            height: 260px;
            margin: 10px;
            overflow: hidden;
            text-align: center;
        }

        .image-container img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .image-label {
            display: block;
            text-align: left;
            margin-top: 5px;
        }
    </style>
    <script>
        function clickCheckbox(image) {
            image.previousElementSibling.querySelector('input[type="checkbox"]').checked = true;
        }
    </script>
</head>
<body>
    <div class="gallery-container">
        <h2>Image Gallery</h2>
        <?php
        function displayImageGallery($directory)
        {
            if (!empty($directory) && is_dir($directory)) {
                $files = scandir($directory);

                echo '<div style="display: flex; flex-wrap: wrap;">'; // Create a flex container for the grid

                foreach ($files as $file) {
                    if ($file != "." && $file != "..") {
                        echo '<div class="image-container">';
                        echo '<label class="image-label">';
                        echo '<input type="checkbox" name="selected_files[]" value="' . $file . '" style="display: none;">';
                        echo '<img src="' . $directory . $file . '" onclick="clickCheckbox(this)">'; // Updated onclick event
                        echo '</label>';
                        echo '<label class="image-label">';
                        echo '<input type="checkbox" name="selected_files[]" value="' . $file . '"> ' . $file . '</label>';
                        echo '</div>';
                    }
                }

                echo '</div>'; // Close the flex container
            } else {
                echo 'Directory does not exist or is empty.';
            }
        }

        $folderPath = $_SERVER['DOCUMENT_ROOT'] . '/Document Management System/Captured Images/';

        displayImageGallery($folderPath); // Call the function to display the image gallery
        ?>
    </div>
</body>
</html>
