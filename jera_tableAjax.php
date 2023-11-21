<?php
include $_SERVER['DOCUMENT_ROOT']."/version.php";
$path = $_SERVER['DOCUMENT_ROOT']."/".v."/Common Data/";
set_include_path($path);
include('Templates/mysqliConnection.php');
include('PHP Modules/gerald_functionDemo.php');
include('PHP Modules/anthony_retrieveText.php');
ini_set("display_errors", "on");

if (isset($_POST['action']) && $_POST['action'] === 'getCPARIds') {
    $param = isset($_POST['parameter'])? $_POST['parameter'] : "";
    $cparId     = isset($_POST['cparId'])? $_POST['cparId'] : "";
    $partNumber = isset($_POST['partNumbers'])? $_POST['partNumbers'] : "";
    
    $data = array();
    $data2 = array();

    $sql = "SELECT qc.cparId, qc.lotNumber, cp.partNumber
    FROM qc_cparlotnumber AS qc
    INNER JOIN ppic_lotlist AS pp ON pp.lotNumber = qc.lotNumber
    INNER JOIN cadcam_parts AS cp ON pp.partId = cp.partId
    INNER JOIN qc_cpar AS cpar ON qc.cparId = cpar.cparId
    WHERE cpar.cparId IN (SELECT cparId FROM qc_cpar WHERE YEAR(cparIssueDate) = YEAR(CURDATE()) AND MONTH(cparIssueDate) IN (MONTH(CURDATE()), MONTH(CURDATE()) - 1) ORDER BY cparIssueDate DESC)
    ORDER BY cpar.cparIssueDate DESC, cpar.cparId DESC";

    if(!empty($param)){
        $sql = "SELECT qc.cparId, qc.lotNumber, cp.partNumber
        FROM qc_cparlotnumber AS qc
        INNER JOIN ppic_lotlist AS pp ON pp.lotNumber = qc.lotNumber
        INNER JOIN cadcam_parts AS cp ON pp.partId = cp.partId
        INNER JOIN qc_cpar AS cpar ON qc.cparId = cpar.cparId
        WHERE cpar.cparId LIKE '$param'
        ORDER BY cpar.cparIssueDate DESC, cpar.cparId DESC";
    }

    if(!empty($cparId)){
        $sql = "SELECT qc.cparId, qc.lotNumber, cp.partNumber
        FROM qc_cparlotnumber AS qc
        INNER JOIN ppic_lotlist AS pp ON pp.lotNumber = qc.lotNumber
        INNER JOIN cadcam_parts AS cp ON pp.partId = cp.partId
        INNER JOIN qc_cpar AS cpar ON qc.cparId = cpar.cparId
        WHERE cpar.cparId LIKE '%$cparId%' 
        ORDER BY cpar.cparIssueDate DESC, cpar.cparId DESC";
    }

    if (!empty($partNumber)) {
        $sql = "SELECT qc.cparId, qc.lotNumber, cp.partNumber
        FROM qc_cparlotnumber AS qc
        INNER JOIN ppic_lotlist AS pp ON pp.lotNumber = qc.lotNumber
        INNER JOIN cadcam_parts AS cp ON pp.partId = cp.partId
        INNER JOIN qc_cpar AS cpar ON qc.cparId = cpar.cparId
        WHERE  cp.partNumber LIKE '%$partNumber%'
        ORDER BY cpar.cparIssueDate DESC, cpar.cparId DESC";
    }

    if (!empty($partNumber) AND !empty($cparId) ) {
        $sql = "SELECT qc.cparId, qc.lotNumber, cp.partNumber
        FROM qc_cparlotnumber AS qc
        INNER JOIN ppic_lotlist AS pp ON pp.lotNumber = qc.lotNumber
        INNER JOIN cadcam_parts AS cp ON pp.partId = cp.partId
        INNER JOIN qc_cpar AS cpar ON qc.cparId = cpar.cparId
        WHERE  cp.partNumber LIKE '%$partNumber%' AND cpar.cparId LIKE '%$cparId%'
        ORDER BY cpar.cparIssueDate DESC, cpar.cparId DESC";
    } 
      
    $result = mysqli_query($db, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $cparId = $row['cparId'];
            $lotNumber = $row['lotNumber'];
            $partNumber = $row['partNumber'];

			$extensions = ['jpg', 'pdf', 'jpeg', 'png'];

			$flag = 0;
			for ($i = 0; $i < 10; $i++) {
				foreach ($extensions as $extension) {
					$filePath = $_SERVER['DOCUMENT_ROOT'] . '/Document Management System/CPAR Folder/' . $cparId . '(' . $lotNumber . ')_' . $i . '.' . $extension;
					if (file_exists($filePath)) {
						$flag = 1;
						break 2; // Exit both loops when a file is found
					}
				}
			}


            if (file_exists($fileLocation)) {
                error_log("File for CPAR ID $cparId exists at $fileLocation");
            } else {
                error_log("File for CPAR ID $cparId does not exist at $fileLocation");
                
                if($flag == 0) { //no underscore
                    $data[] = array(
                        'cparId' => $cparId, 
                        'lotNumber' => $lotNumber,
                        'partNumber' => $partNumber
                    );
                }
				elseif ($flag == 1) { //with underscore
					$data2[] = array(
						'cparId' => $cparId,
						'lotNumber' => $lotNumber,
						'partNumber' => $partNumber
					);
				}
            }
        }
    } else {
        echo json_encode(array('error' => 'Query 1 failed.'));
    }

    // Return both sets of data as JSON
    echo json_encode(array('data' => $data , 'data2' => $data2, 'sql'=>$sql));
} else {
    // Handle other cases if needed
}
?>
