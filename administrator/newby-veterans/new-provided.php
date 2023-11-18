<?php require_once('includes/dbconnect.php'); ?>
<?php
	if(isset($_POST['addprov'])){
		if($_POST['CustomerID']!=''){
			//check if Cust ID exists
			$query = "SELECT * FROM `customers` WHERE `CustomerID` = ".$_POST['CustomerID'];
			$result = $db->query($query);
			if($result->rowCount()>=1){

				$date = date("Y/m/d H:i:s", time());
				$provided = trim($_POST['Provided']);
				if($provided!=''){

					$sql = "INSERT INTO `provided` (`CustomerID`, `Date`, `Provided`) VALUES (:CustomerID, :Date, :Provided)";

					$stmt = $db->prepare($sql);
					
					$stmt->bindParam(':CustomerID', $_POST['CustomerID'], PDO::PARAM_STR);
					$stmt->bindParam(':Date', $date, PDO::PARAM_STR);
					$stmt->bindParam(':Provided', $_POST['Provided'], PDO::PARAM_STR);
					if($stmt->execute()){
						$success = 'Provided Added';
					}else{
						$error = 'Sorry, an error has occured.<br/>';
						foreach($stmt->errorInfo() as $execError){
							$error .= $execError.'<br/>';
						}
					}
				}else{
					$error = 'Provided field empty.';
				}
			}else{
				$error = 'Customer ID does not exist.';
			}
		}else{
			$error = 'Cannot have blank Customer ID.';
		}
	}
?>
<html>
	<head>
		<title>New Provided</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<div id="nav">
			<ul>
				<li><a href="index.php">Customers</a></li>
				<li><a href="veterans.php">Veterans</a></li>
				<li><a href="provided.php">Provided</a></li>
			</ul>
		</div>
		<div id="content">
			<h1>Adding New Provided</h1>
			<?php if(isset($error)){echo '<div class="error">'.$error.'</div>';} ?>
			<?php if(isset($success)){echo '<div class="success">'.$success.'</div>';} ?>
			<form method="post">
				<table class="form">
					<tr>
						<td>Customer ID</td>
						<td><input type="text" name="CustomerID" value="<?php if(isset($_POST['CustomerID'])){echo $_POST['CustomerID'];}elseif(isset($_GET['cust_id'])){echo urldecode($_GET['cust_id']);} ?>" /></td>
					</tr>
					<tr>
						<td>Provided</td>
						<td><textarea name="Provided" rows="5" cols="50"><?php if(isset($_POST['Provided'])){echo $_POST['Provided'];} ?></textarea></td>
					</tr>
				</table>
				<input type="submit" name="addprov" value="Add New Provided" />
			</form>
		</div>
	</body>
</html>