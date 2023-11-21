<?php
include $_SERVER['DOCUMENT_ROOT'] . "/version.php";
$path = $_SERVER['DOCUMENT_ROOT'] . "/" . v . "/Common Data/";
set_include_path($path);
include('PHP Modules/mysqliConnection.php');
include('PHP Modules/gerald_functions.php');
include('PHP Modules/anthony_retrieveText.php');
include("PHP Modules/anthony_wholeNumber.php");
include("PHP Modules/rose_prodfunctions.php");
ini_set("display_errors", "on");

if (isset($_POST['action']) && $_POST['action'] === 'getCparIdAndLotNumber') {
    $pTagValue = $_POST['pTagValue'];

    $sql = "SELECT qc.cparId, qc.lotNumber
            FROM qc_cparlotnumber AS qc
            INNER JOIN ppic_lotlist AS pp ON pp.lotNumber = qc.lotNumber
            INNER JOIN cadcam_parts AS cp ON pp.partId = cp.partId
            WHERE qc.lotNumber IN (
                SELECT lotNumber
                FROM ppic_lotlist
                WHERE productionTag = '$pTagValue' 
            )";

    $result = mysqli_query($db, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            $cparId = $row['cparId'];
            $lotNumber = $row['lotNumber'];

            $response1 = array(
                'cparId' => $cparId,
                'lotNumber' => $lotNumber
            );            

            $json_response1 = json_encode($response1);
            header('Content-Type: application/json');
            echo $json_response1;
        } else {
            echo json_encode(array('error' => 'No data found for the entered PTag.'));
        }
    } else {
        echo json_encode(array('error' => 'Query failed: ' . mysqli_error($db)));
    }
}

?>