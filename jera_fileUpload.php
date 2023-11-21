<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/version.php";
$path = $_SERVER['DOCUMENT_ROOT'] . "/" . v . "/Common Data/";
set_include_path($path);
// include('Templates/mysqliConnectionDemo.php');
include('Templates/mysqliConnection.php');
include('PHP Modules/gerald_functionDemo.php');
include('PHP Modules/anthony_retrieveText.php');
ini_set("display_errors", "on");
PMSResponsive::includeHeader("Image-Linking Software");

$ctrl = new PMSDatabase;
$tpl = new PMSTemplates;
$pms = new PMSDBController;
$rdr = new Render\PMSTemplates;

$tpl->setDisplayId("L10001") # OPTIONAL
    ->setPrevLink("/V4/Others/Jeramay/File Checker Inventory/eric_linkingList.php")
    ->createHeader();

$prev = displayText('L3491', 'utf8', 0, 0, 1);
$next = displayText('L4762', 'utf8', 0, 0, 1);


$param = $_GET['cparId'];
$check = (isset($_GET['check']) and !empty($_GET['check']))  ? "checked" : "";

?>



<!DOCTYPE html>
<html lang="en">

<head>

    <link rel="stylesheet" href="assets/style1.css">


    <style>
        .btn-disabled {
            color: white;
            background-color: gray;
        }
    </style>
</head>

<?php

function getFileType($file)
{
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

function renameFile($oldName, $newBaseName)
{
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

<style>
    /* mobile step-by-step process*/
    .drag-area {
        padding-top: 155px !important;
    }

    @media (max-width: 585px) {
        .step {
            display: none;
        }

        #step1 {
            display: block;
        }

        .form-group {
            height: 62vh !important;
        }

        #listView {
            height: 100vh !important;
        }

        #nextBtn {
            float: right !important;
            margin-right: 1rem !important;
        }

        #prevBtn {
            display: none;
            float: left !important;
            margin-left: 1rem !important;
        }

        .containerMain {
            width: 95%;
            padding: 0 !important;
            padding-bottom: 20px !important;

        }

        .toggle-container {
            margin-top: 4rem !important;
            float: left !important;
            align-items: center;
            text-align: center;
        }

        body {
            font-size: 8px !important;
        }
    }

    @media (min-width: 1024px) {

        #nextBtn,
        #prevBtn {
            display: none !important;
        }
    }
</style>

