<?php require_once('includes/dbconnect.php'); ?>
<?php
	if(isset($_POST['editvet'])){
		if($_POST['FirstName']!=''||$_POST['LastName']!=''){
			if(strtotime($_POST['date_of_birth']) !== false){
				$dob = date('Y-m-d', strtotime($_POST['date_of_birth']));
				$postData = $_POST;
				unset($postData['editvet']);
				$postData['date_of_birth'] = $dob;
				$_POST['date_of_birth'] = $dob;
				$postCount = count($postData);
				$i=0;

				$sql = "UPDATE `veterans` SET ";

				foreach($postData as $postName => $postVal){
					$i++;
					$sql .="`".$postName."` = :".$postName;
					if($i!=$postCount){
						$sql.=', ';
					}
				}
				$sql .=" WHERE `ServiceRecordID` = ".urldecode($_GET['id']);

				$stmt = $db->prepare($sql);

				foreach($postData as $postName => $postVal){
					$stmt->bindParam(':'.$postName, $_POST[$postName], PDO::PARAM_STR);
				}

				if($stmt->execute()){
					$success = 'Veteran Edited';
				}else{
					$error = 'Sorry, an error has occured.<br/>';
					foreach($stmt->errorInfo() as $execError){
						$error .= $execError.'<br/>';
					}
				}
			}else{
				$error = 'Invalid Date of Birth.';
			}
		}else{
			$error = 'Cannot have blank First or Last Names.';
		}
	}

	if(isset($_GET['action'])&&$_GET['action'] == 'del'){
		$sql = "DELETE FROM `veterans` WHERE `ServiceRecordID` =  :ServiceRecordID";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':ServiceRecordID', urldecode($_GET['id']), PDO::PARAM_INT);   
		if($stmt->execute()){
			header('Location: veterans.php?success='.urlencode('Veteran Deleted'));
		}else{
			$error = 'Sorry, an error has occured.<br/>';
			foreach($stmt->errorInfo() as $execError){
				$error .= $execError.'<br/>';
			}
		}
	}

	if(isset($_GET['success'])){
		$success = urldecode($_GET['success']);
	}

	$query = "SELECT * FROM `veterans` WHERE `ServiceRecordID` = ".urldecode($_GET['id']);
	$result = $db->query($query);
	$num_rows = $result->rowCount();
	$vet = $result->fetch(PDO::FETCH_ASSOC);
