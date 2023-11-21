<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

include $_SERVER['DOCUMENT_ROOT']."/version.php";
$path = $_SERVER['DOCUMENT_ROOT']."/".v."/Common Data/";
set_include_path($path);
include('PHP Modules/mysqliConnection.php');
include('PHP Modules/gerald_functions.php');
include('PHP Modules/anthony_retrieveText.php');
include("PHP Modules/anthony_wholeNumber.php");
include("PHP Modules/rose_prodfunctions.php");
ini_set("display_errors", "on");

PMSResponsive::includeHeader("File Checker");

$ctrl = new PMSDatabase;
$tpl = new PMSTemplates;
$pms = new PMSDBController;
$rdr = new Render\PMSTemplates;

$tpl->setDisplayId("")
    ->setPrevLink("")
    ->createHeader();
?>

<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/searchpanes/2.1.2/css/searchPanes.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.2/css/select.dataTables.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdn.datatables.net/scroller/2.0.5/js/dataTables.scroller.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.5/css/scroller.dataTables.min.css">
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>

        <link rel="stylesheet" href="assets/style1.css">
        <script src="assets/script.js"></script>
</head>

<?php

function getFileType($file) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file); 
    finfo_close($finfo);

    switch ($mime_type) {
        case 'video/mp4':
            return 'Video';
        case 'image/png':
        case 'image/jpeg':
            return 'Image';
        case 'application/pdf':
            return 'PDF';
        case 'audio/mpeg':
            return 'Audio';
        default:
            return 'Unknown';
    }
}