<body>
    <div class="containerMain">
        <!-- <form action="jera_upload.php" method="post" enctype="multipart/form-data"> -->
        <div class="row" style="padding: 20px;">
            <div class="col-md-6">

                <!-- step 2 -->
                <div class="form-group step" id="step2" style="overflow: hidden;">
                    <div style="display: flex; align-items: center; margin-bottom: 1.5rem !important;">
                        <h4 for=""><b><?php echo displayText('L349', 'utf8', 0, 0, 1); ?></b></h4>
                        <div class="toggle-container">
                            <input type="checkbox" id="tableToggle">
                            <a class="toggle" id="listAnchor"><?php echo displayText('L915', 'utf8', 0, 0, 1); ?></a>
                            <a class="toggle" id="inputAnchor"><?php echo displayText('L3855', 'utf8', 0, 0, 1); ?></a>
                        </div>
                    </div>

                    <div class="row g-3 mx-1">
                        <div class="col">
                            <input type="text" class="form-control" placeholder="<?php echo displayText('L334', 'utf8', 0, 0, 1); ?>" id="cparIdFilter" aria-label="Inventory ID..">
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" placeholder="<?php echo displayText('L4737', 'utf8', 0, 0, 1); ?>" id='partNumberFilter' aria-label=" Supplies..">
                        </div>
                        <div class="col-auto">
                            <button class='w3-btn w3-green' id='searchBtn'><b><i class="fa fa-search" aria-hidden="true"></i> | SEARCH</b></button>
                        </div>
                    </div>


                    <div id="listView">
                        <table id="lotTable" class="table table-bordered table-wrapper-scroll-y my-custom-scrollbar">
                            <thead style="position: sticky; top: 0; background-color: #fff; z-index: 1;height: 40px;background-color:indigo; color: white;">
                                <tr>
                                    <th> </th>
                                    <th><?php echo displayText('L10002', 'utf8', 0, 0, 1); ?></th>
                                    <th><?php echo displayText('L28', 'utf8', 0, 0, 1); ?></th>
                                    <th><?php echo displayText('L45', 'utf8', 0, 0, 1); ?></th>
                                    <th><?php echo displayText('L547', 'utf8', 0, 0, 1); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>


                    <div id="fileExistView" style="display:none; top: 0;overflow:hidden">
                        <!-- <table id="lotTable" class="table table-bordered table-wrapper-scroll-y my-custom-scrollbar"> -->
                        <table id="fileExistTable" class="table table-bordered table-wrapper-scroll-y my-custom-scrollbar">
                            <thead style="position: sticky; top: 0; background-color: #fff; z-index: 1;height: 40px;background-color:indigo; color: white;">
                                <tr>
                                    <th> </th>
                                    <th><?php echo displayText('L10002', 'utf8', 0, 0, 1); ?></th>
                                    <th><?php echo displayText('L28', 'utf8', 0, 0, 1); ?></th>
                                    <th><?php echo displayText('L45', 'utf8', 0, 0, 1); ?></th>
                                    <th><?php echo displayText('L547', 'utf8', 0, 0, 1); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>


                    <div id="inputView" style="display: none;">
                        <div class="input">
                            <label for="ptag"><?php echo displayText('L286', 'utf8', 0, 0, 1); ?></label>
                            <input type="text" placeholder="" class="inputPtag" id="ptagInput" name="pTagValue">
                            <!-- <a class="enter-button" id="linkToCpar">Link</a> -->
                            <a id="inputViewButton" disabled class="btn btn-secondary"><?php echo displayText('L4051', 'utf8', 0, 0, 1); ?></a>
                            <!-- <a id="inputViewButton" class="">Link</a> -->
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="cparId" id="cparIdInput">
            <input type="hidden" name="lotNumber" id="lotNumberInput">

            <!-- step 1 -->
            <div class="col-md-6 step" id="step1">
                <div class="">
                    <h4 for="" style="margin-bottom: 2rem;">
                        <b><?php echo displayText('L3836', 'utf8', 0, 0, 1); ?></b>
                    </h4>

                    <div class="col-md-12 step" id="step1">
                        <div class="">




                            <!-- drag-area - jovit -->
                            <div class="drag-area" id="dragArea" style="display: flex; flex-wrap: wrap; overflow-x: hidden; overflow-y: auto; height: 400px;">
                                <form action="process_selected_images.php" method="post">
                                    <!-- Replace 'process_selected_images.php' with your processing script -->
                                    <?php
                                    $folderPath = $_SERVER['DOCUMENT_ROOT'] . '/Document Management System/Captured Images/';
                                    $imageFiles = glob($folderPath . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

                                    if ($imageFiles !== false && count($imageFiles) > 0) {
                                        echo "<div style='display: flex; flex-wrap: wrap;'>";
                                        foreach ($imageFiles as $file) {
                                             $imageUrl = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);
                                            $fileName = basename($file);

                                            echo "<label style='flex-basis: 20%;'><input type='checkbox' name='selected_images[]' value='$imageUrl' class='image-checkbox'><img src='$imageUrl' alt='$fileName' class='gallery-image' style='width: 100px; height: 100px; object-fit: cover; margin: 5px;'></label>";
                                        }
                                        echo "</div>";
                                    } else {
                                        echo "<p>No image files found in the directory.</p>";
                                    }
                                    ?>
                                </form>
                            </div>


                            <!-- file extension -->

                            <div class="file-extension">
                                <label for="date"><?php echo displayText('L4161', 'utf8', 0, 0, 1); ?>: </label>
                                <span id="fileExtension"></span>
                            </div>
                            <div class="file-count">
                                <label for="date"><?php echo displayText('L10004', 'utf8', 0, 0, 1); ?>: </label>
                                <span id="fileCount"></span>
                            </div>
                            <!-- Rest of your existing code -->

                            <input type="file" style="display:none;" name="my_file[]" id="fileToUpload" accept=".jpg, .jpeg, .mp3, .mp4" multiple />
                            <div class="file-preview" id="selectedFilePreview" style="display:none;"></div>
                        </div>
                    </div>


                    <!-- <span><label for="fileToUpload" class="custom-file-upload"><?php echo displayText('L10003', 'utf8', 0, 0, 1); ?></label></span> -->
                    <input type="file" style="display:none;" name="my_file[]" id="fileToUpload" accept=".jpg, .jpeg, .mp3, .mp4" multiple />
                    <div class="file-preview" id="selectedFilePreview" style="display:none;"></div>
                </div>

            </div>
        </div>
    </div>
    <!-- </form> -->



    <!-- step 3 -->
    <!-- modal -->
    <div id="successModal" class='modal step' style='display: none; '>
        <div class='modal-dialog'>
            <div class='modal-content' style='margin-top: 30rem; animation: pop-up 0.5s ease-out;border-radius: 2rem;'>
                <div class='modal-header' style="background-color: #96ca2d;border-radius: 2rem 2rem 0 0;">
                    <h5 class='modal-title'>Success</h5>
                    <!-- <button type='button' class='close' data-dismiss='modal'>&times;</button> -->
                </div>
                <div class='modal-body' style="text-align: center;">
                    <img src='assets/images/success1.gif' alt='Success GIF' style="width: 30%; height: auto;">
                    <audio id="successSound" src="assets/sounds/success.wav"></audio>
                    <br>
                    <h1><?php echo displayText('L10005', 'utf8', 0, 0, 1); ?>!</h1>
                </div>
            </div>
        </div>
    </div>
    </div>

    <button id="prevBtn" class="toggle" onclick="showNextStep(true)"><?php echo $prev ?></button>
    <button id="nextBtn" class="toggle" onclick="showNextStep()"><?php echo $next ?></button>

