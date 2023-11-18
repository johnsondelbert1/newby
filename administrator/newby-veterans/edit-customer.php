<?php require_once('includes/dbconnect.php'); ?>
<?php

	if(isset($_POST['addcust'])){

		if($_POST['Service_Record_ID']!='' && $_POST['FirstName']!='' && $_POST['LastName']!=''){

			//check if Service ID exists

			$query = "SELECT * FROM `veterans` WHERE `ServiceRecordID` = ".$_POST['Service_Record_ID'];

			$result = $db->query($query);

			if($result->rowCount()>=1){

				$postData = $_POST;

				unset($postData['addcust']);

				$postCount = count($postData);

				$i=0;



				$sql = "UPDATE `customers` SET ";



				foreach($postData as $postName => $postVal){

					$i++;

					$sql .="`".$postName."` = :".$postName;

					if($i!=$postCount){

						$sql.=', ';

					}

				}

				$sql .=" WHERE `CustomerID` = ".urldecode($_GET['id']);



				$stmt = $db->prepare($sql);

				

				foreach($postData as $postName => $postVal){

					$stmt->bindParam(':'.$postName, $_POST[$postName], PDO::PARAM_STR);

				}

				if($stmt->execute()){

					$success = 'Customer Saved';

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



	if(isset($_POST['addprov'])){

		if($_GET['id']!=''){

			//check if Cust ID exists

			$id = intval(urldecode($_GET['id']));

			$query = "SELECT * FROM `customers` WHERE `CustomerID` = ".$id;

			$result = $db->query($query);

			if($result->rowCount()>=1){



				$provided = trim($_POST['Provided']);

				if($provided!=''){

					$date = date("Y-m-d H:i:s", time());



					$sql = "INSERT INTO `provided` (`CustomerID`, `Date`, `Provided`) VALUES (:CustomerID, :Date, :Provided)";



					$stmt = $db->prepare($sql);

					

					$stmt->bindParam(':CustomerID', $id, PDO::PARAM_INT);

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



	if(isset($_GET['action'])&&$_GET['action'] == 'del'){

		$sql = "DELETE FROM `customers` WHERE `CustomerID` =  :CustomerID";

		$stmt = $db->prepare($sql);

		$stmt->bindParam(':CustomerID', urldecode($_GET['id']), PDO::PARAM_INT);   

		if($stmt->execute()){

			header('Location: index.php?success='.urlencode('Customer Deleted'));

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



	$query = "SELECT * FROM `customers` WHERE `CustomerID` = ".urldecode($_GET['id']);

	$result = $db->query($query);

	$num_rows = $result->rowCount();

	$cust = $result->fetch(PDO::FETCH_ASSOC);

?>

<html>

	<head>

		<title>Edit Customer</title>

		<link rel="stylesheet" type="text/css" href="css/style.css">

	</head>

	<body>

		<script type="text/javascript">

			function confDel(id){

				var r = confirm("Are you sure you want to delete this Customer?");

				if (r == true) {

				    window.location.assign("<?php echo basename($_SERVER['PHP_SELF']) . "?" . $_SERVER['QUERY_STRING']; ?>&action=del")

				}

			}

		</script>

		<div id="nav" <?php if($cust['flagged'] == 1){echo 'style="background-color:#8F0000;"';} ?>>

			<ul>

				<li><a href="index.php">Customers</a></li>

				<li><a href="veterans.php">Veterans</a></li>

				<li><a href="provided.php">Provided</a></li>

			</ul>

		</div>

		<div id="content">

		<?php if($num_rows==1){ ?>

			<h1>Editing "<?php echo $cust['FirstName']." ".$cust['LastName'];?>"</h1>

			<br/><a href="#" onclick="confDel(<?php echo urldecode($_GET['id']); ?>)">- Delete this Customer</a><br/><br/><br/>

			<?php if(isset($error)){echo '<div class="error">'.$error.'</div>';} ?>

			<?php if(isset($success)){echo '<div class="success">'.$success.'</div>';} ?>

			<strong>Created: <?php if($cust['date_created'] != '0000-00-00 00:00:00'){echo date('m/d/Y', strtotime($cust['date_created']));}else{echo 'N/A';} ?></strong><br/><br/>

			<form method="post" class="forms">

				<table class="form">

					<tr>

						<td>Eligible</td>

						<td><input type="radio" id="elig_yes" name="Eligible" value="1" <?php if(isset($cust['Eligible'])&&$cust['Eligible']==1){echo 'checked';} ?> /><label for="elig_yes">Yes</label>&nbsp;&nbsp;&nbsp;<input type="radio" id="elig_no" name="Eligible" value="0" <?php if(isset($cust['Eligible'])&&$cust['Eligible']==0){echo 'checked';} ?> /><label for="elig_no">No</label></td>

					</tr>

					<tr>

						<td>Service Record ID</td>

						<td><input type="text" name="Service_Record_ID" value="<?php if(isset($cust['Service_Record_ID'])){echo $cust['Service_Record_ID'];}elseif(isset($_GET['serv_id'])){echo urldecode($_GET['serv_id']);} ?>" /></td>

					</tr>

					<tr>

						<td>First Name</td>

						<td><input type="text" name="FirstName" value="<?php if(isset($cust['FirstName'])){echo $cust['FirstName'];} ?>" /></td>

					</tr>

					<tr>

						<td>Last Name</td>

						<td><input type="text" name="LastName" value="<?php if(isset($cust['LastName'])){echo $cust['LastName'];} ?>" /></td>

					</tr>

					<tr>

						<td>Mailing Address</td>

						<td><input type="text" name="Mailing_address" value="<?php if(isset($cust['Mailing_address'])){echo $cust['Mailing_address'];} ?>" /></td>

					</tr>

					<tr>

						<td>City</td>

						<td><input type="text" name="City" value="<?php if(isset($cust['City'])){echo $cust['City'];} ?>" /></td>

					</tr>

					<tr>

						<td>State or Province</td>

						<td><input type="text" name="State_or_Province" value="<?php if(isset($cust['State_or_Province'])){echo $cust['State_or_Province'];} ?>" /></td>

					</tr>

					<tr>

						<td>Zip Code</td>

						<td><input type="text" name="ZipCode" value="<?php if(isset($cust['ZipCode'])){echo $cust['ZipCode'];} ?>" /></td>

					</tr>

					<tr>

						<td>Phone Number</td>

						<td><input type="text" name="Phone_Number" value="<?php if(isset($cust['Phone_Number'])){echo $cust['Phone_Number'];} ?>" /></td>

					</tr>

					<tr>

						<td>Alternate number</td>

						<td><input type="text" name="Alternate_number" value="<?php if(isset($cust['Alternate_number'])){echo $cust['Alternate_number'];} ?>" /></td>

					</tr>

					<tr>

						<td>Email Address</td>

						<td><input type="text" name="Email_Address" value="<?php if(isset($cust['Email_Address'])){echo $cust['Email_Address'];} ?>" /></td>

					</tr>

					<tr>

						<td>Partner Name</td>

						<td><input type="text" name="PartnerName" value="<?php if(isset($cust['PartnerName'])){echo $cust['PartnerName'];} ?>" /></td>

					</tr>

					<tr>

						<td>DoB</td>

						<td><input type="text" name="Birthday" value="<?php if(isset($cust['Birthday'])){echo $cust['Birthday'];} ?>" /></td>

					</tr>

					<tr>

						<td>Marriage Status</td>

						<td>

							<select name="Marriage_Status">

								<option value="Married"<?php if(isset($cust['Marriage_Status'])&&$cust['Marriage_Status'] == 'Married'){echo ' selected';} ?>>Married</option>

								<option value="Single"<?php if(isset($cust['Marriage_Status'])&&$cust['Marriage_Status'] == 'Single'){echo ' selected';} ?>>Single</option>

								<option value="Engaged"<?php if(isset($cust['Marriage_Status'])&&$cust['Marriage_Status'] == 'Engaged'){echo ' selected';} ?>>Engaged</option>

								<option value="Living Together"<?php if(isset($cust['Marriage_Status'])&&$cust['Marriage_Status'] == 'Living Together'){echo ' selected';} ?>>Living Together</option>

								<option value="Widowed"<?php if(isset($cust['Marriage_Status'])&&$cust['Marriage_Status'] == 'Widowed'){echo ' selected';} ?>>Widowed</option>

								<option value="Divorced"<?php if(isset($cust['Marriage_Status'])&&$cust['Marriage_Status'] == 'Divorced'){echo ' selected';} ?>>Divorced</option>

								<option value="Other"<?php if(isset($cust['Marriage_Status'])&&$cust['Marriage_Status'] == 'Other'){echo ' selected';} ?>>Other</option>

							</select>

						</td>

					</tr>

					<tr>

						<td>Kids</td>

						<td><input type="radio" id="kids_yes" name="Kids" value="1" <?php if(isset($cust['Kids'])&&$cust['Kids']==1){echo 'checked';} ?> /><label for="kids_yes">Yes</label>&nbsp;&nbsp;&nbsp;<input type="radio" id="kids_no" name="Kids" value="0" <?php if(isset($cust['Kids'])&&$cust['Kids']==0){echo 'checked';} ?> /><label for="kids_no">No</label></td>

					</tr>

					<tr>

						<td>How Many Kids</td>

						<td><input type="text" name="Kids" value="<?php if(isset($cust['Kids'])){echo $cust['Kids'];} ?>" /></td>

					</tr>

					<tr>

						<td>Child One Age</td>

						<td><input type="text" name="Child_Age_one" value="<?php if(isset($cust['Child_Age_One'])){echo $cust['Child_Age_One'];} ?>" /></td>

					</tr>

					<tr>

						<td>Child Two Age</td>

						<td><input type="text" name="Child_Age_two" value="<?php if(isset($cust['Child_Age_Two'])){echo $cust['Child_Age_Two'];} ?>" /></td>

					</tr>

					<tr>

						<td>Child Three Age</td>

						<td><input type="text" name="Child_Age_three" value="<?php if(isset($cust['Child_Age_Three'])){echo $cust['Child_Age_Three'];} ?>" /></td>

					</tr>

					<tr>

						<td>Child Four Age</td>

						<td><input type="text" name="Child_Age_four" value="<?php if(isset($cust['Child_Age_Four'])){echo $cust['Child_Age_Four'];} ?>" /></td>

					</tr>

					<tr>

						<td>Child Five Age</td>

						<td><input type="text" name="Child_Age_five" value="<?php if(isset($cust['Child_Age_Five'])){echo $cust['Child_Age_Five'];} ?>" /></td>

					</tr>

					<tr>

						<td>Child Six Age</td>

						<td><input type="text" name="Child_Age_six" value="<?php if(isset($cust['Child_Age_Six'])){echo $cust['Child_Age_Six'];} ?>" /></td>

					</tr>

					<tr>

						<td>How Did You Hear</td>

						<td>

							<select name="How_Did_You_Hear">

								<option value="Facebook"<?php if(isset($cust['How_Did_You_Hear'])&&$cust['How_Did_You_Hear'] == 'Facebook'){echo ' selected';} ?>>Facebook</option>

								<option value="Television"<?php if(isset($cust['How_Did_You_Hear'])&&$cust['How_Did_You_Hear'] == 'Television'){echo ' selected';} ?>>Television</option>

								<option value="St Vinny"<?php if(isset($cust['How_Did_You_Hear'])&&$cust['How_Did_You_Hear'] == 'St Vinny'){echo ' selected';} ?>>St Vinny</option>

								<option value="SSVF"<?php if(isset($cust['How_Did_You_Hear'])&&$cust['How_Did_You_Hear'] == 'SSVF'){echo ' selected';} ?>>SSVF</option>

								<option value="Friend"<?php if(isset($cust['How_Did_You_Hear'])&&$cust['How_Did_You_Hear'] == 'Friend'){echo ' selected';} ?>>Friend</option>

								<option value="Other"<?php if(isset($cust['How_Did_You_Hear'])&&$cust['How_Did_You_Hear'] == 'Other'){echo ' selected';} ?>>Other</option>

							</select>

						</td>

					</tr>

					<tr>

						<td>Help Seeking</td>

						<td><input type="text" name="Help_Seeking" value="<?php if(isset($cust['Help_Seeking'])){echo $cust['Help_Seeking'];} ?>" /></td>

					</tr>

					<tr>

						<td>How Related Vet</td>

						<td><input type="text" name="How_Related_Vet" value="<?php if(isset($cust['How_Related_Vet'])){echo $cust['How_Related_Vet'];} ?>" /></td>

					</tr>

					<tr>

						<td>Notes</td>

						<td><textarea name="Notes" rows="5" cols="50"><?php if(isset($cust['Notes'])){echo $cust['Notes'];} ?></textarea></td>

					</tr>

					<tr>

						<td>Flagged</td>

						<td><input type="radio" id="flagged_yes" name="flagged" value="1" <?php if(isset($cust['flagged'])&&$cust['flagged']==1){echo 'checked';} ?> /><label for="flagged_yes">Yes</label>&nbsp;&nbsp;&nbsp;<input type="radio" id="flagged_no" name="flagged" value="0" <?php if(isset($cust['flagged'])&&$cust['flagged']==0){echo 'checked';} ?> /><label for="flagged_no">No</label></td>

					</tr>

				</table>

				<input type="submit" name="addcust" value="Save Customer Data" />

			</form>

			<h2>Add Provided Items</h2>

			<form method="post">

				<textarea name="Provided" rows="5" cols="50"><?php if(isset($_POST['Provided'])&&!isset($success)){echo $_POST['Provided'];} ?></textarea><br/><br/>

				<input type="submit" name="addprov" value="Add New Provided" />

			</form>

			<?php

			$query = "SELECT * FROM `provided` WHERE `CustomerID` = ".urldecode($_GET['id'])." ORDER BY `Date` ASC";

			$result = $db->query($query);

			$num_rows_prov = $result->rowCount();

			?>

			<h2>Provided Items under this Customer (<?php echo $num_rows_prov; ?>)</h2>

			<?php

			if($num_rows_prov!=0){?>

			<table class="result">

				<tr>

					<th>Date</th>

					<th>Items</th>

				</tr>

				<?php while($prov = $result->fetch(PDO::FETCH_ASSOC)) {?>

				<tr>

					<td><?php $dt = strtotime($prov['Date']);

	    				echo date('m/d/Y', $dt); ?></td>

					<td><?php echo $prov['Provided'];?></td>

				</tr>

				<?php } ?>

			</table>

			<?php

			}else{

				echo '<p>[No provided items under this Customer]</p>';

			}

			?>

			<?php if($cust['flagged'] == 1){echo '<img src="img/nuh_uh_uh.gif" style="position: fixed; bottom: 0px; right: 0px;" />';} ?>

			<?php }else{ ?>

			<h1>Customer by this Service ID does not exist.</h1>

			<?php } ?>

		</div>

	</body>

</html>