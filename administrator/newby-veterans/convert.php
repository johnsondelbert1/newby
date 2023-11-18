<?php require_once('includes/dbconnect.php'); ?>
<?php 
	$query = "SELECT * FROM `provided`";
	$result = $db->query($query);
	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$oldDate = strtotime($row['Date']);
		$date = date("Y-m-d H:i:s", $oldDate);
		$sql = "UPDATE `provided` SET `Date` = :Date WHERE `id` = ".$row['id'];

		$stmt = $db->prepare($sql);
				
		$stmt->bindParam(':Date', $date, PDO::PARAM_STR);

		if(!$stmt->execute()){
			$error = 'Sorry, an error has occured.<br/>';
			foreach($stmt->errorInfo() as $execError){
				$error .= $execError.'<br/>';
			}
		}
		echo $date."<br>";
	}
?>