</body>

</html>


<!-- function for file count when imag is clicked - jovit-->
<script>
    // Object to store file types and counts
    var fileDetails = {};

    // Function to update file type and count in HTML elements
    function updateFileDetails() {
        // Extract file types and counts
        var fileExtensions = Object.keys(fileDetails);
        var fileCount = 0;
        var fileTypeString = "";

        // Generate string for file types and update file count
        fileExtensions.forEach(function(extension) {
            fileCount += fileDetails[extension];
            fileTypeString += extension + ",";
        });

        // Remove trailing comma
        fileTypeString = fileTypeString.slice(0, -1);

        // Update file type and count elements
        document.getElementById("fileExtension").textContent = fileTypeString;
        document.getElementById("fileCount").textContent = fileCount;
    }

    // Function to update fileDetails based on checked status
    // function updateFileDetailsOnChange(fileName, isChecked) {
    //     // Extract file extension
    //     var fileExtension = fileName.split('.').pop().toUpperCase();

    //     // Update fileDetails object based on checked status
    //     if (isChecked) {
    //         if (fileDetails[fileExtension]) {
    //             fileDetails[fileExtension]++;
    //         } else {
    //             fileDetails[fileExtension] = 1;
    //         }
    //     } else {
    //         if (fileDetails[fileExtension] && fileDetails[fileExtension] > 0) {
    //             fileDetails[fileExtension]--;
    //             if (fileDetails[fileExtension] === 0) {
    //                 delete fileDetails[fileExtension];
    //             }
    //         }
    //     }

    //     // Update file type and count in HTML elements
    //     updateFileDetails();
    // }
</script>





