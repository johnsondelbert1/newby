<?php require_once('includes/dbconnect.php'); ?>
<?php

	if(isset($_POST['addcust'])){

		if($_POST['Service_Record_ID']!='' && $_POST['FirstName']!='' && $_POST['LastName']!=''){

			//check if Service ID exists

			$query = "SELECT * FROM `veterans` WHERE `ServiceRecordID` = ".$_POST['Service_Record_ID'];

			$result = $db->query($query);

			if($result->rowCount()>=1){

				$_POST['date_created'] = date('Y-m-d H:i:s');

				$postData = $_POST;

				unset($postData['addcust']);

				$postCount = count($postData);

				$i=0;



				$sql = "INSERT INTO customers(";



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

					header('Location: edit-customer.php?id='.$db->lastInsertId().'&success='.urlencode('Customer Added'));

				}else{

					$error = 'Sorry, an error has occured.<br/>';

					foreach($stmt->errorInfo() as $execError){

						$error .= $execError.'<br/>';

					}

				}

			}else{

				$error = 'Service Record ID does not exist.';

			}

		}else{

			$error = 'Cannot have blank Service Record ID, First Name, or Last Name.';

		}

	}

?>

<html>

	<head>

		<title>New Customer</title>



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

			<h1>Adding New Customer</h1>

			<?php if(isset($error)){echo '<div class="error">'.$error.'</div>';} ?>

			<?php if(isset($success)){echo '<div class="success">'.$success.'</div>';} ?>

			<form method="post" class="forms">

				<table class="form" class="forms">

					<tr>

						<td>Eligible</td>

						<td><input type="radio" id="elig_yes" name="Eligible" value="1" <?php if(isset($_POST['Eligible'])&&$_POST['Eligible']==1){echo 'checked';} ?> /><label for="elig_yes">Yes</label>&nbsp;&nbsp;&nbsp;<input type="radio" id="elig_no" name="Eligible" value="0" <?php if(isset($_POST['Eligible'])&&$_POST['Eligible']==0){echo 'checked';} ?> /><label for="elig_no">No</label></td>

					</tr>

					<tr>

						<td>Service Record ID</td>

						<td><input type="text" name="Service_Record_ID" value="<?php if(isset($_POST['Service_Record_ID'])){echo $_POST['Service_Record_ID'];}elseif(isset($_GET['serv_id'])){echo urldecode($_GET['serv_id']);} ?>" /></td>

					</tr>

					<tr>

						<td>First Name</td>

						<td><input type="text" name="FirstName" value="<?php if(isset($_POST['FirstName'])){echo $_POST['FirstName'];} ?>" /></td>

					</tr>

					<tr>

						<td>Last Name</td>

						<td><input type="text" name="LastName" value="<?php if(isset($_POST['LastName'])){echo $_POST['LastName'];} ?>" /></td>

					</tr>

					<tr>

						<td>Mailing Address</td>

						<td><input type="text" name="Mailing_address" value="<?php if(isset($_POST['Mailing_address'])){echo $_POST['Mailing_address'];} ?>" /></td>

					</tr>

					<tr>

						<td>City</td>

						<td><input type="text" name="City" value="<?php if(isset($_POST['City'])){echo $_POST['City'];} ?>" /></td>

					</tr>

					<tr>

						<td>State or Province</td>

						<td><input type="text" name="State_or_Province" value="<?php if(isset($_POST['State_or_Province'])){echo $_POST['State_or_Province'];} ?>" /></td>

					</tr>

					<tr>

						<td>Zip Code</td>

						<td><input type="text" name="ZipCode" value="<?php if(isset($_POST['ZipCode'])){echo $_POST['ZipCode'];} ?>" /></td>

					</tr>

					<tr>

						<td>Phone Number</td>

						<td><input type="text" name="Phone_Number" value="<?php if(isset($_POST['Phone_Number'])){echo $_POST['Phone_Number'];} ?>" /></td>

					</tr>

					<tr>

						<td>Alternate number</td>

						<td><input type="text" name="Alternate_number" value="<?php if(isset($_POST['Alternate_number'])){echo $_POST['Alternate_number'];} ?>" /></td>

					</tr>

					<tr>

						<td>Email Address</td>

						<td><input type="text" name="Email_Address" value="<?php if(isset($_POST['Email_Address'])){echo $_POST['Email_Address'];} ?>" /></td>

					</tr>

					<tr>

						<td>Partner Name</td>

						<td><input type="text" name="PartnerName" value="<?php if(isset($_POST['PartnerName'])){echo $_POST['PartnerName'];} ?>" /></td>

					</tr>

					<tr>

						<td>DoB</td>

						<td><input type="text" name="Birthday" value="<?php if(isset($_POST['Birthday'])){echo $_POST['Birthday'];} ?>" /></td>

					</tr>

					<tr>

						<td>Marriage Status</td>

						<td>

							<select name="Marriage_Status">

								<option value="Married"<?php if(isset($_POST['Marriage_Status'])&&$_POST['Marriage_Status'] == 'Married'){echo ' selected';} ?>>Married</option>

								<option value="Single"<?php if(isset($_POST['Marriage_Status'])&&$_POST['Marriage_Status'] == 'Single'){echo ' selected';} ?>>Single</option>

								<option value="Engaged"<?php if(isset($_POST['Marriage_Status'])&&$_POST['Marriage_Status'] == 'Engaged'){echo ' selected';} ?>>Engaged</option>

								<option value="Living Together"<?php if(isset($_POST['Marriage_Status'])&&$_POST['Marriage_Status'] == 'Living Together'){echo ' selected';} ?>>Living Together</option>

								<option value="Widowed"<?php if(isset($_POST['Marriage_Status'])&&$_POST['Marriage_Status'] == 'Widowed'){echo ' selected';} ?>>Widowed</option>

								<option value="Other"<?php if(isset($_POST['Marriage_Status'])&&$_POST['Marriage_Status'] == 'MarOtherried'){echo ' selected';} ?>>Other</option>

							</select>

						</td>

					</tr>

					<tr>

						<td>Kids</td>

						<td><input type="radio" id="kids_yes" name="Kids" value="1" <?php if(isset($_POST['Kids'])&&$_POST['Kids']==1){echo 'checked';} ?> /><label for="kids_yes">Yes</label>&nbsp;&nbsp;&nbsp;<input type="radio" id="kids_no" name="Kids" value="0" <?php if(isset($_POST['Kids'])&&$_POST['Kids']==0){echo 'checked';} ?> /><label for="kids_no">No</label></td>

					</tr>

					<tr>

						<td>How Many Kids</td>

						<td><input type="text" name="Kids" value="<?php if(isset($_POST['Kids'])){echo $_POST['Kids'];} ?>" /></td>

					</tr>

					<tr>

						<td>Child One Age</td>

						<td><input type="text" name="Child_Age_one" value="<?php if(isset($_POST['Child_Age_one'])){echo $_POST['Child_Age_one'];} ?>" /></td>

					</tr>

					<tr>

						<td>Child Two Age</td>

						<td><input type="text" name="Child_Age_two" value="<?php if(isset($_POST['Child_Age_two'])){echo $_POST['Child_Age_two'];} ?>" /></td>

					</tr>

					<tr>

						<td>Child Three Age</td>

						<td><input type="text" name="Child_Age_three" value="<?php if(isset($_POST['Child_Age_three'])){echo $_POST['Child_Age_three'];} ?>" /></td>

					</tr>

					<tr>

						<td>Child Four Age</td>

						<td><input type="text" name="Child_Age_four" value="<?php if(isset($_POST['Child_Age_four'])){echo $_POST['Child_Age_four'];} ?>" /></td>

					</tr>

					<tr>

						<td>Child Five Age</td>

						<td><input type="text" name="Child_Age_five" value="<?php if(isset($_POST['Child_Age_five'])){echo $_POST['Child_Age_five'];} ?>" /></td>

					</tr>

					<tr>

						<td>Child Six Age</td>

						<td><input type="text" name="Child_Age_six" value="<?php if(isset($_POST['Child_Age_six'])){echo $_POST['Child_Age_six'];} ?>" /></td>

					</tr>

					<tr>

						<td>How Did You Hear</td>

						<td><input type="text" name="How_Did_You_Hear" value="<?php if(isset($_POST['How_Did_You_Hear'])){echo $_POST['How_Did_You_Hear'];} ?>" /></td>

					</tr>

					<tr>

						<td>Help Seeking</td>

						<td><input type="text" name="Help_Seeking" value="<?php if(isset($_POST['Help_Seeking'])){echo $_POST['Help_Seeking'];} ?>" /></td>

					</tr>

					<tr>

						<td>How Related Vet</td>

						<td><input type="text" name="How_Related_Vet" value="<?php if(isset($_POST['How_Related_Vet'])){echo $_POST['How_Related_Vet'];} ?>" /></td>

					</tr>

					<tr>

						<td>Notes</td>

						<td><textarea name="Notes" rows="5" cols="50"><?php if(isset($_POST['Notes'])){echo $_POST['Notes'];} ?></textarea></td>

					</tr>

				</table>

				<input type="submit" name="addcust" value="Add New Customer" />

			</form>

		</div>

	</body>

</html>