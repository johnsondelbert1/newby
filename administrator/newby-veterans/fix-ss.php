<?php require_once('includes/dbconnect.php'); ?>
<?php 
	$query = "SELECT * FROM `veterans`";
	$result = $db->query($query);
	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$ss = $row['SS_Last_Four'];
		$ssLength = strlen($ss);

		if($ssLength > 0 && $ssLength < 4){
			switch ($ssLength) {
				case 1:
					$newSS = '000'.$ss;
					break;
				case 2:
					$newSS = '00'.$ss;
					break;
				case 3:
					$newSS = '0'.$ss;
					break;
			}
			echo $newSS.'<br/>';
			$sql = "UPDATE `veterans` SET `SS_Last_Four` = :SS_Last_Four WHERE `ServiceRecordID` = ".$row['ServiceRecordID'];

			$stmt = $db->prepare($sql);
					
			$stmt->bindParam(':SS_Last_Four', $newSS, PDO::PARAM_STR);

			if(!$stmt->execute()){
				$error = 'Sorry, an error has occured.<br/>';
				foreach($stmt->errorInfo() as $execError){
					$error .= $execError.'<br/>';
				}
				echo $error;
			}
		}


		
	}
?>