<script>
    const fileInput = document.getElementById("fileToUpload");

    const selectedFilePreview = document.getElementById("selectedFilePreview");
    const customFileUploadLabel = document.querySelector(".custom-file-upload");
    const param = '<?php echo $param; ?>';
    const check = '<?php echo $check; ?>';
    let cpartIdFilter, partNumberFilter;



    fileInput.addEventListener("change", function() {
        const file = fileInput.files;
        const count = fileInput.files.length;
        // console.log(count)


        $("#fileCount").html(count);
        const isFileSelected = fileInput.files.length > 0;

        if (file) {
            let fileName = file.name;
            let fileExtensionText;

            let validExtensions = ["image/jpeg", "image/jpg", "audio/mpeg", "video/mp4"];
            for (let i = 0; i < file.length; i++) {
                let currentFile = file[i];
                const fileType = currentFile.type;
                let fileExtensionText = currentFile.name.split('.').pop().toUpperCase();
                document.getElementById('fileExtension').textContent = fileExtensionText;

                if (validExtensions.includes(fileType)) {
                    let fileReader = new FileReader()

                    fileReader.onload = () => {
                        let fileURL = fileReader.result;
                        if (fileType.startsWith("image/")) {
                            let imgTag = `<img src="${fileURL}" alt="">`;
                            selectedFilePreview.innerHTML = imgTag;
                        } else if (fileType.startsWith("audio/")) {
                            let audioTag =
                                `<audio controls><source src="${fileURL}" type="${fileType}"></audio>`;
                            selectedFilePreview.innerHTML = audioTag;
                        } else if (fileType.startsWith("video/")) {
                            let videoTag =
                                `<video controls><source src="${fileURL}" type="${fileType}"></video>`;
                            selectedFilePreview.innerHTML = videoTag;
                        } else if (fileType === "application/pdf") {
                            let pdfLink = `<a href="${fileURL}" target="_blank">Open PDF</a>`;
                            selectedFilePreview.innerHTML = pdfLink;
                        }

                        customFileUploadLabel.style.display = "none";
                        selectedFilePreview.style.display = "block";

                        updateLinkButtonDisabled(isFileSelected);
                    };
                    fileReader.readAsDataURL(currentFile);
                } else {
                    // alert("This is not a supported file type.");
                    fileInput.value = null;
                    selectedFilePreview.innerHTML = "";
                }
            }
        } else {
            selectedFilePreview.innerHTML = "";
            updateLinkButtonDisabled(isFileSelected);
        }

    });

    // Function to update fileDetails based on checked status
    function updateFileDetailsOnChange(fileName, isChecked) {
        // Extract file extension
        var fileExtension = fileName.split('.').pop().toUpperCase();

        // Initialize count for this extension if it doesn't exist
        if (!fileDetails[fileExtension]) {
            fileDetails[fileExtension] = 0;
        }

        // Update fileDetails object based on checked status
        if (isChecked) {
            fileDetails[fileExtension]++;
        } else {
            if (fileDetails[fileExtension] > 0) {
                fileDetails[fileExtension]--;
            }
        }

        // Update file type and count in HTML elements
        updateFileDetails();
    }



    const copyImages = (cparId, lotNumber) => {
        // Get the values of the checked checkboxes
        const selectedImages = Array.from(document.querySelectorAll('input[type="checkbox"]:checked'))
            .map(checkbox => checkbox.value);

        console.log(selectedImages, cparId, lotNumber)

        fetch('eric_copy.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    selected_images: selectedImages,
                    cparId: cparId,
                    lotNumber: lotNumber
                }),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                alert('Images copied successfully!');
                // Handle any additional success actions if needed
            })
            .catch(error => {
                console.error('Error copying images:', error);
                // Handle errors if needed
            });
    }




    // remove disable attribute in linkBtn if files were selected
    function updateLinkButtonDisabled(isDisabled) {
        const linkButtons = document.querySelectorAll("#linkBtn");
        const inputViewButton = document.getElementById("inputViewButton");

        linkButtons.forEach(function(button) {
            button.disabled = !isDisabled;
            button.classList.remove("btn-secondary");
            button.classList.add("btn-primary");
        });

        if (inputViewButton) {
            // a.disabled = false;
            inputViewButton.disabled = !isDisabled;
            inputViewButton.classList.remove("btn-secondary");
            inputViewButton.classList.add("btn-primary");
        }

    }


    document.addEventListener("DOMContentLoaded", function() {
        var checkboxes = document.querySelectorAll('.image-checkbox');
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function(event) {
                updateFileDetailsOnChange(event.target.value, event.target.checked);
                updateLinkButtonDisabled(document.querySelectorAll('.image-checkbox:checked').length > 0);
            });
        });
    });

    let currentStep = 1;

    function showNextStep(backward = false) {
        const currentStepElement = document.getElementById(`step${currentStep}`);
        let nextStep = currentStep + (backward ? -1 : 1);
        const nextStepElement = document.getElementById(`step${nextStep}`);
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');

        if (nextStepElement) {
            currentStepElement.style.display = 'none';
            nextStepElement.style.display = 'block';
            currentStep = nextStep;

            if (currentStep === 1) {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'block';
            } else if (currentStep === 2) {
                prevBtn.style.display = 'block';
                nextBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'block';
                nextBtn.style.display = 'block';
            }
        }
    }






    function populateTable(data, tableBody) {
        var rowCounter = 1;
        tableBody.empty();

        data.forEach(function(item) {
            var row = '<tr><td>' +
                rowCounter++ +
                '</td><td>' + item.cparId +
                '</td><td>' + item.partNumber +
                '</td><td>' + item.lotNumber +
                '</td><td><button class="btn btn-secondary" disabled id="linkBtn" onclick="submitFormWithParams(\'' +
                item.cparId + '\', \'' + item.lotNumber +
                '\')"><?php echo displayText('L4051', 'utf8', 0, 0, 1); ?></button></td></tr>';

            tableBody.append(row);
        });
    }

    if (check == 'checked') {
        console.log(check)
        const tableToggle = document.getElementById("tableToggle");
        const listView = document.getElementById("listView");
        const fileExistView = document.getElementById("fileExistView");
        $('#tableToggle').trigger("click");

        listView.style.display = "none";
        fileExistView.style.display = "block";
        $("#inputView").hide();
        $("#inputAnchor").removeClass("active");
        $("#listAnchor").addClass("active");
    } else {
        console.log("has")
        // $('#tableToggle').prop("checked", true);
        // $('#tableToggle').trigger("click");
    }

    $("#searchBtn").click((event) => {
        event.preventDefault()
        cpartIdFilter = $("#cparIdFilter").val();
        partNumberFilter = $("#partNumberFilter").val();
        console.log(cpartIdFilter, partNumberFilter)
        loadDataIntoTable();
    })


    function loadDataIntoTable() {
        $.ajax({
            url: 'jera_tableAjax.php',
            method: 'POST',
            data: {
                action: 'getCPARIds',
                parameter: param.split("<br>").join(""),
                cparId: cpartIdFilter,
                partNumbers: partNumberFilter,
            },
            success: function(response) {
                var data = JSON.parse(response);

                var tableToggle = document.getElementById("tableToggle");
                var lotTableBody = $('#lotTable tbody');
                var fileExistTableBody = $('#fileExistTable tbody');

                if (tableToggle.checked) {
                    fileExistTableBody.empty();
                    populateTable(data.data2, fileExistTableBody);


                } else {
                    lotTableBody.empty();
                    populateTable(data.data, lotTableBody);

                    populateTable(data.data2, fileExistTableBody);

                }
                console.log(data);
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }

    $(document).ready(function() {
        loadDataIntoTable();

        var cparId = 'hello';
        var lotNumber;

        <?php
        if (isset($_SESSION['uploadSuccess']) && $_SESSION['uploadSuccess'] === true) {
            echo "
                $('#successModal').css('display', 'block');

                var successSound = document.getElementById('successSound');
                if (successSound) {
                    successSound.play();
                }

                setTimeout(function() {
                    $('#successModal').css('display', 'none');
                }, 3000);
                location.href = '../../../6-4 CPAR List/gerald_cparList.php';
                ";
            unset($_SESSION['uploadSuccess']);
        }
        ?>

        // toggle tables
        const tableToggle = document.getElementById("tableToggle");
        const listView = document.getElementById("listView");
        const fileExistView = document.getElementById("fileExistView");

        tableToggle.addEventListener("change", function() {
            if (tableToggle.checked) {
                listView.style.display = "none";
                fileExistView.style.display = "block";
                $("#inputView").hide();
                $("#inputAnchor").removeClass("active");
                $("#listAnchor").addClass("active");
            } else {
                listView.style.display = "block";
                fileExistView.style.display = "none";
            }
        });
        // end of toggle tables      

        $('#lotTable tbody').on('click', '.link-button', function() {});
        $("#listAnchor").addClass("active");
        $("#listAnchor").on("click", function() {
            $("#listView").show();
            $("#inputView").hide();
            fileExistView.style.display = "none";
            $(this).addClass("active");
            $("#inputAnchor").removeClass("active");
            tableToggle.checked = false;
        });
        $("#inputAnchor").on("click", function() {
            tableToggle.checked = false;
            $("#listView").hide();
            $("#fileExistView").hide();
            $("#inputView").show();
            $(this).addClass("active");
            $("#listAnchor").removeClass("active");
        });

        $('#inputViewButton').on('click', function() {
            var pTagValue = $('#ptagInput').val();
            // alert(pTagValue);

            $.ajax({
                url: 'jera_getPTag.php',
                method: 'POST',
                data: {
                    action: 'getCparIdAndLotNumber',
                    pTagValue: pTagValue,
                },
                success: function(response1) {
                    // console.log('Response:', response1);
                    try {
                        var data = response1;
                        if (data.cparId !== null && data.cparId !== '') {
                            var cparId = data.cparId;
                            var lotNumber = data.lotNumber;
                            console.log('cparId:', cparId);
                            console.log('lotNumber:', lotNumber);
                            submitFormWithParams(cparId, lotNumber);
                            // updateLinkButtonDisabled(false);
                        } else {
                            alert('CPAR ID is empty or null. Please check your PTag.');
                            updateLinkButtonDisabled(true);
                        }
                    } catch (e) {
                        console.error('Error parsing JSON response:', e);
                        alert('Error parsing JSON response');
                        // updateLinkButtonDisabled(true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                }
            });
        });
    });

    function printCparId(cparId) {
        var cparIdCell = document.getElementById('cparIdCell');
        var cparId = cparIdCell.textContent;
        console.log('Clicked Link for CPAR ID:', cparId);
    }

    function submitFormWithParams(cparId, lotNumber) {

        // e.preventDefault();

        copyImages(cparId, lotNumber);
        console.log('cparId:', cparId);
        // $('form').attr('action', 'jera_upload.php?cparId=' + cparId + '&lotNumber=' + lotNumber);

        // $('form').submit();

    }
</script>