?>
<html>
	<head>
		<title>Editing Veteran</title>

		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<script type="text/javascript">
			function confDel(id){
				var r = confirm("Are you sure you want to delete this Veteran?");
				if (r == true) {
				    window.location.assign("<?php echo basename($_SERVER['PHP_SELF']) . "?" . $_SERVER['QUERY_STRING']; ?>&action=del")
				}
			}
			function changeWhere(state){
				if(state == 'enabled'){
					document.getElementById('where').disabled=false;
				}else{
					document.getElementById('where').disabled=true;
				}
				
			}
		</script>
		<div id="nav">
			<ul>
				<li><a href="index.php">Customers</a></li>
				<li><a href="veterans.php">Veterans</a></li>
				<li><a href="provided.php">Provided</a></li>
			</ul>
		</div>
		<div id="content">
		<?php if($num_rows==1){ ?>
			<h1>Editing "<?php echo $vet['FirstName']." ".$vet['LastName'];?>"</h1>
			<br/><a href="#" onclick="confDel(<?php echo urldecode($_GET['id']); ?>)">- Delete this Veteran</a><br/><br/><br/>
			<?php if(isset($error)){echo '<div class="error">'.$error.'</div>';} ?>
			<?php if(isset($success)){echo '<div class="success">'.$success.'</div>';} ?>
			<strong>Service Record ID: <?php echo $vet['ServiceRecordID']; ?></strong><br/><br/>
			<form method="post" class="forms">
				<table class="form">
					<tr>
						<td>First Name</td>
						<td><input type="text" name="FirstName" value="<?php if(isset($vet['FirstName'])){echo $vet['FirstName'];} ?>" /></td>
					</tr>
					<tr>
						<td>Last Name</td>
						<td><input type="text" name="LastName" value="<?php if(isset($vet['LastName'])){echo $vet['LastName'];} ?>" /></td>
					</tr>
					<tr>
						<td>DoB</td>
						<td><input type="text" name="date_of_birth" placeholder="02/16/1974" value="<?php if(isset($vet['date_of_birth'])){echo $vet['date_of_birth'];} ?>" /></td>
					</tr>
					<tr>
						<td>Branch</td>
						<td><input type="text" name="Branch_Of_Service" value="<?php if(isset($vet['Branch_Of_Service'])){echo $vet['Branch_Of_Service'];} ?>" /></td>
					</tr>
					<tr>
						<td>Deployed Combat</td>
						<td><input type="radio" id="dep_yes" name="Deployed_Combat" onclick="changeWhere('enabled');" value="1" <?php if(isset($vet['Deployed_Combat'])&&$vet['Deployed_Combat']==1){echo 'checked';} ?> /><label for="dep_yes">Yes</label>&nbsp;&nbsp;&nbsp;<input type="radio" id="dep_no" name="Deployed_Combat" onclick="changeWhere('disabled');" value="0" <?php if(isset($vet['Deployed_Combat'])&&$vet['Deployed_Combat']==0){echo 'checked';} ?> /><label for="dep_no">No</label></td>
					</tr>
					<tr>
						<td>Where</td>
						<td><input type="text" id="where" name="Where" <?php echo ($vet['Deployed_Combat'])? '' : 'disabled'; ?> value="<?php if(isset($vet['Where'])){echo $vet['Where'];} ?>" /></td>
					</tr>
					<tr>
						<td>Current Status</td>
						<td>
							<select name="Current_Military_Status">
								<option value="Active Duty"<?php if(isset($vet['Current_Military_Status'])&&$vet['Current_Military_Status'] == 'Active Duty'){echo ' selected';} ?>>Active Duty</option>
								<option value="National Guard"<?php if(isset($vet['Current_Military_Status'])&&$vet['Current_Military_Status'] == 'National Guard'){echo ' selected';} ?>>National Guard</option>
								<option value="Reserve"<?php if(isset($vet['Current_Military_Status'])&&$vet['Current_Military_Status'] == 'Reserve'){echo ' selected';} ?>>Reserve</option>
								<option value="Retired"<?php if(isset($vet['Current_Military_Status'])&&$vet['Current_Military_Status'] == 'Retired'){echo ' selected';} ?>>Retired</option>
								<option value="Discharged"<?php if(isset($vet['Current_Military_Status'])&&$vet['Current_Military_Status'] == 'Discharged'){echo ' selected';} ?>>Discharged</option>
								<option value="KIA/MIA"<?php if(isset($vet['Current_Military_Status'])&&$vet['Current_Military_Status'] == 'KIA/MIA'){echo ' selected';} ?>>KIA/MIA</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Years of Service</td>
						<td><input type="text" name="Years_Of_Service" value="<?php if(isset($vet['Years_Of_Service'])){echo $vet['Years_Of_Service'];} ?>" /></td>
					</tr>
					<tr>
						<td>Discharge</td>
						<td>
							<select name="Discharge">
								<option value="None"<?php if(isset($vet['Discharge'])&&$vet['Discharge'] == 'None'){echo ' selected';} ?>>None</option>
								<option value="Honorable"<?php if(isset($vet['Discharge'])&&$vet['Discharge'] == 'Honorable'){echo ' selected';} ?>>Honorable</option>
								<option value="Other than Honorable"<?php if(isset($vet['Discharge'])&&$vet['Discharge'] == 'Other than Honorable'){echo ' selected';} ?>>Other than Honorable</option>
								<option value="Dishonorable"<?php if(isset($vet['Discharge'])&&$vet['Discharge'] == 'Dishonorable'){echo ' selected';} ?>>Dishonorable</option>
								<option value="General"<?php if(isset($vet['Discharge'])&&$vet['Discharge'] == 'General'){echo ' selected';} ?>>General</option>
								<option value="Bad Conduct"<?php if(isset($vet['Discharge'])&&$vet['Discharge'] == 'Bad Conduct'){echo ' selected';} ?>>Bad Conduct</option>
								<option value="Medical"<?php if(isset($vet['Discharge'])&&$vet['Discharge'] == 'Medical'){echo ' selected';} ?>>Medical</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Veteran Status Checked</td>
						<td><input type="text" name="Veteran_Status_Checked" value="<?php if(isset($vet['Veteran_Status_Checked'])){echo $vet['Veteran_Status_Checked'];} ?>" /></td>
					</tr>
					<tr>
						<td>SS Last Four</td>
						<td><input type="text" name="SS_Last_Four" value="<?php if(isset($vet['SS_Last_Four'])){echo $vet['SS_Last_Four'];} ?>" maxlength="4" /></td>
					</tr>
				</table>
				<input type="submit" name="editvet" value="Save Veteran Data" />
			</form>

			<h2>Customers under this Veteran [ <a href="new-customer.php?serv_id=<?php echo urlencode($vet['ServiceRecordID']); ?>">Add Customer</a> ]</h2>
			<?php
			$query = "SELECT * FROM `customers` WHERE `Service_Record_ID` = ".urldecode($_GET['id']);
			$result = $db->query($query);
			$num_rows_cust = $result->rowCount();

			if($num_rows_cust!=0){?>
			<ol>
				<?php while($cust = $result->fetch(PDO::FETCH_ASSOC)) {?>
				<li><a href="edit-customer.php?id=<?php echo $cust['CustomerID'];?>"><?php echo $cust['FirstName'].' '.$cust['LastName'];?></a></li>
				<?php } ?>
			</ol>
			<?php
			}else{
				echo '<p>[No customers under this Veteran]</p>';
			}
			?>
			<?php }else{ ?>
			<h1>Veteran by this Service ID does not exist.</h1>
			<?php } ?>
		</div>
	</body>
</html>