function renameFile($oldName, $newBaseName) {
    $folderPath = $_SERVER['DOCUMENT_ROOT'] . '/Document Management System/Temp Folder/';
    $oldPath = $folderPath . $oldName;
    $extension = pathinfo($oldName, PATHINFO_EXTENSION); 
    $newName = $newBaseName . '.' . $extension; //append the extension again
    $newPath = $folderPath . $newName;

    if (rename($oldPath, $newPath)) {
        return true;
    } else {
        return false;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldName = $_POST['oldName'];
    $newName = $_POST['newName'];

    if (renameFile($oldName, $newName)) {
        // echo '<p>File renamed successfully.</p>';
        echo '
        
        <script>
            alert("File renamed successfully");
        </script>

        
        ';
    } else {
        $error = error_get_last();
    }
}

?>

<body>   
    <div class="containerMain">
        <div style="">
            <button class='myBtn w3-btn w3-round w3-purple' style='width:130px !important; margin-right: 0 !important;' type="button" id="addBtn">Add</button>
        </div>

        <main class="st_viewport">
            <div class="st_wrap_table" data-table_id="0">
                <header class="st_table_header">

                <div class="st_row">
                    <div class="st_column _rank">#</div>
                    <div class="st_column _name">FILE NAME</div>
                    <div class="st_column _surname">FILE TYPE</div>
                </div>
                </header>

                <div class="st_table">

                <?php
                    $folderPath = $_SERVER['DOCUMENT_ROOT'] . '/Document Management System/Temp Folder/';
                    $folderPath = rtrim($folderPath, '/') . '/';

                    if (is_dir($folderPath)) {
                        $files = scandir($folderPath);
                        $files = array_diff($files, array('.', '..'));

                        $fileCount = count($files); 

                        if ($fileCount > 0) {
                            $rowNumber = 1;
                            foreach ($files as $file) {
                                $fileType = getFileType($folderPath . $file);
                                $baseName = pathinfo($file, PATHINFO_FILENAME);
                                $mimeType = mime_content_type($file);
                               
                                echo '<div class="st_row">';
                                    // td 1
                                    echo '<div class="st_column _rank">'.$rowNumber++.'</div>';
                                    // td 2
                                    echo '<div class="st_column _name">';
                                    echo '<form method="POST">';
                                        echo '<input type="hidden" name="oldName" value="' . htmlspecialchars($file) . '">';
                                        echo '<input type="text" name="newName" value="' . htmlspecialchars($baseName) . '">';
                                    echo '</form>';
                                    echo '</div>';
                                    // td 3

                                    echo '<div class="st_column _surname"> '; 
                                    
                                        if ($fileType == 'Video') {
                                            echo '<p class="video-type">Video</p>';
                                        }
                                        elseif ($fileType == 'Image') {
                                            echo '<p class="image-type">Image</p>';
                                        }
                                        elseif ($fileType == 'PDF') {
                                            echo '<p class="pdf-type">PDF</p>';
                                        }
                                        elseif ($fileType == 'Audio') {
                                            echo '<p class="audio-type">Audio</p>';
                                        }
                                        elseif ($fileType == 'Unknown') {
                                            echo '<p class="unknown-type">Unknown</p>';
                                        }

                                    echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<tr><td colspan="3">The folder is empty.</td></tr>';
                        }
                    } else {
                        echo '<tr><td colspan="3">The folder does not exist.</td></tr>';
                    }
                ?>
                </div>
            </div>             
        </main>

        <!-- <p> <?php echo $fileCount; ?></p> -->

        <!-- Modal -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close">&times;</span>
                    <h2>NG Add File</h2>
                </div>
                
                <form action="jera_upload.php" method="post" enctype="multipart/form-data">
                    <div class="row" style="padding: 20px;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <h4 for="" style="margin-bottom: 2rem;"><b>NG NO. Confirmation List</b></h4>

                                <!-- sql and table -->

                                <table id="lotTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th> </th>
                                            <th>CPAR #</th>
                                            <th>Part Number</th>
                                            <th>Lot Number</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <h4 for="" style="margin-bottom: 2rem;"><b>Choose File</b></h4>

                            <div class="drag-area">
                                    <div class="icon">
                                        <i class="fas fa-images"></i>
                                    </div>
                                    <span><label for="fileToUpload" class="custom-file-upload">Browse</label></span>
                                    <input type="file" style="display:none;" name="my_file[]" id="fileToUpload" accept=".jpg, .jpeg, .png, .mp3, .mp4, .pdf" multiple/>
                                    <div class="file-preview" id="selectedFilePreview"  style="display:none;"></div>
                                </div>
                            </div>
                            <div class="file-extension">
                                <label for="date">File Type: </label>  
                                <span id="fileExtension"></span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <!-- <button type="button" style="font-size: 15px;" id="cancelBtn" class="btn btn-secondary" data-dismiss="modal">Cancel</button> -->
                        <button type="submit" style="font-size: 15px;" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</body>
</html>

<script>
    console.log('test');

    var modal = document.getElementById("myModal");
    var addBtn = document.getElementById("addBtn");

    var closeBtn = modal.querySelector(".close");

    addBtn.addEventListener("click", function() {
        modal.style.display = "block";
    });

    closeBtn.addEventListener("click", function() {
        modal.style.display = "none";
    });

    window.addEventListener("click", function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });


</script>

<script>
    const fileInput = document.getElementById("fileToUpload");
    const selectedFilePreview = document.getElementById("selectedFilePreview");
    const icon = document.querySelector(".icon");
    const customFileUploadLabel = document.querySelector(".custom-file-upload");

    fileInput.addEventListener("change", function () {
        const file = fileInput.files[0];

        if (file) {
            let fileType = file.type;
            let fileName = file.name;
            let fileExtensionText = fileName.split('.').pop().toUpperCase();
            fileExtension.textContent = fileExtensionText;

            let validExtensions = ["image/jpeg", "image/jpg", "image/png", "audio/mpeg", "video/mp4", "application/pdf"];

            if (validExtensions.includes(fileType)) {
                let fileReader = new FileReader();

                fileReader.onload = () => {
                    let fileURL = fileReader.result;
                    if (fileType.startsWith("image/")) {
                        let imgTag = `<img src="${fileURL}" alt="">`;
                        selectedFilePreview.innerHTML = imgTag;
                    } else if (fileType.startsWith("audio/")) {
                        let audioTag = `<audio controls><source src="${fileURL}" type="${fileType}"></audio>`;
                        selectedFilePreview.innerHTML = audioTag;
                    } else if (fileType.startsWith("video/")) {
                        let videoTag = `<video controls><source src="${fileURL}" type="${fileType}"></video>`;
                        selectedFilePreview.innerHTML = videoTag;
                    } else if (fileType === "application/pdf") {
                        let pdfLink = `<a href="${fileURL}" target="_blank">Open PDF</a>`;
                        selectedFilePreview.innerHTML = pdfLink;
                    }

                    // Hide the icon and label
                    icon.style.display = "none";
                    customFileUploadLabel.style.display = "none";
                    selectedFilePreview.style.display = "block";
                };
                fileReader.readAsDataURL(file);
            } else {
                alert("This is not a supported file type.");
                fileInput.value = null; t
                selectedFilePreview.innerHTML = ""; 
            }
        } else {
            selectedFilePreview.innerHTML = ""; 
        }
    });

   
</script>

<script>
     function loadDataIntoTable() {
        $.ajax({
            url: 'jera_tableAjax.php', 
            method: 'POST',
            data: { action: 'getCPARIds' },
            success: function (response) {
                var lotTableBody = $('#lotTable tbody');
                lotTableBody.empty(); 
                var data = JSON.parse(response);

                var rowCounter = 1;

                data.forEach(function (item) {
                    var row = '<tr><td>' + rowCounter++ + '</td><td>' + item.cparId + '</td><td>' + item.partNumber + '</td><td>' + item.lotNumber + '</td></tr>';
                    lotTableBody.append(row);
                });
            },
            error: function (error) {
                console.error('Error:', error);
            }
        });
    }

    $(document).ready(function () {
        loadDataIntoTable();
    });
</script>