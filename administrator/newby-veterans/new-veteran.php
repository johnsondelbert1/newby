<?php require_once('includes/dbconnect.php'); ?>
<?php
	if(isset($_POST['addvet'])){
		if($_POST['FirstName']!=''||$_POST['LastName']!=''){
			if(strtotime($_POST['date_of_birth']) !== false){
				$dob = date('Y-m-d', strtotime($_POST['date_of_birth']));
				$postData = $_POST;
				unset($postData['addvet']);
				$postData['date_of_birth'] = $dob;
				$_POST['date_of_birth'] = $dob;
				$postCount = count($postData);
				$i=0;

				$sql = "INSERT INTO veterans(";

				foreach($postData as $postName => $postVal){
					$i++;
					$sql .="`".$postName."`";
					if($i!=$postCount){
						$sql.=', ';
					}
				}
				$sql .=") VALUES (";
				$i=0;
				foreach($postData as $postName => $postVal){
					$i++;
					$sql .=":".$postName;
					if($i!=$postCount){
						$sql.=', ';
					}
				}
				$sql .=")";

				$stmt = $db->prepare($sql);
				
				foreach($postData as $postName => $postVal){
					$stmt->bindParam(':'.$postName, $_POST[$postName], PDO::PARAM_STR);
				}
				if($stmt->execute()){
					header('Location: edit-veteran.php?id='.$db->lastInsertId().'&success='.urlencode('Veteran Added'));
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
?>
<html>
	<head>
		<title>New Veteran</title>

		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<script type="text/javascript">
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
			<h1>Adding New Veteran</h1>
			<?php if(isset($error)){echo '<div class="error">'.$error.'</div>';} ?>
			<?php if(isset($success)){echo '<div class="success">'.$success.'</div>';} ?>
			<form method="post" class="forms">
				<table class="form">
					<tr>
						<td>First Name</td>
						<td><input type="text" name="FirstName" value="<?php if(isset($_POST['FirstName'])){echo $_POST['FirstName'];} ?>" /></td>
					</tr>
					<tr>
						<td>Last Name</td>
						<td><input type="text" name="LastName" value="<?php if(isset($_POST['LastName'])){echo $_POST['LastName'];} ?>" /></td>
					</tr>
					<tr>
						<td>DoB</td>
						<td><input type="text" name="date_of_birth" placeholder="02/16/1974" value="<?php if(isset($_POST['date_of_birth'])){echo $_POST['date_of_birth'];} ?>" /></td>
					</tr>
					<tr>
						<td>Branch</td>
						<td><input type="text" name="Branch_Of_Service" value="<?php if(isset($_POST['Branch_Of_Service'])){echo $_POST['Branch_Of_Service'];} ?>" /></td>
					</tr>
					<tr>
						<td>Deployed Combat</td>
						<td><input type="radio" id="dep_yes" name="Deployed_Combat" onclick="changeWhere('enabled');" value="1" <?php if(isset($_POST['Deployed_Combat'])&&$_POST['Deployed_Combat']==1){echo 'checked';} ?> /><label for="dep_yes">Yes</label>&nbsp;&nbsp;&nbsp;<input type="radio" id="dep_no" name="Deployed_Combat" onclick="changeWhere('disabled');" value="0" <?php if(isset($_POST['Deployed_Combat'])&&$_POST['Deployed_Combat']==0){echo 'checked';} ?> /><label for="dep_no">No</label></td>
					</tr>
					<tr>
						<td>Where</td>
						<td><input type="text" id="where" name="Where" value="<?php if(isset($_POST['Where'])){echo $_POST['Where'];} ?>" /></td>
					</tr>
					<tr>
						<td>Current Status</td>
						<td>
							<select name="Current_Military_Status">
								<option value="Active Duty"<?php if(isset($_POST['Current_Military_Status'])&&$_POST['Current_Military_Status'] == 'Active Duty'){echo ' selected';} ?>>Active Duty</option>
								<option value="National Guard"<?php if(isset($_POST['Current_Military_Status'])&&$_POST['Current_Military_Status'] == 'National Guard'){echo ' selected';} ?>>National Guard</option>
								<option value="Reserve"<?php if(isset($_POST['Current_Military_Status'])&&$_POST['Current_Military_Status'] == 'Reserve'){echo ' selected';} ?>>Reserve</option>
								<option value="Retired"<?php if(isset($_POST['Current_Military_Status'])&&$_POST['Current_Military_Status'] == 'Retired'){echo ' selected';} ?>>Retired</option>
								<option value="Discharged"<?php if(isset($_POST['Current_Military_Status'])&&$_POST['Current_Military_Status'] == 'Discharged'){echo ' selected';} ?>>Discharged</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Years of Service</td>
						<td><input type="text" name="Years_Of_Service" value="<?php if(isset($_POST['Years_Of_Service'])){echo $_POST['Years_Of_Service'];} ?>" /></td>
					</tr>
					<tr>
						<td>Discharged</td>
						<td>
							<select name="Discharge">
								<option value="None"<?php if(isset($_POST['Discharge'])&&$_POST['Discharge'] == 'None'){echo ' selected';} ?>>None</option>
								<option value="Honorable"<?php if(isset($_POST['Discharge'])&&$_POST['Discharge'] == 'Honorable'){echo ' selected';} ?>>Honorable</option>
								<option value="Other than Honorable"<?php if(isset($_POST['Discharge'])&&$_POST['Discharge'] == 'Other than Honorable'){echo ' selected';} ?>>Other than Honorable</option>
								<option value="Dishonorable"<?php if(isset($_POST['Discharge'])&&$_POST['Discharge'] == 'Dishonorable'){echo ' selected';} ?>>Dishonorable</option>
								<option value="General"<?php if(isset($_POST['Discharge'])&&$_POST['Discharge'] == 'General'){echo ' selected';} ?>>General</option>
								<option value="Bad Conduct"<?php if(isset($_POST['Discharge'])&&$_POST['Discharge'] == 'Bad Conduct'){echo ' selected';} ?>>Bad Conduct</option>
								<option value="Medical"<?php if(isset($_POST['Discharge'])&&$_POST['Discharge'] == 'Medical'){echo ' selected';} ?>>Medical</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Veteran Status Checked</td>
						<td><input type="text" name="Veteran_Status_Checked" value="<?php if(isset($_POST['Veteran_Status_Checked'])){echo $_POST['Veteran_Status_Checked'];} ?>" /></td>
					</tr>
					<tr>
						<td>SS Last Four</td>
						<td><input type="text" name="SS_Last_Four" value="<?php if(isset($_POST['SS_Last_Four'])){echo $_POST['SS_Last_Four'];} ?>" maxlength="4" /></td>
					</tr>
				</table>
				<input type="submit" name="addvet" value="Add New Veteran" />
			</form>
		</div>
	</body>
</html>