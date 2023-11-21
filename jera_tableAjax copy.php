<?php
include $_SERVER['DOCUMENT_ROOT']."/version.php";
$path = $_SERVER['DOCUMENT_ROOT']."/".v."/Common Data/";
set_include_path($path);
include('PHP Modules/mysqliConnection.php');
include('PHP Modules/gerald_functions.php');
include('PHP Modules/anthony_retrieveText.php');
include("PHP Modules/anthony_wholeNumber.php");
include("PHP Modules/rose_prodfunctions.php");
ini_set("display_errors", "on");

if (isset($_POST['action']) && $_POST['action'] === 'getCPARIds') {
    $data = array();

    $sql = "SELECT qc.cparId, qc.lotNumber, cp.partNumber
			FROM qc_cparlotnumber AS qc
			INNER JOIN ppic_lotlist AS pp ON pp.lotNumber = qc.lotNumber
			INNER JOIN cadcam_parts AS cp ON pp.partId = cp.partId
			WHERE cparId IN (
				SELECT cparId
				FROM qc_cpar
				WHERE YEAR(cparIssueDate) = YEAR(CURDATE())
				AND MONTH(cparIssueDate) IN (MONTH(CURDATE()), MONTH(CURDATE()) - 1))
			";
    
    $result = mysqli_query($db, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $cparId = $row['cparId'];
            $lotNumber = $row['lotNumber'];
			$partNumber = $row['partNumber'];
            $fileFlag = 0;

            $fileLocation = $_SERVER['DOCUMENT_ROOT']."/Document Management System/CPAR Folder/" . $cparId . "(" . $lotNumber . ")" . ".jpg";
            $fileLocation2 = $_SERVER['DOCUMENT_ROOT']."/Document Management System/CPAR Folder/" . $cparId . "(" . $lotNumber . ")_0" . ".jpg";

			if(file_exists($fileLocation) OR file_exists($fileLocation2))
			{
				$fileFlag = 1;
			}
			else
			{
				$fileLocation = $_SERVER['DOCUMENT_ROOT']."/Document Management System/CPAR Folder/".$cparId."(".$lotNumber.")".".pdf";
				$fileLocation2 = $_SERVER['DOCUMENT_ROOT']."/Document Management System/CPAR Folder/".$cparId."(".$lotNumber.")_0".".pdf";

				if(file_exists($fileLocation) OR file_exists($fileLocation2))
				{
					$fileFlag = 1;
				}
				else
				{
					$fileLocation = $_SERVER['DOCUMENT_ROOT']."/Document Management System/CPAR Folder/".$cparId."(".$lotNumber.")".".jpg";
					$fileLocation2 = $_SERVER['DOCUMENT_ROOT']."/Document Management System/CPAR Folder/".$cparId."(".$lotNumber.")_0".".jpg";

					if(file_exists($fileLocation) OR file_exists($fileLocation2))
					{
						$fileFlag = 1;
					}
					else
					{
						$fileLocation = $_SERVER['DOCUMENT_ROOT']."/Document Management System/CPAR Folder/".$cparId."(".$lotNumber.")".".jpeg";
						$fileLocation2 = $_SERVER['DOCUMENT_ROOT']."/Document Management System/CPAR Folder/".$cparId."(".$lotNumber.")_0".".jpeg";
						
						if(file_exists($fileLocation) OR file_exists($fileLocation2))
						{
							$fileFlag = 1;
						}
						else
						{
							$fileLocation = $_SERVER['DOCUMENT_ROOT']."/Document Management System/CPAR Folder/".$cparId."(".$lotNumber.")".".png";
							$fileLocation2 = $_SERVER['DOCUMENT_ROOT']."/Document Management System/CPAR Folder/".$cparId."(".$lotNumber.")_0".".png";

							if(file_exists($fileLocation) OR file_exists($fileLocation2))
							{
								$fileFlag = 1;
							}
						}
					}
				}
			}				

            if (file_exists($fileLocation)) {
                error_log("File for CPAR ID $cparId exists at $fileLocation");
            } else {
                error_log("File for CPAR ID $cparId does not exist at $fileLocation");
                
                if($fileFlag == 0) {
                    $data[] = array(

                        'cparId' => $cparId, 
                        'lotNumber' => $lotNumber,
                        'partNumber' => $partNumber
                    );
                }
                
            }

        }

        echo json_encode($data);
    } else {
        echo json_encode(array('error' => 'Query failed.'));
    }
} else {
}

?>
